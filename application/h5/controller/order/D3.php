<?php
namespace app\h5\controller\order;


use app\common\model\OrderD3;
use app\h5\controller\Base;

class D3 extends Base
{
    protected $model = null;
    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderD3();
    }


    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = "id,d3_id,order_id,d3_amount,d3_price,d3_remark";
        $order = $this->model->where($where)->field($fields)->order('id desc')->find();

        $result = [
            'code'  =>  '200',
            'msg'   =>  '获取信息成功',
            'data'  =>  [
                'd3List'  => $order
            ]
        ];

        return json($result);
    }

    public function doEdit()
    {
        $param = $this->request->param();
        $param = json_decode($param['d3List'], true);
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