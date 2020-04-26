<?php
namespace app\h5\controller\order;


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

    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = "id,car_id,order_id,is_master,car_amount,car_price,car_remark";
        $order = $this->model->where($where)->field($fields)->order('id desc')->find();

        $result = [
            'code'  =>  '200',
            'msg'   =>  '获取信息成功',
            'data'  =>  [
                'carList'  => $order
            ]
        ];

        return json($result);
    }

    public function doEdit()
    {
        $params = $this->request->param();

        if(!empty($params['id'])) {
            $where = [];
            $where[] = ['id', '=', $params['id']];
            $model = $this->model->where($where)->find();
            $result = $model->save($params);
        } else {
            $result = $this->model->allowField(true)->save($params);
        }

        if($result) {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}