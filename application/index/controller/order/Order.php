<?php

namespace app\index\controller\order;

use app\common\model\Audit;
use app\common\model\BanquetHall;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\OrderBanquet;
use app\common\model\OrderBanquetPayment;
use app\common\model\OrderBanquetReceivables;
use app\common\model\OrderBanquetSuborder;
use app\common\model\OrderCar;
use app\common\model\OrderConfirm;
use app\common\model\OrderD3;
use app\common\model\OrderDessert;
use app\common\model\OrderEntire;
use app\common\model\OrderHotelItem;
use app\common\model\OrderHotelProtocol;
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
use app\common\model\UserAuth;
use app\index\controller\Backend;
use think\facade\Request;

class Order extends Backend
{
    protected $hotels = [];
    protected $sources = [];
    protected $suppliers = [];
    protected $weddingDevices = [];
    protected $weddingCategories = [];
    protected $brands = [];
    protected $confirmProjectStatusList = [0 => '待审核', 1 => '审核中', 2 => '审核通过', 3 => '审核驳回', 13 => '审核撤销'];
    protected $confirmStatusList = [0 => '待审核', 1 => '审核中', 2 => '审核通过', 3 => '审核驳回', 13 => '审核撤销'];
    protected $cooperationModes = [1 => '返佣单', 2 => '代收代付', 3 => '代收代付+返佣单', 4 => '一单一议'];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Order();

        // 获取系统来源,酒店列表,意向状态
        $this->assign('payments', $this->payments);
        $this->assign('paymentTypes', $this->paymentTypes);
        $this->assign('confirmStatusList', $this->confirmStatusList);
        $this->assign('confirmProjectStatusList', $this->confirmProjectStatusList);
        $this->assign('newsTypes', $this->newsTypes);
        $this->assign('cooperationModes', $this->cooperationModes);

        $staffes = User::getUsersInfoByDepartmentId($this->user['department_id']);
        $this->assign('staffes', $staffes);

        $this->sources = \app\common\model\Source::getSources();
        $this->assign('sources', $this->sources);

