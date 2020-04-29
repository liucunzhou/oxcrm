<?php
namespace app\h5\controller\order;

use app\common\model\OrderWeddingReceivables;
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
    public function create()
    {
        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'incomePaymentList' => $this->config['payments']
            ]
        ];
        return json($result);
    }

    # 编辑婚庆子合同
    public function doCreate()
    {
        $this->model->startTrans();

        $param = $this->request->param();
        $suborder = json_decode($param['weddingSuborderList'], true);
        $suborder['order_id'] = $param['order_id'];
        $suborder['salesman'] = $this->user['id'];
        $result1 = $this->model->allowField(true)->save($suborder);

        $income = json_decode($param['wedding_incomeList'], true);
        $income['order_id'] = $param['order_id'];
        $income['wedding_income_type'] = 5;
        $income['remark'] = $param['income_remark'];
        $receivable = new OrderWeddingReceivables();
        $result2 = $receivable->allowField(true)->save($income);

        if($result1 && $result2) {
            $this->model->commit();
            $result = [
                'code' => '200',
                'msg' => '添加婚庆二销成功'
            ];
        } else {
            $this->model->rollback();
            $result = [
                'code' => '400',
                'msg' => '添加婚庆二销失败'
            ];
        }

        return json($result);
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