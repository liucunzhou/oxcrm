<?php

namespace app\h5\controller\order;


use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\OrderBanquet;
use app\common\model\OrderBanquetReceivables;
use app\common\model\OrderBanquetSuborder;
use app\common\model\OrderCar;
use app\common\model\OrderConfirm;
use app\common\model\OrderD3;
use app\common\model\OrderDessert;
use app\common\model\OrderHotelItem;
use app\common\model\OrderLed;
use app\common\model\OrderLight;
use app\common\model\OrderSugar;
use app\common\model\OrderWedding;
use app\common\model\OrderWine;
use app\h5\controller\Base;
use app\common\model\BanquetHall;
use app\common\model\OrderEntire;
use app\common\model\User;
use app\common\model\Search;
use app\index\controller\organization\Audit;

class Order extends Base
{
    protected $staffs = [];
    protected $brands = [];
    protected $hotels = [];
    protected $carList = [];
    protected $sugarList = [];
    protected $wineList = [];
    protected $lightList = [];
    protected $dessertList = [];
    protected $ledList = [];
    protected $d3List = [];


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

        ## 供应商列表
        $this->suppliers = \app\common\model\Supplier::getList();

        ## 婚庆设备列表
        $this->weddingDevices = \app\common\model\WeddingDevice::getList();

        ## 婚庆二销分类列表
        $this->weddingCategories = \app\common\model\WeddingCategory::getList();

        ## 汽车列表
        $this->carList = \app\common\model\Car::getList();

        ## 酒水列表
        $this->wineList = \app\common\model\Wine::getList();

        ## 喜糖列表
        $this->sugarList = \app\common\model\Sugar::getList();
        ## 灯光列表
        $this->lightList = \app\common\model\Light::getList();

        ## 点心列表
        $this->dessertList = \app\common\model\Dessert::getList();

        ## led列表
        $this->ledList = \app\common\model\Led::getList();

        ## 3d列表
        $this->d3List = \app\common\model\D3::getList();

