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

    public function create()
    {
        $param = $this->request->param();
        $order = \app\common\model\Order::get($param['order_id']);
        $confirmList = $this->getConfirmProcess($order->company_id, 'order');

        $list = \app\common\model\Sugar::getList();
        $result = [
            'code' => '200',
            'msg' => '获取信息成功',
            'data' => [
                'list' => array_values($list),
                'confirmList' => $confirmList
            ]
        ];

        return json($result);
    }

    public function doCreate()
    {
        $param = $this->request->param();
        $orderId = $param['order_id'];
        $param = json_decode($param['sugarList'], true);
        foreach($param as $key=>$value) {
            $value['order_id'] = $orderId;
            $value['operate_id'] = $this->user['id'];
            $value['user_id'] = $this->user['id'];
            $result = $this->model->allowField(true)->save($value);
            $source['sugar'][] = $this->model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "添加喜糖信息审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'添加喜糖信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'添加喜糖信息失败'];
        }

        return json($arr);
    }

    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = "id,sugar_id,order_id,sugar_amount,sugar_price,sugar_remark";
        $data = $this->model->where($where)->field($fields)->find();
        if(!empty($data)) {
            $row = \app\common\model\Sugar::get($data->sugar_id);
            $data['title'] = $row->title;
            $result = [
                'code' => '200',
                'msg' => '获取信息成功',
                'data' => [
                    'sugarList' => $data
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
        $param = json_decode($param['sugarList'], true);
        if(!empty($param['id'])) {
            $where = [];
            $where[] = ['id', '=', $param['id']];
            $model = $this->model->where($where)->find();
            $result = $model->allowField(true)->save($param);
            $source['sugar'][] = $model->toArray();
        } else {
            $result = $this->model->allowField(true)->save($param);
            $source['sugar'][] = $this->model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($param['order_id']);
            $intro = "编辑喜糖审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}