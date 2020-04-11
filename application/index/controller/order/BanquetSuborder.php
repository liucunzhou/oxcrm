<?php

namespace app\index\controller\order;

use app\common\model\BanquetHall;
use app\common\model\OrderBanquetSuborder;
use app\common\model\OrderEntire;
use app\common\model\OrderWeddingSuborder;
use app\common\model\User;
use app\index\controller\Backend;
use think\facade\Request;

class BanquetSuborder extends Backend
{
    protected $hotels = [];
    protected $sources = [];
    protected $suppliers = [];
    protected $weddingDevices = [];
    protected $weddingCategories = [];
    protected $confirmStatusList = [0=>'待审核', 1=>'通过', 2=>'驳回'];

    protected function initialize()
    {
        parent::initialize();

        $this->model = new OrderBanquetSuborder();

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

    # 创建婚宴子合同
    public function create()
    {
        $get = Request::param();
        $order = \app\common\model\Order::get($get['order_id'])->getData();
        $this->assign('order', $order);

        return $this->fetch();
    }

    # 编辑婚宴子合同
    public function edit($id)
    {
        $get = Request::param();
        $suborder = OrderBanquetSuborder::get($get['id']);
        $this->assign('data', $suborder);

        return $this->fetch();
    }

    # 添加/编辑婚宴子合同
    public function doEdit()
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
}