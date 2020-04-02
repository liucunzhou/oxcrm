<?php

namespace app\index\controller\order;

use app\common\model\BanquetHall;
use app\common\model\Member;
use app\common\model\MemberAllocate;
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
use app\common\model\User;
use app\index\controller\Base;
use think\facade\Request;

class Confirm extends Base
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
}