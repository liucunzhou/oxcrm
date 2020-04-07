<?php

namespace app\index\controller\order;

use app\common\model\BanquetHall;
use app\common\model\OrderWeddingReceivables;
use app\common\model\User;
use app\index\controller\Backend;
use think\facade\Request;

class WeddingReceivable extends Backend
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


    # 创建婚庆收款信息
    public function create()
    {
        $params = $this->request->param();

        $order = \app\common\model\Order::get($params['order_id'])->getData();
        $this->assign('order', $order);
        return $this->fetch();
    }

    # 婚庆收款信息
    public function edit($id)
    {

        $banquetReceivable = OrderWeddingReceivables::get($id);
        $this->assign('data', $banquetReceivable);
        $order = \app\common\model\Order::get($banquetReceivable->order_id)->getData();

        ## 获取付款信息
        $data = OrderWeddingReceivables::get($id);
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function doEdit()
    {
        $request = Request::param();
        if(!empty($request['id'])) {
            $action = '更新';
            $model = OrderWeddingReceivables::get($request['id']);
        } else {
            $action = '添加';
            $model = new OrderWeddingReceivables();
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
}