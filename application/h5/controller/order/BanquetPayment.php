<?php

namespace app\h5\controller\order;

use app\common\model\OrderBanquetPayment;
use app\h5\controller\Base;

class BanquetPayment extends Base
{
    protected $model = null;

    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderBanquetPayment();
    }

    # 获取婚宴付款信息
    public function edit($id)
    {
        ## 获取婚宴付款信息
        $fields = '*';
        $data = OrderBanquetPayment::field($fields)->get($id);

        if($data) {
            $result = [
                'code'  => '200',
                'msg'   => '获取婚宴付款信息成功',
                'data'  => [
                    'detail'    => $data
                ]
            ];
        } else {
            $result = [
                'code'  => '400',
                'msg'   => '获取婚宴付款信息失败',
            ];
        }

        return json($result);
    }

    public function doEdit()
    {
        $param = $this->request->param();
        if(!empty($param['id'])) {
            $action = '更新';
            $model = OrderBanquetPayment::get($param['id']);
        } else {
            $action = '添加';
            $model = new OrderBanquetPayment();
        }

        $result = $model->save($param);
        if($result) {
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

}