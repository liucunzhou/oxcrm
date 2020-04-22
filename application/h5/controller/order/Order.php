<?php

namespace app\h5\controller\order;


use app\h5\controller\Base;
use app\common\model\BanquetHall;
use app\common\model\OrderEntire;
use app\common\model\User;
use app\common\model\Search;

class Order extends Base
{
    protected $staffs = [];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Order();

        // 获取系统来源,酒店列表,意向状态

        $staffes = User::getUsersInfoByDepartmentId($this->user['department_id']);

        $this->sources = \app\common\model\Source::getSources();

        ## 获取所有品牌、公司
        $brands = \app\common\model\Brand::getBrands();

        ## 酒店列表
        $this->hotels = \app\common\model\Store::getStoreList();

        ## 宴会厅列表
        $halls = BanquetHall::getBanquetHalls();

        ## 获取套餐列表
        $packages = \app\common\model\Package::getList();

        ## 获取仪式列表
        $rituals = \app\common\model\Ritual::getList();

        ## 酒店服务项目
        $banquetHoteItems = \app\common\model\BanquetHotelItem::getList();

        ## 酒店服务项目
        $cars = \app\common\model\Car::getList();

        ## 供应商列表
        $this->suppliers = \app\common\model\Supplier::getList();

        ## 婚庆设备列表
        $this->weddingDevices = \app\common\model\WeddingDevice::getList();

        ## 婚庆二销分类列表
        $this->weddingCategories = \app\common\model\WeddingCategory::getList();

        ## 汽车列表
        $carList = \app\common\model\Car::getList();

        ## 酒水列表
        $wineList = \app\common\model\Wine::getList();

        ## 喜糖列表
        $sugarList = \app\common\model\Sugar::getList();
        ## 灯光列表
        $lightList = \app\common\model\Light::getList();

        ## 点心列表
        $dessertList = \app\common\model\Dessert::getList();

        ## led列表
        $ledList = \app\common\model\Led::getList();

        ## 3d列表
        $d3List = \app\common\model\D3::getList();

        if (isset($this->role['auth_type']) && $this->role['auth_type'] > 0) {
            $this->staffs = User::getUsersByDepartmentId($this->user['department_id']);
        }

    }

    ##  salesman  order
    public function index()
    {
        $request = $this->request->param();
        $request['limit'] = isset($request['limit']) ? $request['limit'] : 3;
        $request['page'] = isset($request['page']) ? $request['page'] + 1 : 1;
        $config = [
            'page' => $request['page']
        ];

        ###  管理者还是销售
        if($this->role['auth_type'] > 0) {
            ### 员工列表
            if( isset($request['user_id']) && !empty($result['user_id'])) {
                // $user_id = explode(',',$request['user_id']);
                if ($request['user_id'] == 'all') {
                    $map[] = ['salesman', 'in', $this->staffs];
                } else if (is_numeric($request['user_id'])) {
                    $map[] = ['salesman', '=', $this->user['id']];
                } else {
                    $map[] = ['salesman', 'in', $request['user_id']];
                }

            }  else {
                $map[] = ['salesman', '=', $this->user['id']];
            }

        } else {
            $map[] = ['salesman', '=', $this->user['id']];
        }

        $list = $this->model->where($map)->order('id desc')->paginate($request['limit'], false, $config);
        $users = \app\common\model\User::getUsers();

        foreach ($list as $key => &$value) {
            !empty($value['bridegroom_mobile']) && $value['bridegroom_mobile'] = substr_replace($value['bridegroom_mobile'], '***', 3, 3);;
            !empty($value['bride_mobile']) && $value['bride_mobile'] = substr_replace($value['bride_mobile'], '***', 3, 3);
            $value['salesman'] = isset($users[$value['salesman']]) ? $users[$value['salesman']]['realname'] : '-';
        }

        $result = [
            'code' => 0,
            'msg' => '获取数据成功',
            'data' => [
                'order'     =>  $list,
                'count'     =>  $list->total()
            ]
        ];
        return json($result);
    }
}