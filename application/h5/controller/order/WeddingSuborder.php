<?php
namespace app\h5\controller\order;

use app\common\model\OrderWeddingSuborder;
use app\h5\controller\Base;

class WeddingSuborder extends Base
{
    protected $model = null;

    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderWeddingSuborder();
    }

    # 编辑婚庆子合同
    public function edit($id)
    {
        $fields = "create_time,update_time,delete_time";
        $data = OrderWeddingSuborder::field($fields, true)->get($id);
        if($data) {
            $result = [
                'code' => '200',
                'msg' => '获取数据成功',
                'data' => [
                    'weddingSuborderList' => $data
                ]
            ];
        } else {
            $result = [
                'code' => '400',
                'msg' => '获取数据失败'
            ];
        }
        return json($result);
    }

    # 添加/编辑婚庆子合同
    public function doEdit()
    {
        $param = $this->request->param();
        $param = json_decode($param['weddingSuborderList'], true);
        if(!empty($param['id'])) {
            $action = '更新';
            $model = OrderWeddingSuborder::get($param['id']);
        } else {
            $action = '添加';
            $model = new OrderWeddingSuborder();
        }
        $model->startTrans();
        $model->wedding_items = json_encode($param['items']);
        $result1 = $model->save($param);
        if($result1) {
            $model->commit();
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            $model->rollback();
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }
}