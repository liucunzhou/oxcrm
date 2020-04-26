<?php
namespace app\h5\controller\order;


use app\common\model\OrderDessert;
use app\h5\controller\Base;

class Dessert extends Base
{
    protected $model = null;
    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderDessert();
    }


    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = "id,dessert_id,order_id,dessert_amount,dessert_price,dessert_remark";
        $order = $this->model->where($where)->field($fields)->order('id desc')->find();

        $result = [
            'code'  =>  '200',
            'msg'   =>  '获取信息成功',
            'data'  =>  [
                'dessertList'  => $order
            ]
        ];

        return json($result);
    }

    public function doEdit()
    {
        $param = $this->request->param();
        $param = json_decode($param['dessertList'], true);
        if(!empty($param['id'])) {
            $where = [];
            $where[] = ['id', '=', $param['id']];
            $model = $this->model->where($where)->find();
            $result = $model->save($param);
        } else {
            $result = $this->model->allowField(true)->save($param);
        }

        if($result) {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}