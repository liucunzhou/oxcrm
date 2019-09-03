<?php
namespace app\api\controller;

use app\common\model\User;
use Firebase\JWT\JWT;
use think\facade\Request;
use think\Log;

class Dingtalk extends Base
{
    public function getUserInfo()
    {
        $post = Request::param();
        $DingTalkModel = new \app\api\model\DingTalk();
        $info = $DingTalkModel->getUserInfo($post['code']);

        // \think\facade\Log::info("用户信息是:".json_encode($info));
        // \think\facade\Log::info("用户钉钉信息是:".json_encode($this->user));
        if(empty($this->user['dingding'])) {
            \think\facade\Log::info('用户ID是:'.$info['userid']);
            $map['id'] = $this->user['id'];
            $UserModel = new User();
            $UserModel->save(['dingding'=>$info['userid']], $map);
            $this->user['dingding'] = $info['userid'];
        }

        $token = JWT::encode($this->user, 'hongsi');
        return xjson([
            'code'  => '200',
            'msg'   => '获取token正常',
            'result' => [
                'user'  => $this->user,
                'token' => $token
            ]
        ]);
    }

    public function getAccessToken()
    {
        $DingTalkModel = new \app\api\model\DingTalk();
        echo $DingTalkModel->getAccessToken();
    }

    public function sendLinkMessage()
    {
        $users = ['27370821403254239'];
        $DingModel = new \app\api\model\DingTalk();
        $message = $DingModel->linkMessage("新增客资", "新增客资消息", "http://h5.hongsizg.com/pages/customer/today");
        $DingModel->sendJobMessage($users, $message);
    }
}