<?php
namespace app\index\controller\planner;

use app\common\model\OrderWedding;
use app\common\model\UserAuth;
use app\index\controller\Backend;

class Planner extends Backend
{
    protected $hotels = [];
    protected $sources = [];
    protected $suppliers = [];
    protected $weddingDevices = [];
    protected $weddingCategories = [];
    protected $brands = [];
    protected $confirmStatusList = [0 => '待审核', 1 => '审核中', 2 => '审核通过', 3 => '审核驳回'];
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

        $staffes = \app\common\model\User::getUsersInfoByDepartmentId($this->user['department_id']);
        $this->assign('staffes', $staffes);

        $this->sources = \app\common\model\Source::getSources();
        $this->assign('sources', $this->sources);

        ## 获取所有品牌、公司
        $this->brands = \app\common\model\Brand::getBrands();
        $this->assign('brands', $this->brands);

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

    # 我的策划
    public function index()
    {
        if ($this->request->isAjax()) {
            $get = $this->request->param();
            $order = $this->_getOrderList($get);
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $order['count'],
                'data' => $order['data']
            ];
            return json($result);

        } else {
            return $this->fetch('planner/planner/index');
        }
    }

    #
    protected function _getOrderList($get)
    {
        $config = [
            'page' => $get['page']
        ];

        if ($get['company_id'] > 0) {
            $map[] = ['company_id', '=', $get['company_id']];
        }

        if($this->user['nickname'] != 'admin') {
            $userAuth = UserAuth::getUserLogicAuth($this->user['id']);
            $companyIds = empty($userAuth['store_ids']) ? [] : explode(',', $userAuth['store_ids']);
            $map[] = ['company_id', 'in', $companyIds];
        }

        if ($get['source'] > 0) {
            $map[] = ['source_id', '=', $get['source']];
        }

        if ($get['hotel_id'] > 0) {
            $map[] = ['hotel_id', '=', $get['hotel_id']];
        }

        if ($get['staff'] > 0) {
            $map[] = ['salesman', '=',  $get['staff']];
        }

        if (!empty($get['date_range']) && !empty($get['date_range_type'])) {
            $range = $this->getDateRange($get['date_range']);
            $map[] = [$get['date_range_type'], 'between', $range];
        }

        $map[] = ['check_status', '<>', 3];
        $model = model('order')->where($map);
        if (isset($get['mobile'])) {
            $model = $model->where('bridegroom_mobile|bride_mobile', 'like', "%{$get['mobile']}%");
        }

        if($this->user['nickname'] != 'admin') {
            $model = $model->where('id', 'in', function ($query) use ($companyIds) {
                $query->table('tk_order_wedding')->where('company_id', 'in', $companyIds)->field('order_id');
            });
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

            $where = [];
            $where[] = ['order_id', '=', $value->id];
            $wedding = OrderWedding::where($where)->find();
            if(!empty($wedding) && $wedding->company_id > 0) {
                $weddingCompanyId = $wedding->company_id;
                $value['wedding_company'] = $this->brands[$weddingCompanyId]['title'];
            } else {
                $value['wedding_company'] = '-';
            }
        }
        $count = $list->total();

        return ['data' => $data, 'count' => $count];
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