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
            !empty($value['bridegroom_mobile']) && $value['bridegroom_mobile'] = substr_replace($value['bridegroom_mobile'], '***', 3, 3);;
            !empty($value['bride_mobile']) && $value['bride_mobile'] = substr_replace($value['bride_mobile'], '***', 3, 3);;
            $value['source_id'] = isset($this->sources[$value['source_id']]) ? $this->sources[$value['source_id']]['title'] : '-';
            $value['hotel_id'] = isset($this->hotels[$value['hotel_id']]) ? $this->hotels[$value['hotel_id']]['title'] : '-';
            $value['salesman'] = isset($users[$value['salesman']]) ? $users[$value['salesman']]['realname'] : '-';
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