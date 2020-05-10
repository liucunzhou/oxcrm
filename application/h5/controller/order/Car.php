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

        $list = \app\common\model\Car::getList();
        $companyList = Brand::getBrands();
        $result = [
            'code' => '200',
            'msg' => '获取信息成功',
            'data' => [
                'list' => array_values($list),
                'companyList'   => array_values($this->companyList),
            ]
        ];


        return json($result);
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