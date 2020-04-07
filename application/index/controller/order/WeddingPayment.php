<?php

namespace app\index\controller\order;

use app\common\model\BanquetHall;
use app\common\model\OrderEntire;
use app\common\model\OrderWeddingPayment;
use app\common\model\User;
use app\index\controller\Backend;
use think\facade\Request;

class WeddingPayment extends Backend
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


    # 创建婚庆支付信息
    public function create()
    {
        $get = Request::param();
        $order = \app\common\model\Order::get($get['order_id'])->getData();
        $this->assign('order', $order);
        return $this->fetch();
    }

    # 编辑婚庆信息
    public function edit($id)
    {
        $get = Request::param();
        $weddingPayment = OrderWeddingPayment::get($get['id']);
        $this->assign('data', $weddingPayment);
        $order = \app\common\model\Order::get($weddingPayment->order_id)->getData();
        $this->assign('order', $order);
        return $this->fetch();
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
    public function doEdit()
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
}