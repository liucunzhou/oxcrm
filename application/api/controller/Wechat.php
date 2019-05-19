<?php
namespace app\api\controller;


use app\api\model\Member;
use think\Cache;
use think\Controller;

class Wechat extends Controller
{
    /**
     * 通过code换取用户的openid
     * {session_key: "kAaug4U8ngtLs9AaY8ScaQ==", openid: "ohyVG4ydMqOoPrLWXPPoMW93Ty6Y"}
     */
    public function login()
    {
        $code = input("code");
        $Wechat = new \app\api\model\Wechat();
        $loginRs = $Wechat->login($code);

        if (!empty($loginRs['openid'])) {
            $Member = new Member();
            $member = $Member->checkUserExist($loginRs['openid']);
            $result = [
                'code' => '200',
                'msg' => '获取openid成功',
                'result' => [
                    'openid' => $loginRs['openid'],
                    'member' => $member
                ]
            ];
        } else {
            $result = [
                'code' => '500',
                'msg' => '获取openid失败'
            ];
        }

        return json($result);
    }

    public function sycWechatToMember()
    {
        $post = input("post.");
        if (empty($post['openid'])) {
            return json([
                'code' => '400',
                'msg' => '同步用户信息失败'
            ]);
        }

        $data['nickname'] = $post['nickName'];
        $data['avatar'] = $post['avatarUrl'];
        $data['sex'] = $post['gender'];
        $data['has_syn'] = 1;
        $Member = new Member();
        $rs = $Member->save($data, ['openid' => $post['openid']]);

        if($rs) {
            $data['openid'] = $post['openid'];
            $result = [
                'code' => '200',
                'msg' => '数据同步成功',
                'data' => [
                    'member' => $data
                ]
            ];
        } else {
            $result = [
                'code' => '500',
                'msg' => '数据同步失败'
            ];
        }

        return json($result);
    }
}