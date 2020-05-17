<?php
namespace app\h5\controller\order;


use app\common\model\OrderLight;
use app\h5\controller\Base;

class Light extends Base
{
    protected $model = null;
    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderLight();
    }

    public function create()
    {
        $param = $this->request->param();
        $order = \app\common\model\Order::get($param['order_id']);
        $confirmList = $this->getConfirmProcess($order->company_id, 'order');

        $list = \app\common\model\Light::getList();
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
        $param = json_decode($param['lightList'], true);
        foreach($param as $key=>$value) {
            $value['order_id'] = $orderId;
            $value['operate_id'] = $this->user['id'];
            $value['user_id'] = $this->user['id'];
            $model = new OrderLight();
            $result = $model->allowField(true)->save($value);
            $source['light'][] = $model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "添加灯光信息审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'添加灯光信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'添加灯光信息失败'];
        }

        return json($arr);
    }

    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = "id,light_id,order_id,light_amount,light_price,light_remark";
        $data = $this->model->where($where)->field($fields)->find();
        if(!empty($data)) {
            $row = \app\common\model\Light::get($data->light_id);
            $data['title'] = $row->title;
            $result = [
                'code' => '200',
                'msg' => '获取信息成功',
                'data' => [
                    'lightList' => $data
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
        $param = json_decode($param['lightList'], true);
        foreach ($param as $row) {
            $orderId = $row['order_id'];
            $row['user_id'] = $this->user['id'];
            $row['salesman'] = $this->user['id'];
            $row['item_check_status'] = 0;

            $model = OrderLight::get($row['id']);
            $result = $model->allowField(true)->save($row);
            $source['light'][] = $model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "编辑灯光审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}