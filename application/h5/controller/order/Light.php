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
        if(!empty($param['id'])) {
            $where = [];
            $where[] = ['id', '=', $param['id']];
            $model = $this->model->where($where)->find();
            $result = $model->allowField(true)->save($param);
            $source['light'][] = $model->toArray();
        } else {
            $result = $this->model->allowField(true)->save($param);
            $source['light'][] = $this->model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($param['order_id']);
            $intro = "编辑灯光审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}