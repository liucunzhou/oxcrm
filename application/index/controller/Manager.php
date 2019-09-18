<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/7/7
 * Time: 7:13 PM
 */

namespace app\index\controller;

use app\common\model\Allocate;
use app\common\model\Hotel;
use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\MemberApply;
use app\common\model\OrderApply;
use app\common\model\Source;
use app\common\model\Store;
use app\common\model\Tab;
use app\common\model\User;
use app\common\model\UserAuth;
use think\facade\Request;

class Manager extends Base
{
    protected $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];
    protected $washStatus = [];
    protected $hotels = [];
    protected $sources = [];
    protected $status = [];
    protected $auth = [];

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->sources = Source::getSources();
        $this->hotels = Store::getStoreList();
        if (in_array($this->user['role_id'], [2, 7])) {
            // 洗单组回访
            $this->status = [
                ['title' => '未跟进'],
                ['title' => '有效客资'],
                ['title' => '无效客资'],
                ['title' => '跟进中']
            ];
        } else {
            $this->status = Intention::getIntentions();
        }
        $this->auth = UserAuth::getUserLogicAuth($this->user['id']);
    }

    public function applyVisit()
    {
        $get = Request::param();
        if(Request::isAjax()) {
            $users = User::getUsers();
            $hotels = Hotel::getHotels();
            $newsTypes = ['婚宴信息', '婚庆信息', '婚宴转婚庆'];
            $config = [
                'page' => $get['page']
            ];

            $status = !isset($get['status']) || $get['status'] == 0 ? 0 : $get['status'];
            $map[] = ['apply_status', '=', $status];
            if(!empty($get['realname'])) {

                $list = model('MemberApply')->where($map)->where('member_id', 'in', function ($query) use ($get) {
                    $map = [];
                    $map[] = ['realname', 'like', "%{$get['realname']}%"];
                    $query->table('tk_member')->where($map)->field('id');
                })->with('member')->order('create_time desc')->paginate($get['limit'], false, $config);

            } else if(!empty($get['mobile'])) {
                $list = model('MemberApply')->where($map)->where('member_id', 'in', function ($query) use ($get) {
                    $map = [];
                    $map[] = ['mobile', 'like', "%{$get['mobile']}%"];
                    $query->table('tk_member')->where($map)->field('id');
                })->with('member')->order('create_time desc')->paginate($get['limit'], false, $config);
            } else {
                $list = model('MemberApply')->where($map)->with('member')->order('create_time desc')->paginate($get['limit'], false, $config);
            }

            if(!empty($list)) {
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    $member = $value['member'];
                    if (!is_array($member) || empty($value['member'])) continue;
                    unset($member['id']);
                    unset($value['member']);
                    $value = array_merge($value, $member);
                    $value['user_id'] = $users[$value['user_id']]['realname'];
                    $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                    $value['news_type'] = $newsTypes[$value['news_type']];
                    $value['hotel_id'] = $hotels[$value['hotel_id']];
                    if ($this->auth['is_show_alias'] == '1') {
                        $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['alias'] : '未知';
                    } else {
                        $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : '未知';
                    }
                }
            }

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data
            ];
            return json($result);

        } else {
            $tabs = Tab::managerApply($get);
            $this->assign('tabs', $tabs);
            $this->assign('get', $get);
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

        $userList = User::getUsers();
        $total = count($ids);
        $users = [];
        foreach ($ids as $id) {
            $MemberApply = MemberApply::get($id);
            $applyData = $MemberApply->getData();

            $member = Member::get($applyData['member_id']);
            $data = $member->getData();
            $data['active_status'] = 0;
            $data['allocate_type'] = 2;
            $result = MemberAllocate::insertAllocateData($applyData['user_id'], $applyData['member_id'], $data);
            if($result) {
                $MemberApply->apply_status = 1;
                $MemberApply->save();
                $user = $userList[$applyData['user_id']];
                $users[] = $user['dingding'];
            }
        }

        if(!empty($users)) {
            $DingModel = new \app\api\model\DingTalk();
            $message = $DingModel->linkMessage("新增客资", "新增客资消息", "http://h5.hongsizg.com/pages/customer/mine?status=0&is_into_store=0&page_title=%E6%9C%AA%E8%B7%9F%E8%BF%9B%E5%AE%A2%E8%B5%84&time=".time());
            $DingModel->sendJobMessage($users, $message);
        }

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

        foreach ($ids as $id) {
            $MemberApply = MemberApply::get($id);
            $data = [];
            $data['apply_status'] = 2;
            $MemberApply->save($data);
        }

        return json([
            'code'  => '200',
            'msg'   => '驳回回访申请成功'
        ]);
    }

    // 申请成单l
    public function applyToOrder()
    {
        $get = Request::param();
        if (Request::isAjax()) {
            $users = User::getUsers();
            $hotels = Hotel::getHotels();
            $sources = Source::getSources();
            $newsTypes = ['婚宴信息', '婚庆信息', '婚宴转婚庆'];
            $config = [
                'page' => $get['page']
            ];

            $status = !isset($get['status']) || $get['status'] == 0 ? 0 : $get['status'];
            $map[] = ['apply_status', '=', $status];
            $list = model('OrderApply')->where($map)->with('OrderEntire')->paginate($get['limit'], false, $config);

            if (!empty($list)) {
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    if (empty($value['order_entire'])) continue;
                    $order = $value['order_entire'];
                    unset($order['id']);
                    $value = array_merge($value, $order);
                    $value['news_type'] = $newsTypes[$value['news_type']];
                    $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
                    $value['sales_id'] = isset($users[$value['sales_id']]) ? $users[$value['sales_id']]['realname'] : '——';
                    $value['manager_id'] = isset($users[$value['manager_id']]) ? $users[$value['manager_id']]['realname'] : '——';
                    $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '——';
                }
            }

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data
            ];
            return json($result);

        } else {
            $tabs = Tab::applyToOrder($get);
            $this->assign('tabs', $tabs);
            $this->assign('get', $get);
            return $this->fetch();
        }
    }

    // 确认回访申请
    public function confirmOrderApply()
    {
        $ids = Request::post("ids");
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return json([
                'code'  => '500',
                'msg'   => '要确认的回访申请不能为空'
            ]);
        }

        $total = count($ids);
        foreach ($ids as $id) {
            $Apply = OrderApply::get($id);
            $Apply->apply_status = 1;
            $Apply->save();

        }

        return json([
            'code'  => '200',
            'msg'   => '确认申请确认成功'
        ]);
    }

    // 驳回回访申请
    public function cancelOrderApply()
    {
        $ids = Request::post("ids");
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return json([
                'code'  => '500',
                'msg'   => '要驳回的回访申请不能为空'
            ]);
        }

        foreach ($ids as $id) {
            $Apply = OrderApply::get($id);
            $Apply->apply_status = 2;
            $Apply->save();
        }

        return json([
            'code'  => '200',
            'msg'   => '驳回订单申请成功'
        ]);
    }
}