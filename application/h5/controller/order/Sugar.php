<?php
namespace app\h5\controller\order;


use app\common\model\OrderSugar;
use app\h5\controller\Base;

class Sugar extends Base
{
    protected $model = null;
    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderSugar();
    }

    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = "id,sugar_id,order_id,sugar_amount,sugar_price,sugar_remark";
        $order = $this->model->where($where)->field($fields)->order('id desc')->find();

        $result = [
            'code'  =>  '200',
            'msg'   =>  '获取信息成功',
            'data'  =>  [
                'sugarList'  => $order
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