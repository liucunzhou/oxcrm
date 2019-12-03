<?php

namespace app\index\controller;


use app\common\model\Allocate;
use app\common\model\BanquetHall;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\OperateLog;
use app\common\model\OrderApply;
use app\common\model\OrderEntire;
use app\common\model\Search;
use app\common\model\Tab;
use app\common\model\User;
use think\facade\Request;

class Order extends Base
{

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        if (!Request::isAjax()) {
            $staffes = User::getUsersInfoByDepartmentId($this->user['department_id']);
            $this->assign('staffes', $staffes);
            $sources = \app\common\model\Source::getSources();
            $this->assign('sources', $sources);
        }

    }

    public function index()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $get['news_type'] = 2;
            $order = $this->_getOrderList($get);
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $order['count'],
                'data' => $order['data']
            ];
            return json($result);
        } else {
            $this->view->engine->layout(false);
            return $this->fetch('order_entire');
        }
    }

    public function orderEntireSource()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $get['news_type'] = 2;
            $order = $this->_getOrderList($get);
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $order['count'],
                'data' => $order['data']
            ];
            return json($result);
        } else {
            $this->view->engine->layout(false);
            return $this->fetch('order_entire');
        }
    }

    public function orderEntireScore()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $get['news_type'] = 2;
            $order = $this->_getOrderList($get);
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $order['count'],
                'data' => $order['data']
            ];
            return json($result);
        } else {
            $this->view->engine->layout(false);
            return $this->fetch('order_entire');
        }
    }


    # 婚庆订单
    public function wedding()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $get['news_type'] = 1;
            $order = $this->_getOrderList($get);
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $order['count'],
                'data' => $order['data']
            ];
            return json($result);
        } else {
            $this->view->engine->layout(false);
            return $this->fetch('order_wedding');
        }
    }

    # 婚宴订单
    public function banquet()
    {

        if (Request::isAjax()) {
            $get = Request::param();
            $get['news_type'] = 0;

            $order = $this->_getOrderList($get);
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $order['count'],
                'data' => $order['data']
            ];
            return json($result);
        } else {
            return $this->fetch('order_banquet');
        }
    }

    private function _getOrderList($get)
    {
        $config = [
            'page' => $get['page']
        ];
        $map = Search::order($this->user, $get);
        $list = model('OrderEntire')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
        $data = $list->getCollection();
        $sources = \app\common\model\Source::getSources();
        $users = \app\common\model\User::getUsers();
        $halls = BanquetHall::getBanquetHalls();
        foreach ($data as $key => &$value) {
            $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
            $value['sales_id'] = isset($users[$value['sales_id']]) ? $users[$value['sales_id']]['realname'] : '——';
            $value['manager_id'] = isset($users[$value['manager_id']]) ? $users[$value['manager_id']]['realname'] : '——';
            $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '——';
        }
        $count = $list->total();

        return ['data' => $data, 'count' => $count];
    }


    public function createOrder()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $user = User::getUser($get['user_id']);
        $allocate = MemberAllocate::getAllocate($user['id'], $get['member_id']);
        $this->assign('allocate', $allocate);

        $member = Member::get($get['member_id']);
        $this->assign('member', $member);

        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);
        return $this->fetch('create_order');
    }


}