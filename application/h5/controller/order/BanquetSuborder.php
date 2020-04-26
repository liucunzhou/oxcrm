<?php

namespace app\index\controller\order;

use app\common\model\OrderBanquetSuborder;
use app\h5\controller\Base;

class BanquetSuborder extends Base
{
    protected $model = null;

    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderBanquetSuborder();
    }

    # 编辑婚宴子合同
    public function edit($id)
    {
        $data = OrderBanquetSuborder::get($id);
        if($data) {
            $result = [
                'code' => '200',
                'msg' => '获取数据成功',
                'data' => [
                    'detail' => $data
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

    # 添加/编辑婚宴子合同
    public function doEdit()
    {
        $param = $this->request->param();
        if(!empty($param['id'])) {
            $action = '更新';
            $model = OrderBanquetSuborder::get($param['id']);
        } else {
            $action = '添加';
            $model = new OrderBanquetSuborder();
        }

        $model->startTrans();
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