<?php
namespace app\h5\controller\order;


use app\common\model\OrderLed;
use app\h5\controller\Base;

class Led extends Base
{
    protected $model = null;
    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderLed();
    }

    public function create()
    {
        $param = $this->request->param();
        $order = \app\common\model\Order::get($param['order_id']);
        $confirmList = $this->getConfirmProcess($order->company_id, 'order');

        $list = \app\common\model\Led::getList();
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
        $param = json_decode($param['ledList'], true);
        foreach($param as $key=>$value) {
            $value['order_id'] = $orderId;
            $value['operate_id'] = $this->user['id'];
            $value['user_id'] = $this->user['id'];
            $model = new OrderLed();
            $result = $model->allowField(true)->save($value);
            $source['led'][] = $model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "添加Led审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'添加Led信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'添加Led信息失败'];
        }

        return json($arr);
    }

    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = "id,led_id,order_id,led_amount,led_price,led_remark";
        $data = $this->model->where($where)->field($fields)->find();
        if(!empty($data)) {
            $row = \app\common\model\Led::get($data->led_id);
            $data['title'] = $row->title;
            $result = [
                'code' => '200',
                'msg' => '获取信息成功',
                'data' => [
                    'ledList' => $data
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
        $param = json_decode($param['ledList'], true);
        foreach ($param as $row) {
            $orderId = $row['order_id'];
            $row['user_id'] = $this->user['id'];
            $row['salesman'] = $this->user['id'];
            $row['item_check_status'] = 0;

            $model = OrderLed::get($row['id']);
            $result = $model->allowField(true)->save($row);
            $source['led'][] = $model->toArray();
        }

        if($result) {
            $source['led'][] = $this->model->getData();
            $order = \app\common\model\Order::get($orderId);
            $intro = "编辑LED审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}