<?php

namespace app\h5\controller\order;


use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\h5\controller\Base;
use app\common\model\BanquetHall;
use app\common\model\OrderEntire;
use app\common\model\User;
use app\common\model\Search;

class Order extends Base
{
    protected $staffs = [];
    protected $brands = [];
    protected $hotels = [];



    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Order();

        // 获取系统来源,酒店列表,意向状态

        $staffes = User::getUsersInfoByDepartmentId($this->user['department_id']);

        $this->sources = \app\common\model\Source::getSources();

        ## 获取所有品牌、公司
        $this->brands = \app\common\model\Brand::getBrands();

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

    # 创建订单视图
    public function createOrder()
    {
        $request = $this->request->param();
        if (empty($request['id'])) return false;

        $allocate = MemberAllocate::get($request['id']);

        $member = Member::get($request['member_id']);

        ## 获取销售列表
        $salesmans = User::getUsersByRole(8);

        ## 签约公司
        $this->brands;

        ## 酒店列表
        $this->hotels;

        ## 订单类型
        $newsTypes = $this->config['news_type_list'];

    }

    # 创建订单逻辑
    public function doCreateOrder()
    {
        $request = Request::param();
        $OrderModel = new \app\common\model\Order();
        $OrderModel->allowField(true)->save($request);
        $request['order_id'] = $OrderModel->id;
        $request['operate_id'] = $this->user['id'];
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
            $request['salesman']= $request['sugar_salesman'];
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
            $request['salesman']= $request['light_salesman'];
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



        return json(['code' => '200', 'msg' => '创建成功', 'redirect'=> 'tab']);
    }
}