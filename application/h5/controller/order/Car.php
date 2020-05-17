<?php
namespace app\h5\controller\order;


use app\common\model\Brand;
use app\common\model\OrderCar;
use app\h5\controller\Base;

class Car extends Base
{
    protected $model = null;
    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderCar();
    }

    public function create()
    {
        $param = $this->request->param();
        $order = \app\common\model\Order::get($param['order_id']);

        $list = \app\common\model\Car::getList();
        $companyList = Brand::getBrands();
        $result = [
            'code' => '200',
            'msg' => '获取信息成功',
            'data' => [
                'list' => array_values($list),
                'companyList'   => array_values($companyList),
            ]
        ];


        return json($result);
    }

    public function doCreate(){
        $param = $this->request->param();
        $orderId = $param['order_id'];
        $param = json_decode($param['car'], true);
        if (!empty($param['master_car_id'])) {
            $row = [];
            $row['company_id'] = $param['company_id'];
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
            $row['salesman'] = $param['car_salesman'];
            $row['order_id'] = $orderId;
            $row['operate_id'] = $this->user['id'];
            $row['user_id'] = $this->user['id'];
            $carModel = new OrderCar();
            $result1 = $carModel->allowField(true)->save($row);
            $source['car'][] = $carModel->toArray();
        }

        if (!empty($param['slave_car_id'])) {
            $row = [];
            $row['order_id'] = $param['order_id'];
            $row['company_id'] = $param['company_id'];
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
            $row['salesman'] = $param['car_salesman'];
            $row['order_id'] = $orderId;
            $row['operate_id'] = $this->user['id'];
            $row['user_id'] = $this->user['id'];
            $carModel = new OrderCar();
            $result2 = $carModel->allowField(true)->save($row);
            $source['car'][] = $carModel->toArray();
        }

        if($result1 || $result2) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "添加婚车审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'添加婚车信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'添加婚车信息失败'];
        }

        return json($arr);
    }

    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = "id,car_id,order_id,is_master,car_amount,car_price,car_remark";
        $data = $this->model->where($where)->field($fields)->find();
        $carList = \app\common\model\Car::getList();
        if(!empty($data)) {
            $row = \app\common\model\Car::get($data->car_id);
            $data['title'] = $row->title;
            $result = [
                'code' => '200',
                'msg' => '获取信息成功',
                'data' => [
                    'car' => $data,
                    'carList' => $carList
                ]
            ];
        } else {
            $result = [
                'code' => '200',
                'msg' => '获取信息成功'
            ];
        }

        return json($result);
    }

    public function doEdit()
    {
        $param = $this->request->param();
        $param = json_decode($param['car'], true);
        $row = [];
        $row['company_id'] = $param['company_id'];
        $row['is_suborder'] = 1;
        $row['service_hour'] = $param['service_hour'];
        $row['service_distance'] = $param['service_distance'];
        $row['arrive_time'] = $param['arrive_time'];
        $row['arrive_address'] = $param['arrive_address'];
        $row['car_remark'] = $param['car_remark'];
        $row['user_id'] = $this->user['id'];
        $row['salesman'] = $this->user['id'];
        $row['order_id'] = $param['order_id'];

        if (!empty($param['master_order_id'])) {
            $row['id'] = $param['master_order_id'];
            $row['car_id'] = $param['master_car_id'];
            $row['car_price'] = $param['master_car_price'];
            $row['car_amount'] = $param['master_car_amount'];
            $row['item_check_status'] = 0;
            $master = OrderCar::get($param['master_order_id']);
            $result1 = $master->allowField(true)->save($row);
            $source['car'][] = $master->toArray();
        }

        if (!empty($param['slave_order_id'])) {
            $row = [];
            $row['id'] = $param['slave_order_id'];
            $row['car_id'] = $param['slave_car_id'];
            $row['car_price'] = $param['slave_car_price'];
            $row['car_amount'] = $param['slave_car_amount'];
            $row['item_check_status'] = 0;
            $slave = OrderCar::get($param['slave_order_id']);
            $result2 = $slave->allowField(true)->save($row);
            $source['car'][] = $slave->toArray();
        }
        if($result1 && $result2) {
            $order = \app\common\model\Order::get($param['order_id']);
            $intro = "编辑婚车项目审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}