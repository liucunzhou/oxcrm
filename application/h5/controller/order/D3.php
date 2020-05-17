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

    public function create()
    {

        $param = $this->request->param();
        $order = \app\common\model\Order::get($param['order_id']);
        $confirmList = $this->getConfirmProcess($order->company_id, 'order');

        $list = \app\common\model\D3::getList();
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
        $param = json_decode($param['d3List'], true);
        foreach($param as $key=>$value) {
            $value['order_id'] = $orderId;
            $value['operate_id'] = $this->user['id'];
            $value['user_id'] = $this->user['id'];
            $model = new OrderD3();
            $result = $model->allowField(true)->save($value);
            $source['d3'][] = $model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "添加3D信息审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'添加3D信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'添加3D信息失败'];
        }

        return json($arr);
    }

    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = "id,d3_id,order_id,d3_amount,d3_price,d3_remark";
        $data = $this->model->where($where)->field($fields)->find();
        if(!empty($data)) {
            $row = \app\common\model\D3::get($data->d3_id);
            $data['title'] = $row->title;
            $result = [
                'code' => '200',
                'msg' => '获取信息成功',
                'data' => [
                    'd3List' => $data
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
        $param = json_decode($param['d3List'], true);

        foreach ($param as $row) {
            $orderId = $row['order_id'];
            $row['user_id'] = $this->user['id'];
            $row['salesman'] = $this->user['id'];
            $model = OrderD3::get($row['id']);
            $result = $model->allowField(true)->save($row);
            $source['d3'][] = $model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "编辑3D信息审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'编辑3D信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑3D信息失败'];
        }

        return json($arr);
    }
}