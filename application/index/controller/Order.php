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

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);

        ## 酒店列表
        $hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $hotels);
        $this->assign('hotelsJson', json_encode($hotels));

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 供应商列表
        $suppliers = \app\common\model\Supplier::getList();
        $this->assign('suppliers', $suppliers);
        $this->assign('suppliersJson', json_encode($suppliers));
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

            $this->getTab('entire');
            $this->view->engine->layout(false);
            return $this->fetch('order/entire/list/index');
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

            $this->getTab('entire');
            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_source';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);
            $this->view->engine->layout(false);
            return $this->fetch('order/entire/list/contract_source');
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

            $this->getTab('entire');
            $request = Request::param();
            $this->assign('request', $request);

            $statusName = 'check_status_score';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

            $this->view->engine->layout(false);
            return $this->fetch('order/entire/list/contract_score');
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

            $this->getTab('entire');
            $request = Request::param();
            $this->assign('request', $request);

            $statusName = 'check_status_contract_fiance';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

            $this->view->engine->layout(false);
            return $this->fetch('order/entire/list/contract_fiance');
        }
    }

    # 一站式出纳收款审核
    public function entireReceivablesCashier()
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

            $this->getTab('entire');
            $request = Request::param();
            $this->assign('request', $request);

            $statusName = 'check_status_receivables_cashier';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);
            $this->view->engine->layout(false);
            return $this->fetch('order/entire/list/receivables_cashier');
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

            $this->getTab('entire');
            $request = Request::param();
            $this->assign('request', $request);

            $statusName = 'check_status_payment_account';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);
            $this->view->engine->layout(false);
            return $this->fetch('order/entire/list/payment_accounting');
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

            $this->getTab('entire');
            $request = Request::param();
            $this->assign('request', $request);

            $statusName = 'check_status_payment_fiance';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

            $this->view->engine->layout(false);
            return $this->fetch('order/entire/list/payment_fiance');
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

            $this->getTab('entire');
            $request = Request::param();
            $this->assign('request', $request);

            $statusName = 'check_status_payment_cashier';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

            $this->view->engine->layout(false);
            return $this->fetch('order/entire/list/payment_cashier');
        }
    }

    # 一站式付款————出纳审核
    public function entireComplete()
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

            $this->getTab('entire');
            $request = Request::param();
            $this->assign('request', $request);

            $statusName = 'status';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

            $this->view->engine->layout(false);
            return $this->fetch('order/entire/list/payment_cashier');
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

            $this->getTab('wedding');
            $this->view->engine->layout(false);
            return $this->fetch('order/wedding/list/index');
        }
    }

    # 婚庆订单来源审核
    public function weddingSource()
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
            $this->getTab('wedding');

            $this->view->engine->layout(false);
            return $this->fetch('order/wedding/list/contract_source');
        }
    }

    # 婚庆订单积分审核
    public function weddingScore()
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
            $this->getTab('wedding');

            $this->view->engine->layout(false);
            return $this->fetch('order/wedding/list/contract_score');
        }
    }

    # 婚庆订单财务订单审核
    public function weddingFiance()
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
            $this->getTab('wedding');

            $this->view->engine->layout(false);
            return $this->fetch('order/wedding/list/contract_fiance');
        }
    }

    # 婚庆订单收款审核
    public function weddingReceivablesCashier()
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
            $this->getTab('wedding');

            $this->view->engine->layout(false);
            return $this->fetch('order/wedding/list/receivables_cashier');
        }
    }

    # 婚庆订单会计付款审核
    public function weddingPaymentAccounting()
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
            $this->getTab('wedding');

            $this->view->engine->layout(false);
            return $this->fetch('order/wedding/list/payment_accounting');
        }
    }

    # 婚庆订单会计付款审核
    public function weddingPaymentFiance()
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
            $this->getTab('wedding');

            $this->view->engine->layout(false);
            return $this->fetch('order/wedding/list/payment_fiance');
        }
    }

    # 婚庆订单会计付款审核
    public function weddingPaymentCashier()
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
            $this->getTab('wedding');
            $this->view->engine->layout(false);
            return $this->fetch('order/wedding/list/payment_cashier');
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
            $this->getTab('banquet');

            return $this->fetch('order/banquet/list/index');
        }
    }

    # 婚宴订单
    public function banquetSource()
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
            $this->getTab('banquet');

            return $this->fetch('order/banquet/list/contract_source');
        }
    }

    # 婚宴订单
    public function banquetScore()
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
            $this->getTab('banquet');

            return $this->fetch('order/banquet/list/contract_score');
        }
    }

    # 婚宴订单
    public function banquetFiance()
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
            $this->getTab('banquet');

            return $this->fetch('order/banquet/list/contract_fiance');
        }
    }

    # 婚宴订单
    public function banquetReceivablesCashier()
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
            $this->getTab('banquet');

            return $this->fetch('order/banquet/list/receivables_cashier');
        }
    }

    # 婚宴订单
    public function banquetPaymentAccounting()
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
            $this->getTab('banquet');

            return $this->fetch('order/banquet/list/payment_accounting');
        }
    }

    # 婚宴订单
    public function banquetPaymentFiance()
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
            $this->getTab('banquet');

            return $this->fetch('order/banquet/list/payment_fiance');
        }
    }

    # 婚宴订单
    public function banquetPaymentCashier()
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
            $this->getTab('banquet');

            return $this->fetch('order/banquet/list/payment_cashier');
        }
    }

    # 婚宴订单
    public function banquetComplate()
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
            $this->getTab('banquet');

            return $this->fetch('order/banquet/list/index');
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

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/entire/create/create_order');
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

        return $this->fetch('order/entire/confirm/contract_source');
    }

    # 积分审核视图
    public function scoreConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;

        ## 获取订单信息
        $order = \app\common\model\Order::get($get['id'])->getData();

        ## 获取婚宴信息
        $banquet = OrderBanquet::where('order_id', '=', $get['id'])->field('id', true)->find()->getData();
        $order = array_merge($order, $banquet);
        ## 获取婚庆信息
        $wedding = OrderWedding::where('order_id', '=', $get['id'])->field('id', true)->find()->getData();
        $order = array_merge($order, $wedding);
        $this->assign('data', $order);

        ## 获取客资信息
        $member = Member::getByMobile($order['mobile']);
        $this->assign('member', $member);


        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/entire/confirm/contract_score');
    }

    # 财务审核
    public function fianceConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();
        $this->assign('data', $order);

        $member = Member::getByMobile($order['mobile']);
        $this->assign('member', $member);

        ## 获取婚宴信息
        $banquet = OrderBanquet::where('order_id', '=', $get['id'])->field('id', true)->find()->getData();
        $order = array_merge($order, $banquet);
        ## 获取婚宴付款信息
        $banquetPayment = OrderBanquetPayment::where('order_id', '=', $get['id'])->field('id', true)->find()->getData();
        $order = array_merge($order, $banquetPayment);

        ## 获取婚宴付款信息
        $banquetReceivables = OrderBanquetReceivables::where('order_id', '=', $get['id'])->field('id', true)->find()->getData();
        $order = array_merge($order, $banquetReceivables);


        ## 获取婚庆信息
        $wedding = OrderWedding::where('order_id', '=', $get['id'])->field('id', true)->find()->getData();
        $order = array_merge($order, $wedding);
        ## 获取婚庆付款信息
        $weddingPayment = OrderWeddingPayment::where('order_id', '=', $get['id'])->field('id', true)->find()->getData();
        $order = array_merge($order, $weddingPayment);

        ## 获取婚庆收款信息
        $weddingReceivables = OrderWeddingReceivables::where('order_id', '=', $get['id'])->field('id', true)->find()->getData();
        $order = array_merge($order, $weddingReceivables);


        $this->assign('data', $order);


        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/entire/confirm/contract_fiance');
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

        return $this->fetch('order/entire/confirm/receivables_cashier');
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

        return $this->fetch('order/entire/confirm/payment_accounting');
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

        return $this->fetch('order/entire/confirm/payment_fiance');
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

        return $this->fetch('order/entire/confirm/payment_cashier');
    }


    public function doConfirmSource()
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

    # 一站式
    private function getTab($prefix='entire')
    {
        if($prefix == 'entire') {
            $home   = 'index';
        } else  {
            $home   = $prefix;
        }

        switch ($this->user['role_id']) {
            case 35: // 来源审核角色ID
                $tabs = [
                    $home                         => '全部',
                    $prefix.'Source'              => '渠道审核',
                    $prefix.'Complete'            => '完成的订单'
                ];
                break;
            case 51: // 积分审核角色ID
                $tabs = [
                    $home                         => '全部',
                    $prefix.'Score'               => '积点审核',
                    $prefix.'Complete'            => '完成的订单'
                ];
                break;
            case 29: // 财务角色ID
                $tabs = [
                    $home                         => '全部',
                    $prefix.'Fiance'              => '财务审核',
                    $prefix.'PaymentFiance'       => '财务主管付款审核',
                    $prefix.'Complete'            => '完成的订单'
                ];
                break;
            case 33: // 出纳角色ID
                $tabs = [
                    $home                         => '全部',
                    $prefix.'ReceivablesCashier'  => '出纳收款审核',
                    $prefix.'PaymentCashier'      => '出纳付款审核',
                    $prefix.'Complete'            => '完成的订单'
                ];
                break;
            case 34: // 会计角色Id
                $tabs = [
                    $home                         => '全部',
                    $prefix.'PaymentAccounting'   => '会计付款审核',
                    $prefix.'Complete'            => '完成的订单'
                ];
                break;
            default :
                $tabs = [
                    $home                         => '全部',
                    $prefix.'Source'              => '渠道审核',
                    $prefix.'Score'               => '积点审核',
                    $prefix.'Fiance'              => '财务审核',
                    $prefix.'ReceivablesCashier'  => '出纳收款审核',
                    $prefix.'PaymentAccounting'   => '会计付款审核',
                    $prefix.'PaymentFiance'       => '财务主管付款审核',
                    $prefix.'PaymentCashier'      => '出纳付款审核',
                    $prefix.'Complete'            => '完成的订单'
                ];

        }
        $this->assign('tabs', $tabs);
    }

    private function getSubTab($request, $statusName, $action)
    {
        if(isset($request[$statusName])) {
            $crr = $request[$statusName];
        } else {
            $crr = -1;
        }

        $subtab = [];
        $current = $crr == -1 ? 1 : 0;
        unset($request[$statusName]);
        $subtab[] = ['current' => $current, 'url' => url($action, $request), 'text' => '全部'];

        $current = $crr == 0 ? 1 : 0;
        $request[$statusName] = 0;
        $subtab[] = ['current' => $current, 'url' => url($action, $request), 'text' => '待审核'];

        $current = $crr == 1 ? 1 : 0;
        $request[$statusName] = 1;
        $subtab[] = ['current' => $current, 'url' => url($action, $request), 'text' => '已通过'];

        $current = $crr == 2 ? 1 : 0;
        $request[$statusName] = 2;
        $subtab[] = ['current' => $current, 'url' => url($action, $request), 'text' => '已驳回'];
        $this->assign('subtab', $subtab);
    }

}