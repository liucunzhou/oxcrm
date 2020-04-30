<?php

namespace app\index\controller\order;

use app\common\model\BanquetHall;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\OrderBanquet;
use app\common\model\OrderBanquetPayment;
use app\common\model\OrderBanquetReceivables;
use app\common\model\OrderBanquetSuborder;
use app\common\model\OrderConfirm;
use app\common\model\OrderEntire;
use app\common\model\OrderWedding;
use app\common\model\OrderWeddingPayment;
use app\common\model\OrderWeddingReceivables;
use app\common\model\OrderWeddingSuborder;
use app\common\model\Search;
use app\common\model\User;
use app\index\controller\Backend;
use app\index\controller\Base;
use app\index\controller\organization\Audit;
use think\facade\Request;

class Confirm extends Backend
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
        $this->model = new OrderConfirm();

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
    }

    # 誉思
    public function index()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['company_id'] = 24;
            $list = $this->_getConfirmList($get);

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'data' => $list->getCollection(),
                'count' => $list->total()
            ];
            return json($result);

        } else {
            return $this->fetch('order/confirm/index');
        }
    }

    public function hs()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['company_id'] = 26;
            $list = $this->_getConfirmList($get);

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'data' => $list->getCollection(),
                'count' => $list->total()
            ];
            return json($result);

        } else {
            return $this->fetch('order/confirm/index');
        }
    }

    public function lk()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['company_id'] = 27;
            $list = $this->_getConfirmList($get);

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'data' => $list->getCollection(),
                'count' => $list->total()
            ];
            return json($result);

        } else {
            return $this->fetch('order/confirm/index');
        }
    }

    public function mangena()
    {
        if (Request::isAjax()) {
            $get = $this->request->param();
            $get['company_id'] = 24;
            $list = $this->_getConfirmList($get);

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'data' => $list->getCollection(),
                'count' => $list->total()
            ];
            return json($result);

        } else {
            return $this->fetch('order/confirm/index');
        }
    }

    # 查看订单信息
    public function showOrder()
    {
        $get = $this->request->param();
        $orderConfirm = $this->model->where('id', '=', $get['confirm_id'])->find();

        $get['id'] = $orderConfirm->order_id;
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

        #### 获取酒店协议信息
        $hotelProtocol = OrderHotelProtocol::where('order_id', '=', $get['id'])->select();
        $this->assign('hotelProtocol', $hotelProtocol);



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
        if($member) $this->assign('member', $member);

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);
        $this->assign('salesmans', $salesmans);
        $where = [];
        $where[] = ['company_id', '=', $order->company_id];
        $where[] = ['timing', '=', $orderConfirm->confirm_type];
        $audit = Audit::where($where)->find();

        $config = config();
        $sequences = $config['crm']['check_sequence'];
        if(!empty($sequences)) {
            $sequence = (array)json_decode($audit->content, true);
            foreach ($sequence as $key => &$row) {
                $where = [];

                $where[] = ['order_id', '=', $get['id']];
                $where[] = ['company_id', '=', $order->company_id];
                $where[] = ['timing', '=', $orderConfirm->confirm_type];
                $row['title'] = $sequences[$key]['title'];
                // $row['status'] = $sequence['']
            }
            $this->assign('sequence', $sequence);
        }

        return $this->fetch('order/show/main');
    }

    protected function _getConfirmList($get)
    {
        $config = [
            'page' => $get['page']
        ];
        $map = [];
        $map[] = ['company_id', '=', $get['company_id']];

        $list = $this->model->where($map)->order('id desc')->paginate($get['limit'], false, $config);

        $users = \app\common\model\User::getUsers();
        foreach ($list as $key => &$value) {
            $order = \app\common\model\Order::get($value['order_id']);
            $value['bridegroom_mobile'] = $order->bridegroom_mobile ? substr_replace($order->bridegroom_mobile, '***', 3, 3) : '-';
            $value['bride_mobile'] = $order->bride_mobile ? substr_replace($order->bride_mobile, '***', 3, 3) : '-';
            $value['user_id'] = isset($users[$value['user_id']]) ? $users[$value['user_id']]['realname'] : '-';
            $value['banquet_hall_name'] = $order->banquet_hall_name;
            $value['hotel_text'] = $order->hotel_text;
            $value['source_text'] = $order->source_text;
            $value['bridegroom'] = $order->bridegroom;
            $value['bride'] = $order->bride;
            $value['sign_date'] = $order->sign_date;
            $value['event_date'] = $order->event_date;
        }

        return $list;
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
}