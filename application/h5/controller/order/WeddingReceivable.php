<?php

namespace app\index\controller\order;

use app\common\model\OrderWeddingReceivables;
use app\h5\controller\Base;

class WeddingReceivable extends Base
{
    protected $model = null;

    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderWeddingReceivables();
    }

    # 婚庆收款信息
    public function edit($id)
    {
        $fields = "*";
        $data = OrderWeddingReceivables::field($fields)->get($id);
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

    public function doEdit()
    {
        $param = $this->request->param();
        if(!empty($param['id'])) {
            $action = '更新';
            $model = OrderWeddingReceivables::get($param['id']);
        } else {
            $action = '添加';
            $model = new OrderWeddingReceivables();
        }

        $result = $model->save($param);
        if($result) {
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }
}