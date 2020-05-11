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
        $carData = json_decode($param['car'], true);
        if (!empty($carData['master_car_id'])) {
            $row = [];
            $row['company_id'] = $carData['car_company_id'];
            $row['is_master'] = 1;
            $row['is_suborder'] = 0;
            $row['car_id'] = $carData['master_car_id'];
            $row['car_price'] = $carData['master_car_price'];
            $row['car_amount'] = $carData['master_car_amount'];
            $row['service_hour'] = $carData['service_hour'];
            $row['service_distance'] = $carData['service_distance'];
            $row['car_contact'] = $carData['car_contact'];
            $row['car_mobile'] = $carData['car_mobile'];
            $row['arrive_time'] = $carData['arrive_time'];
            $row['arrive_address'] = $carData['arrive_address'];
            $row['car_remark'] = $carData['master_car_remark'];
            $row['salesman'] = $carData['car_salesman'];
            $row['company_id'] = $carData['car_company_id'];
            $row['order_id'] = $orderId;
            $row['operate_id'] = $this->user['id'];
            $row['user_id'] = $this->user['id'];
            $carModel = new OrderCar();
            $result1 = $carModel->allowField(true)->save($row);
            $source['car'][] = $carModel->toArray();
        }

        if (!empty($carData['slave_car_id'])) {
            $row = [];
            $row['order_id'] = $carData['order_id'];
            $row['company_id'] = $carData['car_company_id'];
            $row['is_master'] = 0;
            $row['is_suborder'] = 0;
            $row['car_id'] = $carData['slave_car_id'];
            $row['car_price'] = $carData['slave_car_price'];
            $row['car_amount'] = $carData['slave_car_amount'];
            $row['service_hour'] = $carData['service_hour'];
            $row['service_distance'] = $carData['service_distance'];
            $row['car_contact'] = $carData['car_contact'];
            $row['car_mobile'] = $carData['car_mobile'];
            $row['arrive_time'] = $carData['arrive_time'];
            $row['arrive_address'] = $carData['arrive_address'];
            $row['car_remark'] = $carData['slave_car_remark'];
            $row['create_time'] = time();
            $row['salesman'] = $carData['car_salesman'];
            $row['company_id'] = $carData['car_company_id'];
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
        $param = json_decode($param['carList'], true);
        if(!empty($param['id'])) {
            $where = [];
            $where[] = ['id', '=', $param['id']];
            $model = $this->model->where($where)->find();
            $result = $model->allowField(true)->save($param);
            $source['car'][] = $model->toArray();
        } else {
            $result = $this->model->allowField(true)->save($param);
            $source['car'][] = $this->model->toArray();
        }

        if($result) {
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