        if (isset($this->role['auth_type']) && $this->role['auth_type'] > 0) {
            $this->staffs = User::getUsersByDepartmentId($this->user['department_id']);
        }

    }

    ##
    public function index()
    {
        $param = $this->request->param();
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 3;
        $param['page'] = isset($param['page']) ? (int)$param['page'] + 1 : 1;
        $config = [
            'page' => $param['page']
        ];

        ##  审核状态
        if( isset($param['check_status']) && $param['check_status'] != 'all' ){
            $map[] = ['check_status','=',$param['check_status']];
        }

        ## 签约公司
        if( isset($param['company_id']) && $param['company_id'] > 0 ){
            $map[] = ['company_id','=',$param['company_id']];
        }

        ## 时间类型
        if( isset($param['range']) && !empty($param['range'])){
            $column = !empty($param['range_type']) ? $param['range_type'] : 'create_time';
            $range = format_date_range($param['range']);
            $map[] = [$column, 'between', $range];
        }

        ###  管理者还是销售
        if($this->role['auth_type'] > 0) {
            ### 员工列表
            if( isset($param['user_id']) && !empty($param['user_id'])) {

                if ($param['user_id'] == 'all') {
                    $map[] = ['salesman', 'in', $this->staffs];
                } else if (is_numeric($param['user_id'])) {
                    $map[] = ['salesman', '=', $this->user['id']];
                } else {
                    $map[] = ['salesman', 'in', $param['user_id']];
                }

            }  else {
                $map[] = ['salesman', '=', $this->user['id']];
            }

        } else {
            $map[] = ['salesman', '=', $this->user['id']];
        }


        $fields = "id,contract_no,company_id,news_type,sign_date,status,event_date,hotel_text,cooperation_mode,bridegroom,bridegroom_mobile,bride,bride_mobile";
        $list = $this->model->where($map);

        if( isset( $param['keywords'] ) && !empty($param['keywords']) ) {
            if(is_numeric($param['keywords'])) {
                $list->where('bride_mobile|bridegroom_mobile','like',"%{$param['keywords']}%");
            } else {
                $list->where('bride|bridegroom','like',"%{$param['keywords']}%");
            }
        }

        $list = $list->field($fields)->order('id desc')->paginate($param['limit'], false, $config);
        if (!$list->isEmpty()) {
            $list = $list->getCollection()->toArray();
            $newsTypes = $this->config['news_type_list'];
            $cooperationMode = $this->config['cooperation_mode'];

            foreach ($list as $key => &$value) {
                $value['company_id'] = $this->brands[$value['company_id']]['title'];
                $value['news_type'] = $newsTypes[$value['news_type']];
                $value['cooperation_mode'] = isset($cooperationMode[$value['cooperation_mode']]) ? $cooperationMode[$value['cooperation_mode']] : '-';
                $value['status'] = '待审核';
                $value['sign_date'] = substr($value['sign_date'], 0, 10);
                $value['event_date'] = substr($value['event_date'], 0, 10);
                $value['bridegroom_mobile'] = isset($value['bridegroom_mobile']) ? substr_replace($value['bridegroom_mobile'], '***', 3, 3) : '-';
                $value['bride_mobile'] = isset($value['bride_mobile']) ? substr_replace($value['bride_mobile'], '***', 3, 3) : '-';
            }
        } else {
            $list = [];
        }

        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'orderlist'     =>  $list,
            ]
        ];
        return json($result);
    }

    ##
    public function detail()
    {
        $param = $this->request->param();
        $fields = "id,contract_no,score,company_id,news_type,banquet_hall_name,sign_date,event_date,hotel_text,cooperation_mode,bridegroom,salesman,recommend_salesman,bridegroom_mobile,bride,bride_mobile,remark";
        $order = $this->model->where('id','=',$param['id'])->field($fields)->find();
        if( !$order->isEmpty() ) {
            $order = $order->toArray();
            $newsTypes = $this->config['news_type_list'];
            $cooperationMode = $this->config['cooperation_mode'];
            $order['contract_no'] = isset($order['contract_no']) ? $order['contract_no'] : '-';
            $order['company_id'] = $this->brands[$order['company_id']]['title'];
            $order['news_type'] = $newsTypes[$order['news_type']];
            $order['cooperation_mode'] = isset($cooperationMode[$order['cooperation_mode']]) ? $cooperationMode[$order['cooperation_mode']] : '-';
            $order['status'] = '待审核';
            $order['sign_date'] = substr($order['sign_date'], 0, 10);
            $order['event_date'] = substr($order['event_date'], 0, 10);
            $order['bridegroom_mobile'] = isset($order['bridegroom_mobile']) ? substr_replace($order['bridegroom_mobile'], '***', 3, 3) : '-';
            $order['bride_mobile'] = isset($order['bride_mobile']) ? substr_replace($order['bride_mobile'], '***', 3, 3) : '-';
        } else {
            $order = [];
        }

        $member = Member::field('realname,mobile,source_text')->where('id','=',$order->member_id)->find();

        #### 获取婚宴订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $banquet = \app\common\model\OrderBanquet::where($where)->order('id desc')->find();
        if(empty($banquet)) $banquet = [];

        #### 酒店服务项目
        $where = [];
        $where['order_id'] = $param['id'];
        $hotelItem = \app\common\model\OrderHotelItem::where($where)->order('id desc')->find();
        if (empty($hotelItem)) $hotelItem = [];

        #### 获取婚宴二销订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $banquetSuborderList = \app\common\model\OrderBanquetSuborder::where($where)->select();

        #### 获取婚宴收款信息
        $banquetReceivableList = \app\common\model\OrderBanquetReceivables::where('order_id', '=', $param['id'])->select();

        #### 获取婚宴付款信息
        $banquetPaymentList = \app\common\model\OrderBanquetPayment::where('order_id', '=', $param['id'])->select();

        #### 获取婚庆订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $wedding = \app\common\model\OrderWedding::where($where)->order('id desc')->find();
        if(empty($wedding)) $wedding = [];

        #### 获取婚宴二销订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $weddingSuborderList = \app\common\model\OrderWeddingSuborder::where($where)->select();

        #### 获取婚宴收款信息
        $weddingReceivableList = \app\common\model\OrderWeddingReceivables::where('order_id', '=', $param['id'])->select();
        #### 获取婚庆付款信息
        $weddingPaymentList = \app\common\model\OrderWeddingPayment::where('order_id', '=', $param['id'])->select();

        #### 婚车
        $where = [];
        $where['order_id'] = $param['id'];
        $carList = \app\common\model\OrderCar::where($where)->select();
        foreach ( $carList as $key=>&$row ) {
            $row['car_id'] = $this->carList[$row['id']]['title'];
            $row['is_master'] = $row['is_master'] == '1' ? '主车' : '跟车';
        }

        #### 喜糖
        $where = [];
        $where['order_id'] = $param['id'];
        $sugarList = \app\common\model\OrderSugar::where($where)->select();
        foreach ( $sugarList as $key=>&$row ) {
            $row['sugar_id'] = $this->sugarList[$row['id']]['title'];
        }

        #### 酒水
        $where = [];
        $where['order_id'] = $param['id'];
        $wineList = \app\common\model\OrderWine::where($where)->select();
        foreach ( $wineList as $key=>&$row ) {
            $row['wine_id'] = $this->wineList[$row['id']]['title'];
        }

        #### 灯光
        $where = [];
        $where['order_id'] = $param['id'];
        $lightList = \app\common\model\OrderLight::where($where)->select();
        foreach ( $lightList as $key=>&$row ) {
            $row['light_id'] = $this->lightList[$row['id']]['title'];
        }

        #### 点心
        $where = [];
        $where['order_id'] = $param['id'];
        $dessertList = \app\common\model\OrderDessert::where($where)->select();
        foreach ( $dessertList as $key=>&$row ) {
            $row['desser_id'] = $this->dessertList[$row['id']]['title'];
        }

        #### LED
        $where = [];
        $where['order_id'] = $param['id'];
        $ledList = \app\common\model\OrderLed::where($where)->select();
        foreach ( $ledList as $key=>&$row ) {
            $row['led_id'] = $this->ledList[$row['id']]['title'];
        }

        #### 3D
        $where = [];
        $where['order_id'] = $param['id'];
        $d3List = \app\common\model\OrderD3::where($where)->select();
        foreach ( $carList as $key=>&$row ) {
            $row['d3_id'] = $this->d3List[$row['id']]['title'];
        }

        #### 获取审核进度

        $result = [
            'code'  =>  '200',
            'msg'   =>  '获取成功',
            'data'  =>  [
                'order'                 =>  [
                    'api'       => '/h5/order.order/doEdit',
                    'json'      => $order
                ],
                'member'    =>  [
                    'api'       => '',
                    'json'      => $member,
                ],
                'banquet'   =>  [
                    'api'   => '/h5/order.banquet/doEdit',
                    'json'  => $banquet,
                ],
                'banquetSuborderList'   =>  [
                    'api'   => '/h5/order.banquet_suborder/doEdit',
                    'array'  => $banquetSuborderList
                ],
                'banquetReceivableList' =>  [
                    'api'   => '/h5/order.banquet_receivable/doEdit',
                    'array'  => $banquetReceivableList
                ],
                'banquetPaymentList'    =>  [
                    'api'   => '/h5/order.banquet_payment/doEdit',
                    'array'  => $banquetPaymentList
                ],
                'hotelItem'             =>  [
                    'api'   => '/h5/order.hotel_item/doEdit',
                    'json'  => $hotelItem
                ],
                'wedding'               =>  [
                    'api'   => '/h5/order.wedding/doEdit',
                    'json'  => $wedding
                ],
                'weddingSuborderList'   =>  [
                    'api'   => '/h5/order.wedding_suborder/doEdit',
                    'array'  => $weddingSuborderList
                ],
                'weddingReceivableList' =>  [
                    'api'   => '/h5/order.wedding_receivable/doEdit',
                    'array'  => $weddingReceivableList
                ],
                'weddingPaymentList'    =>  [
                    'api'   => '/h5/order.wedding_payment/doEdit',
                    'array'  => $weddingPaymentList
                ],
                'carList'               =>  [
                    'api'   => '/h5/order.car/doEdit',
                    'array'  => $carList
                ],
                'wineList'              =>  [
                    'api'   => '/h5/order.wine/doEdit',
                    'array'  => $wineList
                ],
                'sugarList'             =>  [
                    'api'   => '/h5/order.sugar/doEdit',
                    'array'  => $sugarList
                ],
                'dessertList'           =>  [
                    'api'   => '/h5/order.dessert/doEdit',
                    'array'  => $dessertList
                ],
                'lightList'             =>  [
                    'api'   => '/h5/order.light/doEdit',
                    'array'  => $lightList
                ],
                'ledList'               =>  [
                    'api'   => '/h5/order.led/doEdit',
                    'array'  => $ledList
                ],
                'd3List'                =>  [
                    'api'   => '/h5/order.d3/doEdit',
                    'array'  => $d3List
                ]
            ]
        ];

        return json($result);
    }

    # 创建订单视图
    public function createOrder()
    {
        $param = $this->request->param();

        $allocate = MemberAllocate::get($param['id']);

        $fields = "id,realname,mobile,source_id,source_text";
        $member = Member::field($fields)->get($allocate['member_id']);

        $result = [
            'code'  =>  '200',
            'msg'   =>  '获取成功',
            'data'  =>  [
                'member'                =>  $member,          ## 客资信息
                'companyList'           =>  $this->brands,    ##签约公司列表
                'newsTypeList'          =>  $this->config['news_type_list'],    ## 订单类型
                'cooperationModeList'   =>  $this->config['cooperation_mode'],  ## 合同模式
            ]
        ];
        return json($$result);
    }

    # 创建订单逻辑
    public function doCreateOrder()
    {
        $param = $this->request->param();
        $OrderModel = new \app\common\model\Order();
        $OrderModel->allowField(true)->save($param);
        $param['order_id'] = $OrderModel->id;
        $param['operate_id'] = $this->user['id'];
        ## banquet message
        if (!empty($param['wedding_total'])) {
            $BanquetModel = new OrderBanquet();
            $BanquetModel->allowField(true)->save($param);
        }

        ## wedding message
        if (!empty($param['banquet_totals'])) {
            $weddingModel = new OrderWedding();
            // get wedding devices
            $weddingModel->allowField(true)->save($param);
        }

        ## 婚车主车
        if (!empty($param['master_car_id'])) {
            $row = [];
            $row['operate_id'] = $this->user['id'];
            $row['order_id'] = $param['order_id'];
            $row['company_id'] = $param['car_company_id'];
            $row['is_master'] = 1;
            $row['is_suborder'] = 0;
            $row['car_id'] = $param['master_car_id'];
            $row['car_price'] = $param['master_car_price'];
            $row['car_amount'] = $param['master_car_amount'];
            $row['service_hour'] = $param['service_hour'];
            $row['service_distance'] = $param['service_distance'];
            $row['car_contact'] = $param['car_contact'];
            $row['car_mobile'] = $param['car_mobile'];
            $row['arrive_time'] = $param['arrive_time'];
            $row['arrive_address'] = $param['arrive_address'];
            $row['car_remark'] = $param['master_car_remark'];
            $row['create_time'] = time();
            $row['salesman'] = $row['car_salesman'];
            $row['company_id'] = $row['car_company_id'];

            $carModel = new OrderCar();
            $carModel->allowField(true)->save($row);
        }

        ## 婚车跟车
        if (!empty($param['slave_car_id'])) {
            $row = [];
            $row['operate_id'] = $this->user['id'];
            $row['order_id'] = $param['order_id'];
            $row['company_id'] = $param['car_company_id'];
            $row['is_master'] = 0;
            $row['is_suborder'] = 0;
            $row['car_id'] = $param['slave_car_id'];
            $row['car_price'] = $param['slave_car_price'];
            $row['car_amount'] = $param['slave_car_amount'];
            $row['service_hour'] = $param['service_hour'];
            $row['service_distance'] = $param['service_distance'];
            $row['car_contact'] = $param['car_contact'];
            $row['car_mobile'] = $param['car_mobile'];
            $row['arrive_time'] = $param['arrive_time'];
            $row['arrive_address'] = $param['arrive_address'];
            $row['car_remark'] = $param['slave_car_remark'];
            $row['create_time'] = time();
            $row['salesman'] = $row['car_salesman'];
            $row['company_id'] = $row['car_company_id'];

            $carModel = new OrderCar();
            $carModel->allowField(true)->save($row);
        }

        ## 喜糖
        if (!empty($param['sugar_id'])) {
            $sugarModel = new OrderSugar();
            // get wedding devices
            $param['salesman']= $param['sugar_salesman'];
            $sugarModel->allowField(true)->save($param);
        }


        ## 酒水
        if (!empty($param['wine_id'])) {
            $wineModel = new OrderWine();
            // get wedding devices
            $param['salesman'] = $param['wine_salesman'];
            $wineModel->allowField(true)->save($param);
        }

        ## 灯光
        if (!empty($param['light_id'])) {
            $lightModel = new OrderLight();
            // get wedding devices
            $param['salesman']= $param['light_salesman'];
            $lightModel->allowField(true)->save($param);
        }

        ## 点心
        if (!empty($param['dessert_id'])) {
            $dessertModel = new OrderDessert();
            // get wedding devices
            $param['salesman'] = $param['dessert_salesman'];
            $dessertModel->allowField(true)->save($param);
        }

        ## led
        if (!empty($param['led_id'])) {
            $ledModel = new OrderLed();
            // get wedding devices
            $param['salesman'] = $param['led_salesman'];
            $ledModel->allowField(true)->save($param);
        }

        ## 3D
        if (!empty($param['d3_id'])) {
            $d3Model = new OrderD3();
            // get wedding devices
            $param['salesman'] = $param['d3_salesman'];
            $d3Model->allowField(true)->save($param);
            echo $d3Model->getLastSql();
        }

        return json(['code' => '200', 'msg' => '创建成功', 'redirect'=> 'tab']);
    }

    public function edit()
    {
        $param = $this->request->param();

        $order = \app\common\model\Order::get($param['id']);

        $result = [
            'code'    =>    '200',
            'msg'     =>    '获取信息成功',
            'data'    =>    [
                'order' =>  $order
            ]
        ];

        return json($result);
    }

    public function doEdit()
    {
        $param = $this->request->param();

        if( isset($param['id']) || empty($param['id'])) {
            $result = [
                'code'    =>    '400',
                'msg'     =>    '缺少必要参数'
            ];

            return json($result);
        }

        $request = $this->model->save($param);
        if( $request ) {
            $result = [
                'code'    =>    '200',
                'msg'     =>    '编辑成功'
            ];
        } else {
            $result = [
                'code'    =>    '400',
                'msg'     =>    '编辑失败'
            ];
        }
        return json($result);
    }
}