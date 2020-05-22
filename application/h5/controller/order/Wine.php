<?php
namespace app\h5\controller\order;


use app\common\model\OrderWine;
use app\h5\controller\Base;

class Wine extends Base
{
    protected $model = null;
    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderWine();
    }

    public function create()
    {
        $param = $this->request->param();
        $order = \app\common\model\Order::get($param['order_id']);
        $confirmList = $this->getConfirmProcess($order->company_id, 'order');

        $list = \app\common\model\Wine::getList();
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
        $param = json_decode($param['wineList'], true);
        foreach($param as $key=>$value) {
            $value['order_id'] = $orderId;
            $value['operate_id'] = $this->user['id'];
            $value['user_id'] = $this->user['id'];
            $model = new OrderWine();
            $result = $model->allowField(true)->save($value);
            $source['wine'][] = $model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            // echo \app\common\model\Order::getLastSql();
            $intro = "添加酒水信息审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'添加酒水信息成功'];
        } else {
            $arr = ['code'=>'500', 'msg'=>'添加酒水信息失败'];
        }

        return json($arr);
    }

    public function edit($id)
    {
        $where = [];
        $where[] = ['id', '=', $id];
        $fields = "id,wine_id,order_id,wine_amount,wine_price,wine_remark";
        $data = $this->model->where($where)->field($fields)->order('id desc')->find();
        if(!empty($data)) {
            $row = \app\common\model\Wine::get($data->wine_id);
            $data['title'] = $row->title;
            $result = [
                'code' => '200',
                'msg' => '获取信息成功',
                'data' => [
                    'wineList' => $data
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
        $param = json_decode($param['wineList'], true);

        foreach ($param as $row) {
            unset($row['create_time']);
            unset($row['update_time']);

            $orderId = $row['order_id'];
            $row['user_id'] = $this->user['id'];
            $row['salesman'] = $this->user['id'];
            $row['item_check_status']  = 0;
            $model = OrderWine::get($row['id']);
            $result = $model->allowField(true)->save($row);
            $source['wine'][] = $model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($orderId);
            $intro = "编辑酒水审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}