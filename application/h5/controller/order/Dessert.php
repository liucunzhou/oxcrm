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

    public function create()
    {

        $param = $this->request->param();
        $order = \app\common\model\Order::get($param['order_id']);
        $confirmList = $this->getConfirmProcess($order->company_id, 'order');

        $list = \app\common\model\Dessert::getList();
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
        $param = json_decode($param['dessertList'], true);
        foreach($param as $key=>$value) {
            $value['order_id'] = $orderId;
            $value['operate_id'] = $this->user['id'];
            $value['user_id'] = $this->user['id'];
            $result = $this->model->allowField(true)->save($value);
            $source['dessert'][] = $this->model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "添加糕点审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'添加糕点信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'添加糕点信息失败'];
        }

        return json($arr);
    }

    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = "id,dessert_id,order_id,dessert_amount,dessert_price,dessert_remark";
        $data = $this->model->where($where)->field($fields)->find();
        if(!empty($data)) {
            $row = \app\common\model\Dessert::get($data->dessert_id);
            $data['title'] = $row->title;
            $result = [
                'code' => '200',
                'msg' => '获取信息成功',
                'data' => [
                    'dessertList' => $data
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
        $param = json_decode($param['dessertList'], true);
        foreach ($param as $row) {
            $orderId = $row['order_id'];
            $row['user_id'] = $this->user['id'];
            $row['salesman'] = $this->user['id'];
            $model = OrderDessert::get($row['id']);
            $result = $model->allowField(true)->save($row);
            $source['dessert'][] = $model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "编辑糕点审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}