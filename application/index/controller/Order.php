<?php

namespace app\index\controller;


use app\common\model\Allocate;
use app\common\model\BanquetHall;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\OperateLog;
use app\common\model\OrderApply;
use app\common\model\OrderBanquet;
use app\common\model\OrderBanquetPayment;
use app\common\model\OrderBanquetReceivables;
use app\common\model\OrderEntire;
use app\common\model\OrderWedding;
use app\common\model\OrderWeddingPayment;
use app\common\model\OrderWeddingReceivables;
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

        switch ($this->user['role_id']) {
            case 35: // 来源审核角色ID
                $entireTabs = [
                    'index'                     => '全部',
                    'entireSource'              => '渠道审核',
                    'entireComplete'            => '完成的订单'
                ];
                break;
            case 51: // 积分审核角色ID
                $entireTabs = [
                    'index'                     => '全部',
                    'entireScore'               => '积点审核',
                    'entireComplete'            => '完成的订单'
                ];
                break;
            case 29: // 财务角色ID
                $entireTabs = [
                    'index'                     => '全部',
                    'entireFiance'              => '财务审核',
                    'entirePaymentFiance'       => '财务主管付款审核',
                    'entireComplete'            => '完成的订单'
                ];
                break;
            case 33: // 出纳角色ID
                $entireTabs = [
                    'index'                     => '全部',
                    'entireReceviablesCashier'  => '出纳收款审核',
                    'entirePaymentCashier'      => '出纳付款审核',
                    'entireComplete'            => '完成的订单'
                ];
                break;
            case 34: // 会计角色Id
                $entireTabs = [
                    'index'                     => '全部',
                    'entirePaymentAccounting'   => '会计付款审核',
                    'entireComplete'            => '完成的订单'
                ];
                break;
            default :
                $entireTabs = [
                    'index'                     => '全部',
                    'entireSource'              => '渠道审核',
                    'entireScore'               => '积点审核',
                    'entireFiance'              => '财务审核',
                    'entireReceviablesCashier'  => '出纳收款审核',
                    'entirePaymentAccounting'   => '会计付款审核',
                    'entirePaymentFiance'       => '财务主管付款审核',
                    'entirePaymentCashier'      => '出纳付款审核',
                    'entireComplete'            => '完成的订单'
                ];

        }

        $this->assign('entireTabs', $entireTabs);
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
            return $this->fetch('order/entire/index');
        }
    }

    # 一站式来源审核
    public function entireSource()
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
            return $this->fetch('order/entire/source');
        }
    }

    # 一站式积分审核
    public function entireScore()
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
            return $this->fetch('order/entire/score');
        }
    }

    # 一站式财务审核
    public function entireFiance()
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
            return $this->fetch('order/entire/fiance');
        }
    }

    # 一站式出纳收款审核
    public function entireReceviablesCashier()
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
            return $this->fetch('order/entire/receviables_cashier');
        }
    }

    # 一站式付款————会计审核
    public function entirePaymentAccounting()
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
            return $this->fetch('order/entire/payment_accounting');
        }
    }

    # 一站式付款————财务主管审核
    public function entirePaymentFiance()
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
            return $this->fetch('order/entire/payment_fiance');
        }
    }

    # 一站式付款————出纳审核
    public function entirePaymentCashier()
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
            return $this->fetch('order/entire/payment_cashier');
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
            return $this->fetch('order/list/order_wedding');
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
            return $this->fetch('order/list/order_banquet');
        }
    }

    private function _getOrderList($get)
    {
        $config = [
            'page' => $get['page']
        ];
        $map = Search::order($this->user, $get);
        $list = model('order')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
        $data = $list->getCollection();
        $sources = \app\common\model\Source::getSources();
        $users = \app\common\model\User::getUsers();
        $halls = BanquetHall::getBanquetHalls();
        foreach ($data as $key => &$value) {
            $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
            $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '——';
        }
        $count = $list->total();

        return ['data' => $data, 'count' => $count];
    }


    # 创建订单视图
    public function createOrder()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $user = User::getUser($get['user_id']);
        $allocate = MemberAllocate::getAllocate($user['id'], $get['member_id']);
        $this->assign('allocate', $allocate);

        $member = Member::get($get['member_id']);
        $this->assign('member', $member);

        ## 酒店列表
        $hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $hotels);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/create/create_order');
    }

    # 创建订单逻辑
    public function doCreateOrder()
    {
        $request = Request::param();
        $OrderModel = new \app\common\model\Order();

        $OrderModel->allowField(true)->save($request);
        $request['order_id'] = $OrderModel->id;

        ## banquet message
        $BanquetModel = new OrderBanquet();
        $BanquetModel->allowField(true)->save($request);

        $BanquetPaymentModel = new OrderBanquetPayment();
        $BanquetPaymentModel->allowField(true)->save($request);

        $BanquetReceivablesModel = new OrderBanquetReceivables();
        $BanquetReceivablesModel->allowField(true)->save($request);

        ## wedding message
        $WeddingModel = new OrderWedding();
        $WeddingModel->allowField(true)->save($request);

        $WeddingPaymentModel = new OrderWeddingPayment();
        $WeddingPaymentModel->allowField(true)->save($request);

        $WeddingReceivablesModel = new OrderWeddingReceivables();
        $WeddingReceivablesModel->allowField(true)->save($request);
    }

    # 来源审核视图
    public function sourceConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();
        $this->assign('data', $order);

        $member = Member::getByMobile($order['mobile']);
        $this->assign('member', $member);

        ## 酒店列表
        $hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $hotels);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/confirm/source_confirm');
    }

    # 积分审核视图
    public function scoreConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();
        $this->assign('data', $order);

        $member = Member::getByMobile($order['mobile']);
        $this->assign('member', $member);

        ## 酒店列表
        $hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $hotels);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/confirm/score_confirm');
    }

    public function fianceConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();
        $this->assign('data', $order);

        $member = Member::getByMobile($order['mobile']);
        $this->assign('member', $member);

        ## 酒店列表
        $hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $hotels);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/confirm/fiance_confirm');
    }

    # 出纳收款审核
    public function cashierReceivablesConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();
        $this->assign('data', $order);

        $member = Member::getByMobile($order['mobile']);
        $this->assign('member', $member);

        ## 酒店列表
        $hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $hotels);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/confirm/cashier_receivables_confirm');
    }

    # 会计付款审核
    public function accountingPaymentConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();
        $this->assign('data', $order);

        $member = Member::getByMobile($order['mobile']);
        $this->assign('member', $member);

        ## 酒店列表
        $hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $hotels);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/confirm/accounting_payment_confirm');
    }

    # 会计付款审核
    public function fiancePaymentConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();
        $this->assign('data', $order);

        $member = Member::getByMobile($order['mobile']);
        $this->assign('member', $member);

        ## 酒店列表
        $hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $hotels);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/confirm/fiance_payment_confirm');
    }

    # 会计付款审核
    public function cashierPaymentConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();
        $this->assign('data', $order);

        $member = Member::getByMobile($order['mobile']);
        $this->assign('member', $member);

        ## 酒店列表
        $hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $hotels);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/confirm/cashier_payment_confirm');
    }

    public function doSourceConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $user = User::getUser($get['user_id']);
        $allocate = MemberAllocate::getAllocate($user['id'], $get['member_id']);
        $this->assign('allocate', $allocate);

        $member = Member::get($get['member_id']);
        $this->assign('member', $member);

        ## 酒店列表
        $hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $hotels);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/create/create_order');
    }
}