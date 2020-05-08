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
        if(!empty($param['id'])) {
            $where = [];
            $where[] = ['id', '=', $param['id']];
            $model = $this->model->where($where)->find();
            $result = $model->allowField(true)->save($param);
            $source['wine'][] = $model->toArray();
        } else {
            $result = $this->model->allowField(true)->save($param);
            $source['wine'][] = $this->model->toArray();
        }

        if($result) {
            $order = \app\common\model\Order::get($param['order_id']);
            $intro = "编辑酒水审核";
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}