        ## 获取所有品牌、公司
        $this->brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $this->brands);

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

        ## led列表
        $ledList = \app\common\model\Led::getList();
        $this->assign('ledList', $ledList);

        ## 3d列表
        $d3List = \app\common\model\D3::getList();
        $this->assign('d3List', $d3List);
    }

    ## 我的订单
    public function mine()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            // $get['company_id'] = 25;
            $userAuth = UserAuth::getUserLogicAuth($this->user['id']);
            $companyIds = empty($userAuth['store_ids']) ? [] : explode(',', $userAuth['store_ids']);

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

    // lk
    public function lk()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $get['company_id'] = 27;
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

        return $this->fetch('order/create/main');
    }

    # 创建订单逻辑
    public function doCreateOrder()
    {
        $request = Request::param();
        $OrderModel = new \app\common\model\Order();
        $OrderModel->allowField(true)->save($request);
        $request['order_id'] = $OrderModel->id;
        $request['operate_id'] = $this->user['id'];
        $request['user_id'] = $this->user['id'];
        ## banquet message
        if (!empty($request['wedding_total'])) {
            $BanquetModel = new OrderBanquet();
            $BanquetModel->allowField(true)->save($request);
        }

        ## wedding message
        if (!empty($request['banquet_totals'])) {
            $weddingModel = new OrderWedding();
            // get wedding devices
            $weddingModel->allowField(true)->save($request);
        }

        ## 婚车主车
        if (!empty($request['master_car_id'])) {
            $row = [];
            $row['operate_id'] = $this->user['id'];
            $row['order_id'] = $request['order_id'];
            $row['company_id'] = $request['car_company_id'];
            $row['is_master'] = 1;
            $row['is_suborder'] = 0;
            $row['car_id'] = $request['master_car_id'];
            $row['car_price'] = $request['master_car_price'];
            $row['car_amount'] = $request['master_car_amount'];
            $row['service_hour'] = $request['service_hour'];
            $row['service_distance'] = $request['service_distance'];
            $row['car_contact'] = $request['car_contact'];
            $row['car_mobile'] = $request['car_mobile'];
            $row['arrive_time'] = $request['arrive_time'];
            $row['arrive_address'] = $request['arrive_address'];
            $row['car_remark'] = $request['master_car_remark'];
            $row['create_time'] = time();
            $row['salesman'] = $row['car_salesman'];
            $row['company_id'] = $row['car_company_id'];

            $carModel = new OrderCar();
            $carModel->allowField(true)->save($row);
        }

        ## 婚车跟车
        if (!empty($request['slave_car_id'])) {
            $row = [];
            $row['operate_id'] = $this->user['id'];
            $row['order_id'] = $request['order_id'];
            $row['company_id'] = $request['car_company_id'];
            $row['is_master'] = 0;
            $row['is_suborder'] = 0;
            $row['car_id'] = $request['slave_car_id'];
            $row['car_price'] = $request['slave_car_price'];
            $row['car_amount'] = $request['slave_car_amount'];
            $row['service_hour'] = $request['service_hour'];
            $row['service_distance'] = $request['service_distance'];
            $row['car_contact'] = $request['car_contact'];
            $row['car_mobile'] = $request['car_mobile'];
            $row['arrive_time'] = $request['arrive_time'];
            $row['arrive_address'] = $request['arrive_address'];
            $row['car_remark'] = $request['slave_car_remark'];
            $row['create_time'] = time();
            $row['salesman'] = $row['car_salesman'];
            $row['company_id'] = $row['car_company_id'];

            $carModel = new OrderCar();
            $carModel->allowField(true)->save($row);
        }

        ## 喜糖
        if (!empty($request['sugar_id'])) {
            $sugarModel = new OrderSugar();
            // get wedding devices

            $request['salesman'] = $request['sugar_salesman'];
            $sugarModel->allowField(true)->save($request);
        }

        ## 酒水
        if (!empty($request['wine_id'])) {
            $wineModel = new OrderWine();
            // get wedding devices
            $request['salesman'] = $request['wine_salesman'];
            $wineModel->allowField(true)->save($request);
        }

        ## 灯光
        if (!empty($request['light_id'])) {
            $lightModel = new OrderLight();
            // get wedding devices
            $request['salesman'] = $request['light_salesman'];
            $lightModel->allowField(true)->save($request);
        }

        ## 点心
        if (!empty($request['dessert_id'])) {
            $dessertModel = new OrderDessert();
            // get wedding devices
            $request['salesman'] = $request['dessert_salesman'];
            $dessertModel->allowField(true)->save($request);
        }

        ## led
        if (!empty($request['led_id'])) {
            $ledModel = new OrderLed();
            // get wedding devices
            $request['salesman'] = $request['led_salesman'];
            $ledModel->allowField(true)->save($request);
        }

        ## 3D
        if (!empty($request['d3_id'])) {
            $d3Model = new OrderD3();
            // get wedding devices
            $request['salesman'] = $request['d3_salesman'];
            $d3Model->allowField(true)->save($request);
            echo $d3Model->getLastSql();
        }

        return json(['code' => '200', 'msg' => '创建成功', 'redirect' => 'tab']);
    }

    # 编辑订单视图
    public function editOrder()
    {
        $get = Request::param();
        if (empty($get['id'])) return false;
        $order = \app\common\model\Order::get($get['id']);
        if (empty($this->user['sale']) && $order->salesman > 0) {
            $sale = User::getUser($order->salesman);
            $order->sale = $sale['realname'];
        }

        #### 获取婚宴订单信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $banquet = OrderBanquet::where($where)->order('id desc')->find();
        $this->assign('banquet', $banquet);

        #### 酒店服务项目
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $hotelItem = OrderHotelItem::where($where)->order('id desc')->find();
        $this->assign('hotelItem', $hotelItem);

        #### 获取婚宴二销订单信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $banquetOrders = OrderBanquetSuborder::where($where)->select();
        $this->assign('banquetOrders', $banquetOrders);
        if ($banquetOrders->isEmpty()) {
            $banquetOrderArr = [];
        } else {
            $banquetOrderArr = $banquetOrders->toArray();
        }

        #### 获取婚宴收款信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $receivables = OrderBanquetReceivables::where($where)->select();
        foreach ($receivables as $key => &$row) {
            $contractImg = !empty($row['contract_img']) ? explode(',', $row['contract_img']) : [];
            $receiptImg = !empty($row['receipt_img']) ? explode(',', $row['receipt_img']) : [];
            $noteImg = !empty($row['note_img']) ? explode(',', $row['note_img']) : [];
            $photos = array_merge($contractImg, $receiptImg, $noteImg);
            $images = [];
            foreach ($photos as $key => $val) {
                $images[$key]['alt'] = '';
                $images[$key]['pid'] = $order['id'];
                $images[$key]['src'] = $val;
                $images[$key]['thumb'] = $val;
            }
            $imagesFormat = [
                'id' => $order['id'],
                'title' => '凭证',
                'start' => 0,
                'data' => $images
            ];
            $row['images'] = $imagesFormat;
        }
        $this->assign('banquetReceivables', $receivables);

        #### 获取婚宴付款信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $banquetPayments = OrderBanquetPayment::where($where)->select();
        foreach ($banquetPayments as $key => &$row) {
            $receiptImg = !empty($row['receipt_img']) ? explode(',', $row['receipt_img']) : [];
            $noteImg = !empty($row['note_img']) ? explode(',', $row['note_img']) : [];
            $photos = array_merge($receiptImg, $noteImg);
            $images = [];
            foreach ($photos as $key => $val) {
                $images[$key]['alt'] = '';
                $images[$key]['pid'] = $order['id'];
                $images[$key]['src'] = $val;
                $images[$key]['thumb'] = $val;
            }
            $imagesFormat = [
                'id' => $order['id'],
                'title' => '凭证',
                'start' => 0,
                'data' => $images
            ];
            $row['images'] = $imagesFormat;
        }
        $this->assign('banquetPayments', $banquetPayments);

        #### 获取酒店协议信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $hotelProtocol = OrderHotelProtocol::where($where)->select();
        $this->assign('hotelProtocol', $hotelProtocol);

        #### 获取婚庆订单信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $wedding = OrderWedding::where($where)->order('id desc')->find();
        $this->assign('wedding', $wedding);

        #### 获取婚宴二销订单信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $weddingOrders = OrderWeddingSuborder::where($where)->select();
        if ($weddingOrders->isEmpty()) {
            $weddingOrderArr = [];
        } else {
            $weddingOrderArr = $weddingOrders->toArray();
        }
        $this->assign('weddingOrders', $weddingOrders);

        #### 获取婚宴收款信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $weddingReceivables = OrderWeddingReceivables::where($where)->select();
        foreach ($weddingReceivables as $key => &$row) {
            $contractImg = !empty($row['contract_img']) ? explode(',', $row['contract_img']) : [];
            $receiptImg = !empty($row['receipt_img']) ? explode(',', $row['receipt_img']) : [];
            $noteImg = !empty($row['note_img']) ? explode(',', $row['note_img']) : [];
            $photos = array_merge($contractImg, $receiptImg, $noteImg);
            $images = [];
            foreach ($photos as $key => $val) {
                $images[$key]['alt'] = '';
                $images[$key]['pid'] = $order['id'];
                $images[$key]['src'] = $val;
                $images[$key]['thumb'] = $val;
            }
            $imagesFormat = [
                'id' => $order['id'],
                'title' => '凭证',
                'start' => 0,
                'data' => $images
            ];
            $row['images'] = $imagesFormat;
        }
        $this->assign('weddingReceivables', $weddingReceivables);

        #### 获取婚庆付款信息
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $weddingPayments = OrderWeddingPayment::where($where)->select();
        foreach ($weddingPayments as $key => &$row) {
            $receiptImg = !empty($row['receipt_img']) ? explode(',', $row['receipt_img']) : [];
            $noteImg = !empty($row['note_img']) ? explode(',', $row['note_img']) : [];
            $photos = array_merge($receiptImg, $noteImg);
            $images = [];
            foreach ($photos as $key => $val) {
                $images[$key]['alt'] = '';
                $images[$key]['pid'] = $order['id'];
                $images[$key]['src'] = $val;
                $images[$key]['thumb'] = $val;
            }
            $imagesFormat = [
                'id' => $order['id'],
                'title' => '凭证',
                'start' => 0,
                'data' => $images
            ];
            $row['images'] = $imagesFormat;
        }
        $this->assign('weddingPayments', $weddingPayments);

        #### 婚车
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $car = OrderCar::where($where)->select();
        $this->assign('car', $car);

        #### 喜糖
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $sugar = OrderSugar::where($where)->select();
        $this->assign('sugar', $sugar);

        #### 酒水
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $wine = OrderWine::where($where)->select();
        $this->assign('wine', $wine);

        #### 灯光
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $light = OrderLight::where($where)->select();
        $this->assign('light', $light);

        #### 点心
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $dessert = OrderDessert::where($where)->select();
        $this->assign('dessert', $dessert);

        #### LED
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $led = OrderLed::where($where)->select();
        $this->assign('led', $led);


        #### 3D
        $where = [];
        $where[] = ['order_id', '=', $get['id']];
        $where[] = ['item_check_status', 'in', [0, 1, 2]];
        $d3 = OrderD3::where($where)->select();
        $this->assign('d3', $d3);

        ## 获取客户信息
        $member = Member::get($order->member_id);
        $this->assign('member', $member);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);

        ## 合同
        if (!empty($order['image'])) {
            $order['image'] = explode(',', $order['image']);
        } else {
            $order['image'] = [];
        }
        ## 收据
        if (!empty($order['receipt_img'])) {
            $order['receipt_img'] = explode(',', $order['receipt_img']);
        } else {
            $order['receipt_img'] = [];
        }
        ## 小票
        if (!empty($order['note_img'])) {
            $order['note_img'] = explode(',', $order['note_img']);
        } else {
            $order['note_img'] = [];
        }
        $this->assign('data', $order);
        $photos = [];
        $images = array_merge($order['image'], $order['receipt_img'], $order['note_img']);
        foreach ($images as $key => $val) {
            $photos[$key]['alt'] = '';
            $photos[$key]['pid'] = $order['id'];
            $photos[$key]['src'] = $val;
            $photos[$key]['thumb'] = $val;
        }
        $photosData = [
            'id' => $order['id'],
            'title' => '订单凭证',
            'start' => 0,
            'data' => $photos
        ];
        $this->assign('photosData', $photosData);
        $this->assign('images', $images);

        // 统计
        ### 婚庆总计
        if (empty($weddingOrderArr)) {
            $count['wedding_totals'] = 0;
        } else {
            $weddingTotalsArr = array_column($weddingOrderArr, 'wedding_totals');
            $count['wedding_totals'] = array_sum($weddingTotalsArr);
        }
        ### 婚宴总计
        if (empty($banquetOrderArr)) {
            $count['banquet_totals'] = 0;
        } else {
            $banquetTotalsArr = array_column($banquetOrderArr, 'banquet_totals');
            $count['banquet_totals'] = array_sum($banquetTotalsArr);
        }
        ### 订单综合
        $count['totals'] = $order['totals'];
        $count['customer_totals'] = $count['totals'] + $count['wedding_totals'] + $count['banquet_totals'];
        $this->assign('count', $count);

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
        $order->tail_money = $request['contract_totals'] * 0.2 + $banquetTotals + $weddingTotals;
        $order->totals = $request['contract_totals'] + $banquetTotals + $weddingTotals;
        unset($request['tail_money']);
        unset($request['totals']);
        $result2 = $order->save($request);
        return json(['code' => '200', 'msg' => '更新订单成功']);
    }

    # 查看订单信息
    public function showOrder()
    {
        $request = $this->request->param();
        $this->editOrder();

        $order = $this->model->where('id', '=', $request['id'])->find();
        if (empty($this->user['sale']) && $order->salesman > 0) {
            $sale = User::getUser($order->salesman);
            $order->sale = $sale['realname'];
        }
        $audit = Audit::where('company_id', '=', $order->company_id)->find();

        $config = config();
        $sequences = $config['crm']['check_sequence'];
        if (!empty($sequences)) {
            $sequence = (array)json_decode($audit->content, true);
            foreach ($sequence as $key => &$row) {
                $row['title'] = $sequences[$key]['title'];
            }
            $this->assign('sequence', $sequence);
        }

        ## 合同
        if (!empty($order['image'])) {
            $order['image'] = explode(',', $order['image']);
        } else {
            $order['image'] = [];
        }
        ## 收据
        if (!empty($order['receipt_img'])) {
            $order['receipt_img'] = explode(',', $order['receipt_img']);
        } else {
            $order['receipt_img'] = [];
        }
        ## 小票
        if (!empty($order['note_img'])) {
            $order['note_img'] = explode(',', $order['note_img']);
        } else {
            $order['note_img'] = [];
        }
        $this->assign('data', $order);
        $photos = [];
        $images = array_merge($order['image'], $order['receipt_img'], $order['note_img']);
        foreach ($images as $key => $val) {
            $photos[$key]['alt'] = '';
            $photos[$key]['pid'] = $order['id'];
            $photos[$key]['src'] = $val;
            $photos[$key]['thumb'] = $val;
        }
        $photosData = [
            'id' => $order['id'],
            'title' => '订单凭证',
            'start' => 0,
            'data' => $photos
        ];
        $this->assign('photosData', $photosData);
        $this->assign('images', $images);

        return $this->fetch('order/show/main');
    }

    public function deleteOrder($id)
    {
        $order = \app\common\model\Order::get($id);
        $result = $order->delete();
        if ($result) {
            $where = [];
            $where[] = ['order_id', '=', $id];
            ### 婚宴信息删除
            OrderBanquet::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            OrderBanquetReceivables::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            OrderBanquetPayment::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            OrderBanquetSuborder::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });

            ### 婚庆信息删除
            OrderWedding::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            OrderWeddingReceivables::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            OrderBanquetPayment::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            OrderBanquetSuborder::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });

            ### 酒店服务项目
            OrderHotelItem::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            ### 酒店协议
            OrderHotelProtocol::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            ### 删除婚车信息
            OrderCar::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            ### 删除喜糖
            OrderSugar::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            ### 删除酒水
            OrderWine::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            ### 删除点心
            OrderDessert::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            ### 删除灯光
            OrderLight::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            ### 删除Led
            OrderLed::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            ### 删除3D
            OrderD3::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });

            ### 删除审核
            OrderConfirm::destroy(function ($query) use ($id) {
                $query->where('order_id', '=', $id);
            });
            $arr = [
                'code' => '200',
                'msg' => '删除成功'
            ];
        } else {
            $arr = [
                'code' => '400',
                'msg' => '删除失败'
            ];
        }
        return json($arr);
    }


    # 获取订单列表
    private function _getOrderList($get, $statusField = 'check_status_source')
    {
        $config = [
            'page' => $get['page']
        ];

        if (is_array($get['company_id'])) {
            $map[] = ['company_id', 'in', $get['company_id']];
        } else if ($get['company_id'] > 0) {
            $map[] = ['company_id', '=', $get['company_id']];
        }

        if (isset($get['source']) && !empty($get['source'])) {
            $map[] = ['source_id', '=', $get['source']];
        }

        if (isset($get['hotel_id']) && !empty($get['hotel_id'])) {
            $map[] = ['hotel_id', '=', $get['hotel_id']];
        }

        if (isset($get['staff']) && $get['staff'] > 0) {
            $map[] = ['user_id', '=', $get['staff']];
        }

        if (isset($get['date_range']) && !empty($get['date_range']) && !empty($get['date_range_type'])) {
            $range = $this->getDateRange($get['date_range']);
            $map[] = [$get['date_range_type'], 'between', $range];
        }


        $model = model('order')->where($map);
        if (isset($get['mobile'])) {
            $model = $model->where('bridegroom_mobile|bride_mobile', 'like', "%{$get['mobile']}%");
        }

        $list = $model->order('id desc')->paginate($get['limit'], false, $config);
        $data = $list->getCollection();

        $users = \app\common\model\User::getUsers();
        foreach ($data as $key => &$value) {
            $companyId = $value->company_id;
            $value['company'] = $this->brands[$companyId]['title'];
            $checkStatus = $value->check_status;
            $value['check_status'] = $this->confirmStatusList[$checkStatus];
            !empty($value['bridegroom_mobile']) && $value['bridegroom_mobile'] = substr_replace($value['bridegroom_mobile'], '***', 3, 3);;
            !empty($value['bride_mobile']) && $value['bride_mobile'] = substr_replace($value['bride_mobile'], '***', 3, 3);;
            $value['source_id'] = isset($this->sources[$value['source_id']]) ? $this->sources[$value['source_id']]['title'] : '-';
            $value['hotel_id'] = isset($this->hotels[$value['hotel_id']]) ? $this->hotels[$value['hotel_id']]['title'] : '-';
            $value['salesman'] = isset($users[$value['salesman']]) ? $users[$value['salesman']]['realname'] : '-';
        }
        $count = $list->total();

        return ['data' => $data, 'count' => $count];
    }

    private function getColsFile($aciton = 'index')
    {
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

    private function formatOrderDate($order)
    {
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
            $order->save(['image' => $info->getPathname()]);

            $arr = [
                'code' => '200',
                'msg' => '上传成功',
                'image' => '/' . $info->getPathname()
            ];
        } else {
            $arr = [
                'code' => '500',
                'msg' => '上传失败'
            ];
        }

        return json($arr);
    }

    public function getDateRange($dateRange)
    {
        if ($dateRange == 'today') {

            $start = strtotime(date('Y-m-d'));
            $end = strtotime('tomorrow');
        } else {

            $range = explode('~', $dateRange);
            $range[0] = str_replace("+", "", trim($range[0]));
            $range[1] = str_replace("+", "", trim($range[1]));
            $start = strtotime($range[0]);
            $end = strtotime($range[1]) + 86400;
        }

        return [$start, $end];
    }
}