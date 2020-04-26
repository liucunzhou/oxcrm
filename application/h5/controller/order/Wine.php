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
            $data['wine_title'] = $row->title;
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
            $result = $model->save($param);
        } else {
            $result = $this->model->allowField(true)->save($param);
        }

        if($result) {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息成功'];
        } else {
            $arr = ['code'=>'200', 'msg'=>'编辑基本信息失败'];
        }

        return json($arr);
    }
}