<?php
namespace app\h5\controller\order;

use app\h5\controller\Base;

class Contract extends Base
{
    public $model = null;

    protected function initialize()
    {
        parent::initialize();

        $this->model = new \app\common\model\Order();
    }

    public function edit($id)
    {
        $fields = 'id,totals,earnest_money_date,earnest_money,middle_money_date,middle_money,tail_money_date,tail_money';
        $order = $this->model->field($fields)->where('id', '=', $id)->find();

        $result = [
            'code'  => '200',
            'msg'   => '获取订单信息成功',
            'data'  => [
                'contractPrice' => $order
            ]
        ];
        return json($result);
    }

    public function doEdit()
    {
        $param = $this->request->param();
        $param = json_decode($param['contractPrice'], true);
        if(!empty($param['id'])) {
            $where = [];
            $where[] = ['id', '=', $param['id']];
            $model = $this->model->where($where)->find();
            $result = $model->allowField(true)->save($param);
        } else {
            $result = $this->model->allowField(true)->save($param);
        }

        if($result) {
            $order = \app\common\model\Order::get($param['id']);
            $intro = "编辑订单金额信息审核";
            create_order_confirm($order->order_id, $order->company_id, $this->user['id'], 'income', $intro);
            $arr = ['code'=>'200', 'msg'=>'编辑成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑失败'];
        }

        return json($arr);
    }
}