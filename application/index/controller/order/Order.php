<?php

namespace app\index\controller\order;

use app\common\model\BanquetHall;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\OrderBanquet;
use app\common\model\OrderBanquetPayment;
use app\common\model\OrderBanquetReceivables;
use app\common\model\OrderBanquetSuborder;
use app\common\model\OrderCar;
use app\common\model\OrderD3;
use app\common\model\OrderDessert;
use app\common\model\OrderEntire;
use app\common\model\OrderHotelItem;
use app\common\model\OrderLed;
use app\common\model\OrderLight;
use app\common\model\OrderSugar;
use app\common\model\OrderWedding;
use app\common\model\OrderWeddingPayment;
use app\common\model\OrderWeddingReceivables;
use app\common\model\OrderWeddingSuborder;
use app\common\model\OrderWine;
use app\common\model\Search;
use app\common\model\User;
use app\index\controller\Backend;
use think\facade\Request;

class Order extends Backend
{
    protected $hotels = [];
    protected $sources = [];
    protected $suppliers = [];
    protected $weddingDevices = [];
    protected $weddingCategories = [];
    protected $paymentTypes = [1=>'定金', 2=>'预付款', 3=>'尾款', 4=>'其他'];
    protected $payments = [1=>'现金', 2=>'POS机', 3=>'微信', 4=>'支付宝'];
    protected $confirmStatusList = [0=>'待审核', 1=>'通过', 2=>'驳回'];
    protected $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];
    protected $cooperationModes = [1=>'返佣单',2=>'代收代付',3=>'代收代付+返佣单',4=>'一单一议'];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Order();

        // 获取系统来源,酒店列表,意向状态
        $this->assign('payments', $this->payments);
        $this->assign('paymentTypes', $this->paymentTypes);
        $this->assign('confirmStatusList', $this->confirmStatusList);
        $this->assign('newsTypes', $this->newsTypes);
        $this->assign('cooperationModes', $this->cooperationModes);

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

        ## 酒店服务项目
        $banquetHoteItems = \app\common\model\BanquetHotelItem::getList();
        $this->assign('banquetHoteItems', $banquetHoteItems);

        ## 酒店服务项目
        $cars = \app\common\model\Car::getList();
        $this->assign('cars', $cars);

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

        ## 汽车列表
        $carList = \app\common\model\Car::getList();
        $this->assign('carList', $carList);

        ## 酒水列表
        $wineList = \app\common\model\Wine::getList();
        $this->assign('wineList', $wineList);

        ## 喜糖列表
        $sugarList = \app\common\model\Sugar::getList();
        $this->assign('sugarList', $sugarList);

        ## 灯光列表
        $lightList = \app\common\model\Light::getList();
        $this->assign('lightList', $lightList);

        ## 点心列表
        $dessertList = \app\common\model\Dessert::getList();
        $this->assign('dessertList', $dessertList);

    }

    // 誉丝
    public function index()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $get['company_id'] = 25;
            $order = $this->_getOrderList($get, 'index');
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',

                'count' => $order['count'],
                'data' => $order['data']
            ];
            return json($result);
        } else {
            $this->getColsFile('index');
            $this->view->engine->layout(false);
            return $this->fetch('order/list/index');
        }
    }

    // 红丝
    public function hs()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $get['company_id'] = 26;
            $order = $this->_getOrderList($get, 'index');
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',

                'count' => $order['count'],
                'data' => $order['data']
            ];
            return json($result);
        } else {
            $this->getColsFile('index');
            $this->view->engine->layout(false);
            return $this->fetch('order/list/index');
        }
    }

    // 曼格纳
    public function mangena()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $get['company_id'] = 24;
            $order = $this->_getOrderList($get, 'index');
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',

                'count' => $order['count'],
                'data' => $order['data']
            ];
            return json($result);
        } else {
            $this->getColsFile('index');
            $this->view->engine->layout(false);
            return $this->fetch('order/list/index');
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
        $order = \app\common\model\Order::get($get['id']);
        $this->assign('data', $order);

        #### 获取婚宴订单信息
        $where = [];
        $where['order_id'] = $get['id'];
        $banquet = OrderBanquet::where($where)->order('id desc')->find();
        $this->assign('banquet', $banquet);

        #### 酒店服务项目
        $where = [];
        $where['order_id'] = $get['id'];
        $hotelItem = OrderHotelItem::where($where)->order('id desc')->find();
        $this->assign('hotelItem', $hotelItem);

        #### 获取婚宴二销订单信息
        $where = [];
        $where['order_id'] = $get['id'];
        $banquetOrders = OrderBanquetSuborder::where($where)->select();
        $this->assign('banquetOrders', $banquetOrders);

        #### 获取婚宴收款信息
        $receivables = OrderBanquetReceivables::where('order_id', '=', $get['id'])->select();
        $this->assign('banquetReceivables', $receivables);
        #### 获取婚宴付款信息
        $banquetPayments = OrderBanquetPayment::where('order_id', '=', $get['id'])->select();
        $this->assign('banquetPayments', $banquetPayments);


        #### 获取婚庆订单信息
        $where = [];
        $where['order_id'] = $get['id'];
        $wedding = OrderWedding::where($where)->order('id desc')->find();
        $this->assign('wedding', $wedding);
        if(!empty($wedding)) {
            $weddingData = $wedding->getData();
            $selectedWeddingDevices = json_decode($weddingData['wedding_device'], true);
            if (!is_array($selectedWeddingDevices)) $selectedWeddingDevices = [];
            $this->assign('selectedWeddingDevices', $selectedWeddingDevices);
        }
        #### 获取婚宴二销订单信息
        $where = [];
        $where['order_id'] = $get['id'];
        $weddingOrders = OrderWeddingSuborder::where($where)->select();
        $this->assign('weddingOrders', $weddingOrders);

        #### 获取婚宴收款信息
        $weddingReceivables = OrderWeddingReceivables::where('order_id', '=', $get['id'])->select();
        $this->assign('weddingReceivables', $weddingReceivables);
        #### 获取婚庆付款信息
        $weddingPayments = OrderWeddingPayment::where('order_id', '=', $get['id'])->select();
        $this->assign('weddingPayments', $weddingPayments);

        #### 婚车
        $where = [];
        $where['order_id'] = $get['id'];
        $car = OrderCar::where($where)->select();
        $this->assign('car', $car);

        #### 喜糖
        $where = [];
        $where['order_id'] = $get['id'];
        $sugar = OrderSugar::where($where)->select();
        $this->assign('sugar', $sugar);

        #### 酒水
        $where = [];
        $where['order_id'] = $get['id'];
        $wine = OrderWine::where($where)->select();
        $this->assign('wine', $wine);

        #### 灯光
        $where = [];
        $where['order_id'] = $get['id'];
        $light = OrderLight::where($where)->select();
        $this->assign('light', $light);

        #### 点心
        $where = [];
        $where['order_id'] = $get['id'];
        $dessert = OrderDessert::where($where)->select();
        $this->assign('dessert', $dessert);

        #### LED
        $where = [];
        $where['order_id'] = $get['id'];
        $led = OrderLed::where($where)->select();
        $this->assign('led', $led);


        #### 3D
        $where = [];
        $where['order_id'] = $get['id'];
        $d3 = OrderD3::where($where)->select();
        $this->assign('d3', $d3);


        ##　获取客资分配信息
        $allocate = MemberAllocate::where('id', '=', $order['member_allocate_id'])->find();
        $this->assign('allocate', $allocate);

        ## 获取客户信息
        $member = Member::get($order->member_id);
        if($member) $this->assign('customer', $member);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        return $this->fetch('order/edit/main');
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
        $order = $this->formatOrderDate($order);
        $this->assign('data', $order);

        #### 获取婚宴订单信息
        $where = [];
        $where['order_id'] = $get['id'];
        $banquet = OrderBanquet::where($where)->field('id', true)->find();
        $this->assign('banquet', $banquet);

        #### 获取婚宴二销订单信息
        $where = [];
        $where['order_id'] = $get['id'];
        $banquetOrders = OrderBanquetSuborder::where($where)->select();
        $this->assign('banquetOrders', $banquetOrders);
        #### 获取婚宴收款信息
        $banquetReceivables = OrderBanquetReceivables::where('order_id', '=', $get['id'])->select();
        $this->assign('banquetReceivables', $banquetReceivables);
        #### 获取婚宴付款信息
        $banquetPayments = OrderBanquetPayment::where('order_id', '=', $get['id'])->select();
        $this->assign('banquetPayments', $banquetPayments);

        #### 获取婚庆订单信息
        $where = [];
        $where['order_id'] = $get['id'];
        $wedding = OrderWedding::where($where)->field('id', true)->find();
        if(!empty($wedding)) {
            $selectedWeddingDevices = json_decode($wedding['wedding_device'], true);
            if (!is_array($selectedWeddingDevices)) $selectedWeddingDevices = [];
            $this->assign('selectedWeddingDevices', $selectedWeddingDevices);
            #### 获取婚宴二销订单信息
            $where = [];
            $where['order_id'] = $get['id'];
            $weddingOrders = OrderWeddingSuborder::where($where)->select();
            $this->assign('weddingOrders', $weddingOrders);
        }
        $this->assign('wedding', $wedding);
        #### 获取婚庆收款信息
        $weddingReceivables = OrderWeddingReceivables::where('order_id', '=', $get['id'])->select();
        $this->assign('weddingReceivables', $weddingReceivables);
        #### 获取婚庆付款信息
        $weddingPayments = OrderWeddingPayment::where('order_id', '=', $get['id'])->select();
        $this->assign('weddingPayments', $weddingPayments);

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

        return $this->fetch('order/show/main');
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
                !empty($value['bridegroom_mobile']) && $value['bridegroom_mobile'] = substr_replace($value['bridegroom_mobile'], '***', 3, 3);;
                !empty($value['bride_mobile']) && $value['bride_mobile'] = substr_replace($value['bride_mobile'], '***', 3, 3);;
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
                !empty($value['bridegroom_mobile']) && $value['bridegroom_mobile'] = substr_replace($value['bridegroom_mobile'], '***', 3, 3);;
                !empty($value['bride_mobile']) && $value['bride_mobile'] = substr_replace($value['bride_mobile'], '***', 3, 3);;
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

    public function upload()
    {
        $params = $this->request->param();
        $file = request()->file("file");
        $info = $file->move("uploads/order");
        if ($info) {
            $origin = $info->getInfo();
            $data = [];
            $data['order_id'] = $params['id'];
            $data['origin_file_name'] = $origin['name'];
            $data['new_file_name'] = $info->getFileName();
            $data['new_file_path'] = $info->getPathname();

            $where = [];
            $where['id'] = $params['id'];
            $order = $this->model->where($where)->find();
            $order->save(['image'=>$info->getPathname()]);

            $arr = [
                'code'  => '200',
                'msg'   => '上传成功',
                'image' => '/'.$info->getPathname()
            ];
        } else {
            $arr = [
                'code'  => '500',
                'msg'   => '上传失败'
            ];
        }

        return json($arr);
    }
}