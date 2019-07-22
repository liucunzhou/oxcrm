<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/7/7
 * Time: 7:13 PM
 */

namespace app\index\controller;

use app\index\model\Hotel;
use app\index\model\MemberApply;
use app\index\model\UserAuth;
use think\facade\Request;
use think\facade\Session;

class Manager extends Base
{
    public function applyVisit()
    {
        $user = Session::get("user");
        $auth = UserAuth::getUserLogicAuth($user['id']);
        $roleIds = $auth['role_ids'];

        if(Request::isAjax()) {
            $sources = \app\index\model\Source::getSources();
            $hotels = Hotel::getHotels();
            $newsTypes = ['婚宴信息', '婚庆信息', '婚宴转婚庆'];

            $get = Request::param();
            $user = Session::get("user");
            $config = [
                'page' => $get['page']
            ];

            switch ($roleIds) {
                case 3: // 推荐组主管
                    $map[] = ['recommend_manager_id', '=', $user['id']];
                    // 分配到推荐组客服状态过滤
                    if(isset($get['assign_status'])){
                        $map[] = ['recommend_manager_assign_status', '=', $get['assign_status']];
                    }
                    break;
                case 4: // 推荐组客服
                    $map[] = ['recommend_staff_id', '=', $user['id']];
                    if(isset($get['assign_status'])){
                        $map[] = ['recommend_staff_assign_status', '=', $get['assign_status']];
                    }
                    break;
                case 10: // 派单组主管
                    $map[] = ['dispatch_manager_id', '=', $user['id']];
                    if(isset($get['assign_status'])){
                        $map[] = ['dispatch_manager_assign_status', '=', $get['assign_status']];
                    }
                    break;
                case 11: // 派单组客服
                    $map[] = ['dispatch_staff_id', '=', $user['id']];
                    if(isset($get['assign_status'])){
                        $map[] = ['dispatch_staff_assign_status', '=', $get['assign_status']];
                    }
                    break;

            }

            if($map) {
                $list = model('MemberApply')->where($map)->with('member')->paginate($get['limit'], false, $config);
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $member = $value['member'];
                    unset($member['id']);
                    unset($value['member']);
                    $value = array_merge($value, $member);
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $newsTypes[$value['news_type']];
                    $value['hotel_id'] = $hotels[$value['hotel_id']];
                }
                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => $list->total(),
                    'data' => $data
                ];
            } else {

                $result = [
                    'code' => 0,
                    'msg' => '获取数据成功',
                    'count' => 0,
                    'data' => []
                ];
            }
            return json($result);

        } else {
            return $this->fetch();
        }
    }

    // 确认回访申请
    public function confirmVisitApply()
    {
        $ids = Request::post("ids");
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return json([
                'code'  => '500',
                'msg'   => '要确认的回访申请不能为空'
            ]);
        }

        $map[] = ['id', 'in', $ids];
        $MemberApply = new MemberApply();
        $MemberApply->where($map)->save(['apply_status'=>1]);

        return json([
            'code'  => '200',
            'msg'   => '确认回访申请成功'
        ]);
    }

    // 驳回回访申请
    public function cancelVisitApply()
    {
        $ids = Request::post("ids");
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return json([
                'code'  => '500',
                'msg'   => '要驳回的回访申请不能为空'
            ]);
        }
        $map[] = ['id', 'in', $ids];
        $MemberApply = new MemberApply();
        $MemberApply->where($map)->save(['apply_status'=>2]);

        return json([
            'code'  => '200',
            'msg'   => '驳回回访申请成功'
        ]);
    }
}