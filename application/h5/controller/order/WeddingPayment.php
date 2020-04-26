<?php
namespace app\h5\controller\order;

use app\common\model\OrderWeddingPayment;
use app\h5\controller\Base;

class WeddingPayment extends Base
{
    protected $model = null;

    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderWeddingPayment();
    }

    # 编辑婚庆信息
    public function edit($id)
    {
        $fields = "*";
        $data = OrderWeddingPayment::field($fields)->get($id);
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

    # 执行编辑婚庆信息逻辑
    public function doEdit()
    {
        $param = $this->request->param();
        if(!empty($param['id'])) {
            $action = '更新';
            $model = OrderWeddingPayment::get($param['id']);
        } else {
            $action = '添加';
            $model = new OrderWeddingPayment();
        }

        $result = $model->save($param);
        if($result) {
            ### 添加操作日志
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }
}