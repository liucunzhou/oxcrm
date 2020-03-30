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
use app\common\model\OrderBanquetSuborder;
use app\common\model\OrderEntire;
use app\common\model\OrderWedding;
use app\common\model\OrderWeddingPayment;
use app\common\model\OrderWeddingReceivables;
use app\common\model\OrderWeddingSuborder;
use app\common\model\Search;
use app\common\model\Tab;
use app\common\model\User;
use think\facade\Request;

class Order extends Base
{
    protected $hotels = [];
    protected $sources = [];
    protected $suppliers = [];
    protected $weddingDevices = [];
    protected $weddingCategories = [];
    protected $paymentTypes = [1=>'定金', 2=>'预付款', 3=>'尾款', 4=>'其他'];
    protected $payments = [1=>'现金', 2=>'POS机', 3=>'微信', 4=>'支付宝'];
    protected $confirmStatusList = [0=>'待审核', 1=>'通过', 2=>'驳回'];

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->assign('payments', $this->payments);
        $this->assign('paymentTypes', $this->paymentTypes);
        $this->assign('confirmStatusList', $this->confirmStatusList);

        $staffes = User::getUsersInfoByDepartmentId($this->user['department_id']);
        $this->assign('staffes', $staffes);

        $this->sources = \app\common\model\Source::getSources();
        $this->assign('sources', $this->sources);

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $brands);

        ## 酒店列表
        $this->hotels = \app\common\model\Store::getStoreList();
        $this->assign('hotels', $this->hotels);
        $this->assign('hotelsJson', json_encode($this->hotels));

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取套餐列表
        $packages = \app\common\model\Package::getList();
        $this->assign('packages', $packages);

        ## 获取仪式列表
        $rituals = \app\common\model\Ritual::getList();
        $this->assign('rituals', $rituals);

        ## 供应商列表
        $this->suppliers = \app\common\model\Supplier::getList();
        $this->assign('suppliers', $this->suppliers);
        $this->assign('suppliersJson', json_encode($this->suppliers));

        ## 婚庆设备列表
        $this->weddingDevices = \app\common\model\WeddingDevice::getList();
        $this->assign('weddingDevices', $this->weddingDevices);

        ## 婚庆二销分类列表
        $this->weddingCategories = \app\common\model\WeddingCategory::getList();
        $this->assign('weddingCategories', $this->weddingDevices);
    }

    public function index()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $get['news_type'] = 2;
            $order = $this->_getOrderList($get, 'index');
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',

                'count' => $order['count'],
                'data' => $order['data']
            ];
            return json($result);
        } else {
            $this->getTab('entire');
            $this->getColsFile('index');
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
            $order = $this->_getOrderList($get, 'check_status_source');
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
            $order = $this->_getOrderList($get, 'check_status_score');
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $order['count'],
                'data' => $order['data']
            ];
            return json($result);
        } else {

            $this->getTab('entire', 'check_status_score');

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
            $order = $this->_getOrderList($get, 'check_status_contract_fiance');
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
            $order = $this->_getOrderList($get, 'check_status_receivables_cashier');
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
            $order = $this->_getOrderList($get, 'check_status_payment_account');
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
            $order = $this->_getOrderList($get, 'check_status_payment_fiance');
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
            $order = $this->_getOrderList($get, 'check_status_payment_cashier');
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
            $order = $this->_getOrderList($get, 'complete');
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
            return $this->fetch('order/entire/list/complete');
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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_source';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_score';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_contract_fiance';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_receivables_cashier';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_payment_account';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_payment_fiance';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_payment_cashier';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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
            $this->getColsFile('index');
            $this->getTab('banquet');

            $this->view->engine->layout(false);
            return $this->fetch('order/banquet/list/index');
        }
    }

    # 婚宴订单审核
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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_source';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_score';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_receivables_cashier';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_payment_account';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_payment_fiance';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

            $request = Request::param();
            $this->assign('request', $request);
            $statusName = 'check_status_payment_cashier';
            $action = $this->request->action();
            $this->getSubTab($request, $statusName, $action);

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

    # 创建订单视图
    public function createOrder()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $user = User::getUser($get['user_id']);
        $allocate = MemberAllocate::get($get['id']);
        $this->assign('allocate', $allocate);

        $member = Member::get($get['member_id']);
        $this->assign('member', $member);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        if($allocate['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/create/main';
        } else if ($allocate['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/create/main';
        } else if ($allocate['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/create/main';
        }
        return $this->fetch($view);
    }

    # 创建订单逻辑
    public function doCreateOrder()
    {
        $request = Request::param();
        $OrderModel = new \app\common\model\Order();
        $OrderModel->allowField(true)->save($request);
        $request['order_id'] = $OrderModel->id;
        $newsType = $request['news_type'];
        switch ($newsType) {
            case 0: // banquet
                ## banquet message
                $BanquetModel = new OrderBanquet();
                $BanquetModel->allowField(true)->save($request);

                ## banquet receivables message
                $BanquetReceivablesModel = new OrderBanquetReceivables();
                $BanquetReceivablesModel->allowField(true)->save($request);
                break;
            case 1: // wedding
                ## wedding contact message
                $WeddingModel = new OrderWedding();
                // get wedding devices
                $WeddingModel->wedding_device = json_encode($request['weddingDevices']);
                $WeddingModel->allowField(true)->save($request);

                ## wedding receivable message
                $WeddingReceivablesModel = new OrderWeddingReceivables();
                $WeddingReceivablesModel->allowField(true)->save($request);
                break;
            case 2: // entire
                ## banquet message
                $BanquetModel = new OrderBanquet();
                $BanquetModel->allowField(true)->save($request);

                ## banquet receivables message
                $BanquetReceivablesModel = new OrderBanquetReceivables();
                $BanquetReceivablesModel->allowField(true)->save($request);

                ## wedding contact message
                $WeddingModel = new OrderWedding();
                // get wedding devices
                $WeddingModel->wedding_device = json_encode($request['weddingDevices']);
                $WeddingModel->allowField(true)->save($request);
                break;
            default: // entire
                ## banquet message
                $BanquetModel = new OrderBanquet();
                $BanquetModel->allowField(true)->save($request);

                ## banquet receivables message
                $BanquetReceivablesModel = new OrderBanquetReceivables();
                $BanquetReceivablesModel->allowField(true)->save($request);

                ## wedding contact message
                $WeddingModel = new OrderWedding();
                // get wedding devices
                $WeddingModel->wedding_device = json_encode($request['weddingDevices']);
                $WeddingModel->allowField(true)->save($request);
                break;
        }

        return json(['code' => '200', 'msg' => '创建成功']);
    }

    # 编辑订单视图
    public function editOrder()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();

        switch ($order['news_type'])
        {
            ### 婚宴信息
            case 0:
                #### 获取婚宴订单信息
                $where = [];
                $where['pid'] = 0;
                $where['order_id'] = $get['id'];
                $banquet = OrderBanquet::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $banquet);
                #### 获取婚宴二销订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $banquetOrders = OrderBanquetSuborder::where($where)->select();
                $this->assign('banquetOrders', $banquetOrders);

                #### 获取婚宴收款信息
                $receivables = OrderBanquetReceivables::where('order_id', '=', $get['id'])->select();
                $this->assign('receivables', $receivables);
                #### 获取婚宴付款信息
                $banquetPayments = OrderBanquetPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('banquetPayments', $banquetPayments);
                break;

            ### 婚庆信息
            case 1:
                #### 获取婚庆订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $wedding = OrderWedding::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $wedding);
                $selectedWeddingDevices = json_decode($wedding['wedding_device'], true);
                if(!is_array($selectedWeddingDevices)) $selectedWeddingDevices = [];
                $this->assign('selectedWeddingDevices', $selectedWeddingDevices);

                #### 获取婚宴二销订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $weddingOrders = OrderWeddingSuborder::where($where)->select();
                $this->assign('weddingOrders', $weddingOrders);
                #### 获取婚庆收款信息
                $receivables = OrderWeddingReceivables::where('order_id', '=', $get['id'])->select();
                $this->assign('receivables', $receivables);
                #### 获取婚庆付款信息
                $weddingPayments = OrderWeddingPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('weddingPayments', $weddingPayments);
                break;

            ### 一站式信息
            case 2:
                #### 获取婚宴订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $banquet = OrderBanquet::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $banquet);
                #### 获取婚宴二销订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $banquetOrders = OrderBanquetSuborder::where($where)->select();
                $this->assign('banquetOrders', $banquetOrders);

                #### 获取婚庆订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $wedding = OrderWedding::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $wedding);
                $selectedWeddingDevices = json_decode($wedding['wedding_device'], true);
                if(!is_array($selectedWeddingDevices)) $selectedWeddingDevices = [];
                $this->assign('selectedWeddingDevices', $selectedWeddingDevices);
                #### 获取婚宴二销订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $weddingOrders = OrderWeddingSuborder::where($where)->select();
                $this->assign('weddingOrders', $weddingOrders);


                #### 获取婚宴收款信息
                $receivables = OrderBanquetReceivables::where('order_id', '=', $get['id'])->select();
                $this->assign('receivables', $receivables);
                #### 获取婚宴付款信息
                $banquetPayments = OrderBanquetPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('banquetPayments', $banquetPayments);
                #### 获取婚庆付款信息
                $weddingPayments = OrderWeddingPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('weddingPayments', $weddingPayments);
                break;

            default:
                #### 获取婚宴订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $where['pid'] = 0;
                $banquet = OrderBanquet::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $banquet);
                #### 获取婚宴二销订单信息
                $where = [];
                $where['pid'] = ['neq', 0];
                $where['order_id'] = $get['id'];
                $banquetOrders = OrderBanquet::where($where)->select();
                $this->assign('banquetOrders', $banquetOrders);

                #### 获取婚庆订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $where['pid'] = 0;
                $wedding = OrderWedding::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $wedding);
                #### 获取婚宴二销订单信息
                $where = [];
                $where['pid'] = ['neq', 0];
                $where['order_id'] = $get['id'];
                $weddingOrders = OrderWedding::where($where)->select();
                $this->assign('weddingOrders', $weddingOrders);

                #### 获取婚宴收款信息
                $receivables = OrderBanquetReceivables::where('order_id', '=', $get['id'])->select();
                $this->assign('receivables', $receivables);
                #### 获取婚宴付款信息
                $banquetPayments = OrderBanquetPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('banquetPayments', $banquetPayments);
                #### 获取婚庆付款信息
                $weddingPayments = OrderWeddingPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('weddingPayments', $weddingPayments);
        }

        $order = $this->formatOrderDate($order);
        $this->assign('data', $order);

        ##　获取客资分配信息
        $allocate = MemberAllocate::where('id', '=', $order['member_allocate_id'])->find();
        $this->assign('allocate', $allocate);

        ## 获取客户信息
        $member = Member::get($order['member_id']);
        $this->assign('member', $member);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        if($allocate->news_type == '0') { // 婚宴订单
            $view = 'order/banquet/edit/main';
        } else if ($allocate->news_type == 1) { // 婚庆客资
            $view = 'order/wedding/edit/main';
        } else if ($allocate->news_type == 2) { // 一站式客资
            $view = 'order/entire/edit/main';
        } else {
            $view = 'order/entire/edit/main';
        }
        return $this->fetch($view);
    }

    # 编辑订单逻辑
    public function doEditOrder()
    {
        $request = Request::param();
        $request['order_id'] = $request['id'];
        $newsType = $request['news_type'];
        unset($request['id']);
        switch ($newsType) {
            case 0: // banquet
                ## banquet message
                $where = [];
                $where['order_id'] = $request['order_id'];
                $BanquetModel = OrderBanquet::where($where)->find();
                $BanquetModel->allowField(true)->save($request);
                break;
            case 1: // wedding
                $where = [];
                $where['order_id'] = $request['order_id'];
                ## wedding contact message
                $WeddingModel = OrderWedding::where($where)->find();
                // get wedding devices
                $WeddingModel->wedding_device = json_encode($request['weddingDevices']);
                $WeddingModel->allowField(true)->save($request);
                break;
            case 2: // entire

                ## banquet message
                $where = [];
                $where['order_id'] = $request['order_id'];
                $BanquetModel = OrderBanquet::where($where)->find();
                $BanquetModel->allowField(true)->save($request);
                ## wedding contact message
                $WeddingModel = OrderWedding::where($where)->find();
                // get wedding devices
                $WeddingModel->wedding_device = json_encode($request['weddingDevices']);
                $WeddingModel->allowField(true)->save($request);
                break;

            default: // entire

                ## banquet message
                $where = [];
                $where['order_id'] = $request['order_id'];
                $BanquetModel = OrderBanquet::where($where)->find();
                $BanquetModel->allowField(true)->save($request);
                ## wedding contact message
                $WeddingModel = OrderWedding::where($where)->find();
                // get wedding devices
                $WeddingModel->wedding_device = json_encode($request['weddingDevices']);
                $WeddingModel->allowField(true)->save($request);
                break;
        }

        ## 重新计算订单金额
        // tail_money = contact_money * 0.3 +  sum(wedding_totals) + sum(banquet_totals)
        $order = \app\common\model\Order::get($request['order_id']);
        $banquetTotals = OrderBanquetSuborder::where('order_id', '=', $request['order_id'])->sum('banquet_totals');
        $weddingTotals = OrderWeddingSuborder::where('order_id', '=', $request['order_id'])->sum('wedding_totals');
        $order->tail_money = $request['contract_totals']*0.2 + $banquetTotals + $weddingTotals;
        $order->totals = $request['contract_totals'] + $banquetTotals + $weddingTotals;
        unset($request['tail_money']);
        unset($request['totals']);
        $result2 = $order->save($request);
        return json(['code' => '200', 'msg' => '更新订单成功']);
    }

    # 查看订单信息
    public function showOrder()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();

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

        $order = $this->formatOrderDate($order);
        $this->assign('data', $order);

        ##　获取客资分配信息
        $allocate = MemberAllocate::where('id', '=', $order['member_allocate_id'])->find();

        ## 获取客户信息
        $member = Member::get($order['member_id']);
        $this->assign('member', $member);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/show/main';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/show/main';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/show/main';
        } else {
            $view = 'order/entire/show/main';
        }
        return $this->fetch($view);
    }

    # 创建婚庆子合同
    public function createWeddingSuborder()
    {
        $get = Request::param();
        $order = \app\common\model\Order::get($get['order_id'])->getData();

        ## 获取二销项目列表
        $items = \app\common\model\WeddingDevice::getList();
        $this->assign('items', $items);

        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/create/wedding_suborder';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/create/wedding_suborder';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/create/wedding_suborder';
        }
        return $this->fetch($view);
    }

    # 编辑婚庆子合同
    public function editWeddingSuborder()
    {
        $get = Request::param();
        $suborder = OrderWeddingSuborder::get($get['id']);
        $this->assign('data', $suborder);
        $selectedItems =json_decode($suborder->wedding_items, true);
        $this->assign('selectedItems', $selectedItems);

        ## 获取二销项目列表
        $items = \app\common\model\WeddingDevice::getList();
        $this->assign('items', $items);

        $order = \app\common\model\Order::get($suborder->order_id)->getData();
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/edit/wedding_suborder';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/edit/wedding_suborder';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/edit/wedding_suborder';
        }
        return $this->fetch($view);
    }

    # 添加/编辑婚庆子合同
    public function doEditWeddingSuborder()
    {
        $request = Request::param();
        if(!empty($request['id'])) {
            $action = '更新';
            $model = OrderWeddingSuborder::get($request['id']);
        } else {
            $action = '添加';
            $model = new OrderWeddingSuborder();
        }
        $model->startTrans();
        $model->wedding_items = json_encode($request['items']);
        $result1 = $model->save($request);

        // tail_money = contact_money * 0.2 +  sum(wedding_totals) + sum(banquet_totals)
        $order = \app\common\model\Order::get($request['order_id']);
        $banquetTotals = OrderBanquetSuborder::where('order_id', '=', $request['order_id'])->sum('banquet_totals');
        $weddingTotals = OrderWeddingSuborder::where('order_id', '=', $request['order_id'])->sum('wedding_totals');
        $order->tail_money = $order->contract_totals*0.2 + $banquetTotals + $weddingTotals;
        $order->totals = $order->contract_totals + $banquetTotals + $weddingTotals;
        $result2 = $order->save();

        if($result1 && $result2) {
            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($model);
            $model->commit();
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            $model->rollback();
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    # 编辑婚庆子合同
    public function confirmWeddingSuborder()
    {
        $get = Request::param();
        $suborder = OrderWeddingSuborder::get($get['id']);
        $this->assign('data', $suborder);

        $order = \app\common\model\Order::get($suborder->order_id)->getData();

        ## 获取二销项目列表
        $items = \app\common\model\WeddingDevice::getList();
        $this->assign('items', $items);

        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/confirm/wedding_suborder';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/confirm/wedding_suborder';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/confirm/wedding_suborder';
        }
        return $this->fetch($view);
    }

    # 添加/编辑婚庆子合同
    public function doConfirmWeddingSuborder()
    {
        $request = Request::param();
        if(!empty($request['id'])) {
            $action = '审核';
            $model = OrderWeddingSuborder::get($request['id']);
        } else {
            $action = '审核';
            $model = new OrderWeddingSuborder();
        }

        $model->wedding_items = json_encode($request['items']);
        $result = $model->save($request);
        if($result) {
            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    # 创建婚宴子合同
    public function createBanquetSuborder()
    {
        $get = Request::param();
        $order = \app\common\model\Order::get($get['order_id'])->getData();

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/create/banquet_suborder';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/create/banquet_suborder';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/create/banquet_suborder';
        }
        return $this->fetch($view);
    }

    # 编辑婚宴子合同
    public function editBanquetSuborder()
    {
        $get = Request::param();
        $suborder = OrderBanquetSuborder::get($get['id']);
        $this->assign('data', $suborder);

        $order = \app\common\model\Order::get($suborder->order_id)->getData();

        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/edit/banquet_suborder';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/edit/banquet_suborder';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/edit/banquet_suborder';
        }
        return $this->fetch($view);
    }

    # 添加/编辑婚宴子合同
    public function doEditBanquetSuborder()
    {
        $request = Request::param();
        if(!empty($request['id'])) {
            $action = '更新';
            $model = OrderBanquetSuborder::get($request['id']);
        } else {
            $action = '添加';
            $model = new OrderBanquetSuborder();
        }

        $model->startTrans();
        $result1 = $model->save($request);
        // tail_money = contact_money * 0.2 +  sum(wedding_totals) + sum(banquet_totals)
        $order = \app\common\model\Order::get($request['order_id']);
        $banquetTotals = OrderBanquetSuborder::where('order_id', '=', $request['order_id'])->sum('banquet_totals');
        $weddingTotals = OrderWeddingSuborder::where('order_id', '=', $request['order_id'])->sum('wedding_totals');
        $order->tail_money = $order->contract_totals*0.2 + $banquetTotals + $weddingTotals;
        $order->totals = $order->contract_totals + $banquetTotals + $weddingTotals;
        $result2 = $order->save();

        if($result1 && $result2) {
            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($model);
            $model->commit();
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            $model->rollback();
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    # 确认婚宴二销信息
    public function confirmBanquetSuborder()
    {
        $get = Request::param();
        $suborder = OrderBanquetSuborder::get($get['id']);
        $this->assign('data', $suborder);

        $order = \app\common\model\Order::get($suborder->order_id)->getData();

        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/confirm/banquet_suborder';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/confirm/banquet_suborder';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/confirm/banquet_suborder';
        }
        return $this->fetch($view);
    }

    # 审核婚宴二销订单
    public function doConfirmBanquetSuborder()
    {
        $request = Request::param();
        $action = '确认';
        $model = OrderBanquetSuborder::get($request['id']);
        $result = $model->save($request);
        if($result) {
            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    # 创建婚宴收款信息
    public function createBanquetReceivable()
    {
        $get = Request::param();
        $order = \app\common\model\Order::get($get['order_id'])->getData();

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/create/banquet_receivable';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/create/banquet_receivable';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/create/banquet_receivable';
        }
        return $this->fetch($view);
    }

    # 创建婚宴收款信息
    public function editBanquetReceivable()
    {
        $get = Request::param();
        $banquetReceivable = OrderBanquetReceivables::get($get['id']);
        $this->assign('data', $banquetReceivable);
        $order = \app\common\model\Order::get($banquetReceivable->order_id)->getData();

        ## 获取付款信息
        $data = OrderBanquetReceivables::get($get['id']);
        $this->assign('data', $data);

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/edit/banquet_receivable';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/edit/banquet_receivable';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/edit/banquet_receivable';
        }
        return $this->fetch($view);
    }

    # 婚宴收款逻辑
    public function doEditBanquetReceivable()
    {
        $request = Request::param();
        if(!empty($request['id'])) {
            $action = '更新';
            $model = OrderBanquetReceivables::get($request['id']);
        } else {
            $action = '添加';
            $model = new OrderBanquetReceivables();
        }

        $result = $model->save($request);
        if($result) {
            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    # 确认婚宴收款信息
    public function confirmBanquetReceivable()
    {
        $get = Request::param();
        $banquetReceivable = OrderBanquetReceivables::get($get['id']);
        $this->assign('data', $banquetReceivable);
        $order = \app\common\model\Order::get($banquetReceivable->order_id)->getData();

        ## 获取付款信息
        $data = OrderBanquetReceivables::get($get['id']);
        $this->assign('data', $data);

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/confirm/banquet_receivable';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/confirm/banquet_receivable';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/confirm/banquet_receivable';
        }
        return $this->fetch($view);
    }

    # 确认婚宴收款逻辑
    public function doConfirmBanquetReceivable()
    {
        $request = Request::param();
        if(!empty($request['id'])) {
            $action = '更新';
            $model = OrderBanquetReceivables::get($request['id']);
        } else {
            $action = '添加';
            $model = new OrderBanquetReceivables();
        }

        $result = $model->save($request);
        if($result) {
            $order = \app\common\model\Order::get($request['order_id']);
            if($order['news_type'] == 0) {
                $order->save(['check_status_receivables_cashier' => $request['check_banquet_receivable_status']]);
            } else if ($order['news_type'] == 1) {
                $order->save(['check_status_receivables_cashier' => $request['check_status_receivables_cashier']]);
            } else {
                $order->save(['check_status_receivables_cashier' => $request['check_banquet_receivable_status']]);
            }
            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    # 创建婚宴付款信息
    public function createBanquetPayment()
    {
        $get = Request::param();
        $order = \app\common\model\Order::get($get['order_id'])->getData();

        // 获取酒店信息
        $hotel = \app\common\model\Store::get($order['hotel_id']);
        $this->assign('hotel', $hotel);

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/create/banquet_payment';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/create/banquet_payment';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/create/banquet_payment';
        }
        return $this->fetch($view);
    }

    # 获取婚宴付款信息
    public function editBanquetPayment()
    {
        $get = Request::param();
        $banquetPayment = OrderBanquetPayment::get($get['id']);
        $this->assign('data', $banquetPayment);
        $order = \app\common\model\Order::get($banquetPayment->order_id)->getData();

        ## 获取酒店信息
        $hotel = \app\common\model\Store::get($order['hotel_id']);
        $this->assign('hotel', $hotel);

        ## 获取婚宴付款信息
        $data = OrderBanquetPayment::get($get['id']);
        $this->assign('data', $data);

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/edit/banquet_payment';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/edit/banquet_payment';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/edit/banquet_payment';
        }
        return $this->fetch($view);
    }

    # 婚宴付款信息--会计审核
    public function checkBanquetPaymentAccounting()
    {
        $get = Request::param();
        $banquetPayment = OrderBanquetPayment::get($get['id']);
        $this->assign('data', $banquetPayment);
        $order = \app\common\model\Order::get($banquetPayment->order_id)->getData();

        ## 获取酒店信息
        $hotel = \app\common\model\Store::get($order['hotel_id']);
        $this->assign('hotel', $hotel);

        ## 获取婚宴付款信息
        $data = OrderBanquetPayment::get($get['id']);
        $this->assign('data', $data);

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/confirm/banquet_payment_accounting';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/confirm/banquet_payment_accounting';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/confirm/banquet_payment_accounting';
        }
        return $this->fetch($view);
    }

    # 获取婚宴付款信息--财务审核
    public function checkBanquetPaymentFiance()
    {
        $get = Request::param();
        $banquetPayment = OrderBanquetPayment::get($get['id']);
        $this->assign('data', $banquetPayment);
        $order = \app\common\model\Order::get($banquetPayment->order_id)->getData();

        ## 获取酒店信息
        $hotel = \app\common\model\Store::get($order['hotel_id']);
        $this->assign('hotel', $hotel);

        ## 获取婚宴付款信息
        $data = OrderBanquetPayment::get($get['id']);
        $this->assign('data', $data);

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/confirm/banquet_payment_fiance';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/confirm/banquet_payment_fiance';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/confirm/banquet_payment_fiance';
        }
        return $this->fetch($view);
    }

    # 婚宴付款信息--出纳审核
    public function checkBanquetPaymentCashier()
    {
        $get = Request::param();
        $banquetPayment = OrderBanquetPayment::get($get['id']);
        $this->assign('data', $banquetPayment);
        $order = \app\common\model\Order::get($banquetPayment->order_id)->getData();

        ## 获取酒店信息
        $hotel = \app\common\model\Store::get($order['hotel_id']);
        $this->assign('hotel', $hotel);

        ## 获取婚宴付款信息
        $data = OrderBanquetPayment::get($get['id']);
        $this->assign('data', $data);

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/confirm/banquet_payment_cashier';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/confirm/banquet_payment_cashier';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/confirm/banquet_payment_cashier';
        }
        return $this->fetch($view);
    }

    public function doEditBanquetPayment()
    {
        $request = Request::param();
        if(!empty($request['id'])) {
            $action = '更新';
            $model = OrderBanquetPayment::get($request['id']);
        } else {
            $action = '添加';
            $model = new OrderBanquetPayment();
        }

        $result = $model->save($request);
        if($result) {
            $order = \app\common\model\Order::get($request['order_id']);
            if(isset($request['check_status_payment_account'])) {
                $order->save(['check_status_payment_account' => $request['check_status_payment_account']]);
            } else if (isset($request['check_status_payment_fiance'])) {
                $order->save(['check_status_payment_fiance' => $request['check_status_payment_fiance']]);
            } else if (isset($request['check_status_payment_cashier'])){
                $order->save(['check_status_payment_cashier' => $request['check_status_payment_cashier']]);
            }
            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    # 创建婚庆支付信息
    public function createWeddingPayment()
    {
        $get = Request::param();
        $order = \app\common\model\Order::get($get['order_id'])->getData();

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/create/wedding_payment';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/create/wedding_payment';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/create/wedding_payment';
        }
        return $this->fetch($view);
    }

    # 编辑婚庆信息
    public function editWeddingPayment()
    {
        $get = Request::param();
        $weddingPayment = OrderWeddingPayment::get($get['id']);
        $this->assign('data', $weddingPayment);
        $order = \app\common\model\Order::get($weddingPayment->order_id)->getData();

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/edit/wedding_payment';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/edit/wedding_payment';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/edit/wedding_payment';
        }
        return $this->fetch($view);
    }

    # 会计审核婚庆支付信息
    public function checkWeddingPaymentAccounting()
    {
        $get = Request::param();
        $weddingPayment = OrderWeddingPayment::get($get['id']);
        $this->assign('data', $weddingPayment);
        $order = \app\common\model\Order::get($weddingPayment->order_id)->getData();

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/confirm/wedding_payment_accounting';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/confirm/wedding_payment_accounting';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/confirm/wedding_payment_accounting';
        }
        return $this->fetch($view);
    }

    # 财务审核婚庆支付信息
    public function checkWeddingPaymentFiance()
    {
        $get = Request::param();
        $weddingPayment = OrderWeddingPayment::get($get['id']);
        $this->assign('data', $weddingPayment);
        $order = \app\common\model\Order::get($weddingPayment->order_id)->getData();

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/confirm/wedding_payment_fiance';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/confirm/wedding_payment_fiance';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/confirm/wedding_payment_fiance';
        }
        return $this->fetch($view);
    }

    # 出纳审核婚庆支付信息
    public function checkWeddingPaymentCashier()
    {
        $get = Request::param();
        $weddingPayment = OrderWeddingPayment::get($get['id']);
        $this->assign('data', $weddingPayment);
        $order = \app\common\model\Order::get($weddingPayment->order_id)->getData();

        $this->assign('order', $order);
        if($order['news_type'] == '0') { // 婚宴订单
            $view = 'order/banquet/confirm/wedding_payment_cashier';
        } else if ($order['news_type'] == 1) { // 婚庆客资
            $view = 'order/wedding/confirm/wedding_payment_cashier';
        } else if ($order['news_type'] == 2) { // 一站式客资
            $view = 'order/entire/confirm/wedding_payment_cashier';
        }
        return $this->fetch($view);
    }

    # 执行编辑婚庆信息逻辑
    public function doEditWeddingPayment()
    {
        $request = Request::param();
        if(!empty($request['id'])) {
            $action = '更新';
            $model = OrderWeddingPayment::get($request['id']);
        } else {
            $action = '添加';
            $model = new OrderWeddingPayment();
        }

        $result = $model->save($request);
        if($result) {
            $order = \app\common\model\Order::get($request['order_id']);
            if(isset($request['check_status_payment_account'])) {
                $order->save(['check_status_payment_account' => $request['check_status_payment_account']]);
            } else if (isset($request['check_status_payment_fiance'])) {
                $order->save(['check_status_payment_fiance' => $request['check_status_payment_fiance']]);
            } else if (isset($request['check_status_payment_cashier'])){
                $order->save(['check_status_payment_cashier' => $request['check_status_payment_cashier']]);
            }
            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    # 来源审核视图
    public function sourceConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();
        $order = $this->formatOrderDate($order);
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

        $selectedWeddingDevices = json_decode($wedding['wedding_device'], true);
        if(!is_array($selectedWeddingDevices)) $selectedWeddingDevices = [];
        $this->assign('selectedWeddingDevices', $selectedWeddingDevices);

        $order = $this->formatOrderDate($order);
        $this->assign('data', $order);

        ## 获取客资信息
        $member = Member::getByMobile($order['mobile']);
        $this->assign('member', $member);


        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/entire/confirm/contract_score');
    }

    # 合同（财务）审核
    public function fianceConfirm()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id'])->getData();

        switch ($order['news_type'])
        {
            ### 婚宴信息
            case 0:
                #### 获取婚宴订单信息
                $where = [];
                $where['pid'] = 0;
                $where['order_id'] = $get['id'];
                $banquet = OrderBanquet::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $banquet);
                #### 获取婚宴二销订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $banquetOrders = OrderBanquetSuborder::where($where)->select();
                $this->assign('banquetOrders', $banquetOrders);

                #### 获取婚宴收款信息
                $receivables = OrderBanquetReceivables::where('order_id', '=', $get['id'])->select();
                $this->assign('receivables', $receivables);
                #### 获取婚宴付款信息
                $banquetPayments = OrderBanquetPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('banquetPayments', $banquetPayments);
                break;

            ### 婚庆信息
            case 1:
                #### 获取婚庆订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $wedding = OrderWedding::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $wedding);
                $selectedWeddingDevices = json_decode($wedding['wedding_device'], true);
                if(!is_array($selectedWeddingDevices)) $selectedWeddingDevices = [];
                $this->assign('selectedWeddingDevices', $selectedWeddingDevices);

                #### 获取婚宴二销订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $weddingOrders = OrderWeddingSuborder::where($where)->select();
                $this->assign('weddingOrders', $weddingOrders);
                #### 获取婚庆收款信息
                $receivables = OrderWeddingReceivables::where('order_id', '=', $get['id'])->select();
                $this->assign('receivables', $receivables);
                #### 获取婚庆付款信息
                $weddingPayments = OrderWeddingPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('weddingPayments', $weddingPayments);
                break;

            ### 一站式信息
            case 2:
                #### 获取婚宴订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $banquet = OrderBanquet::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $banquet);
                #### 获取婚宴二销订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $banquetOrders = OrderBanquetSuborder::where($where)->select();
                $this->assign('banquetOrders', $banquetOrders);

                #### 获取婚庆订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $wedding = OrderWedding::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $wedding);
                $selectedWeddingDevices = json_decode($wedding['wedding_device'], true);
                if(!is_array($selectedWeddingDevices)) $selectedWeddingDevices = [];
                $this->assign('selectedWeddingDevices', $selectedWeddingDevices);
                #### 获取婚宴二销订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $weddingOrders = OrderWeddingSuborder::where($where)->select();
                $this->assign('weddingOrders', $weddingOrders);


                #### 获取婚宴收款信息
                $receivables = OrderBanquetReceivables::where('order_id', '=', $get['id'])->select();
                $this->assign('receivables', $receivables);
                #### 获取婚宴付款信息
                $banquetPayments = OrderBanquetPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('banquetPayments', $banquetPayments);
                #### 获取婚庆付款信息
                $weddingPayments = OrderWeddingPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('weddingPayments', $weddingPayments);
                break;

            default:
                #### 获取婚宴订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $where['pid'] = 0;
                $banquet = OrderBanquet::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $banquet);
                #### 获取婚宴二销订单信息
                $where = [];
                $where['pid'] = ['neq', 0];
                $where['order_id'] = $get['id'];
                $banquetOrders = OrderBanquet::where($where)->select();
                $this->assign('banquetOrders', $banquetOrders);

                #### 获取婚庆订单信息
                $where = [];
                $where['order_id'] = $get['id'];
                $where['pid'] = 0;
                $wedding = OrderWedding::where($where)->field('id', true)->find()->getData();
                $order = array_merge($order, $wedding);
                #### 获取婚宴二销订单信息
                $where = [];
                $where['pid'] = ['neq', 0];
                $where['order_id'] = $get['id'];
                $weddingOrders = OrderWedding::where($where)->select();
                $this->assign('weddingOrders', $weddingOrders);

                #### 获取婚宴收款信息
                $receivables = OrderBanquetReceivables::where('order_id', '=', $get['id'])->select();
                $this->assign('receivables', $receivables);
                #### 获取婚宴付款信息
                $banquetPayments = OrderBanquetPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('banquetPayments', $banquetPayments);
                #### 获取婚庆付款信息
                $weddingPayments = OrderWeddingPayment::where('order_id', '=', $get['id'])->select();
                $this->assign('weddingPayments', $weddingPayments);
        }

        $order = $this->formatOrderDate($order);
        $this->assign('data', $order);

        ##　获取客资分配信息
        $allocate = MemberAllocate::where('id', '=', $order['member_allocate_id'])->find();
        $this->assign('allocate', $allocate);

        ## 获取客户信息
        $member = Member::get($order['member_id']);
        $this->assign('member', $member);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        if($allocate->news_type == '0') { // 婚宴订单
            $view = 'order/banquet/confirm/contract_fiance';
        } else if ($allocate->news_type == 1) { // 婚庆客资
            $view = 'order/wedding/confirm/contract_fiance';
        } else if ($allocate->news_type == 2) { // 一站式客资
            $view = 'order/entire/confirm/contract_fiance';
        } else {
            $view = 'order/entire/confirm/contract_fiance';
        }
        return $this->fetch($view);
    }

    # 来源-积分-合同审核确认，执行逻辑
    public function doConfirm()
    {
        $params = Request::param();

        ## 获取订单信息
        $order = \app\common\model\Order::get($params['id']);

        $result = $order->save($params);
        if($result) {
            $json = ['code' => '200', 'msg' => '完成审核是否继续?'];
        } else {
            $json = ['code' => '500', 'msg' => '完成失败是否继续?'];
        }

        return json($json);
    }

    # 获取订单列表
    private function _getOrderList($get, $statusField='check_status_source')
    {
        $config = [
            'page' => $get['page']
        ];
        $map = Search::order($this->user, $get);
        ## 关联审核状态
        if($statusField == 'index') {

        } else if ($statusField == 'complete') {

        } else {
            $statuses = [
                'check_status_source',
                'check_status_score',
                'check_status_contract_fiance',
                'check_status_receivables_cashier',
                'check_status_payment_account',
                'check_status_payment_fiance',
                'check_status_payment_cashier'
            ];

            foreach ($statuses as $value) {
                if (isset($get[$value])) {
                    $map[] = [$value, '=', $get[$value]];
                }
            }
        }

        if($statusField == 'check_status_score') {
            $map[] = ['score', '<>', ''];
        }

        $list = model('order')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
        $data = $list->getCollection();

        $users = \app\common\model\User::getUsers();
        $halls = BanquetHall::getBanquetHalls();

        if ($statusField == 'index') {
            $statusTexts = ['待审核', '已通过', '已驳回'];
            foreach ($data as $key => &$value) {
                $value['check_status_source'] = $statusTexts[$value['check_status_source']];
                $value['check_status_score'] = $statusTexts[$value['check_status_score']];
                $value['check_status_contract_fiance'] = $statusTexts[$value['check_status_contract_fiance']];
                $value['check_status_receivables_cashier'] = $statusTexts[$value['check_status_receivables_cashier']];
                $value['check_status_payment_account'] = $statusTexts[$value['check_status_payment_account']];
                $value['check_status_payment_fiance'] = $statusTexts[$value['check_status_payment_fiance']];
                $value['check_status_payment_cashier'] = $statusTexts[$value['check_status_payment_cashier']];
                $value['source_id'] = isset($this->sources[$value['source_id']]) ? $this->sources[$value['source_id']]['title'] : '-';
                $value['hotel_id'] = isset($this->hotels[$value['hotel_id']]) ? $this->hotels[$value['hotel_id']]['title'] : '-';
                $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '-';
                $value['salesman'] = isset($users[$value['salesman']]) ? $users[$value['salesman']]['realname'] : '-';
            }
        } else if($statusField == 'complete') {
            foreach ($data as $key => &$value) {
                $value['source_id'] = isset($this->sources[$value['source_id']]) ? $this->sources[$value['source_id']]['title'] : '-';
                $value['hotel_id'] = isset($this->hotels[$value['hotel_id']]) ? $this->hotels[$value['hotel_id']]['title'] : '-';
                $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '-';
                $value['salesman'] = isset($users[$value['salesman']]) ? $users[$value['salesman']]['realname'] : '-';
            }
        } else {
            $statusTexts = ['待审核', '已通过', '已驳回'];
            foreach ($data as $key => &$value) {
                $statusIndex = $value[$statusField];
                $value['confirm_status'] = $statusTexts[$statusIndex];
                $value['source_id'] = isset($this->sources[$value['source_id']]) ? $this->sources[$value['source_id']]['title'] : '-';
                $value['hotel_id'] = isset($this->hotels[$value['hotel_id']]) ? $this->hotels[$value['hotel_id']]['title'] : '-';
                $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '-';
                $value['salesman'] = isset($users[$value['salesman']]) ? $users[$value['salesman']]['realname'] : '-';
            }
        }
        $count = $list->total();

        return ['data' => $data, 'count' => $count];
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
            case 10: // 来源审核角色ID
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


    private function getColsFile($aciton='index') {
        switch ($this->user['role_id']) {
            case 10: // 来源审核角色ID
            case 51: // 积分审核角色ID
                $colsfile = 'cols_index_no_functions';
                break;
            case 1:
            case 29: // 财务角色ID
            case 33: // 出纳角色ID
            case 34: // 会计角色Id
                $colsfile = 'cols_index';
                break;
            default :
                $colsfile = 'cols_index';
        }
        $this->assign('colsfile', $colsfile);
    }

    private function formatOrderDate($order) {
        isset($order['sign_date']) && $order['sign_date'] = date('Y-m-d', $order['sign_date']);
        isset($order['event_date']) && $order['event_date'] = date('Y-m-d', $order['event_date']);
        isset($order['earnest_money_date']) && $order['earnest_money_date'] = date('Y-m-d', $order['earnest_money_date']);
        isset($order['middle_money_date']) && $order['middle_money_date'] = date('Y-m-d', $order['middle_money_date']);
        isset($order['tail_money_date']) && $order['tail_money_date'] = date('Y-m-d', $order['tail_money_date']);
        isset($order['banquet_income_date']) && $order['banquet_income_date'] = date('Y-m-d', $order['banquet_income_date']);
        isset($order['banquet_apply_pay_date']) && $order['banquet_apply_pay_date'] = date('Y-m-d', $order['banquet_apply_pay_date']);
        isset($order['wedding_apply_pay_date']) && $order['wedding_apply_pay_date'] = date('Y-m-d', $order['wedding_apply_pay_date']);
        isset($order['banquet_income_real_date']) && $order['banquet_income_real_date'] = date('Y-m-d', $order['banquet_income_real_date']);

        return $order;
    }
}