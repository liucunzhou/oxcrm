<?php

namespace app\h5\controller\order;

use app\common\model\Brand;
use app\common\model\OrderBanquetReceivables;
use app\common\model\OrderBanquetSuborder;
use app\common\model\User;
use app\h5\controller\Base;

class BanquetSuborder extends Base
{
    protected $model = null;
    protected $companyList = [];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderBanquetSuborder();

        $this->companyList = Brand::getBrands();
    }

    # 编辑婚庆子合同
    public function create()
    {

        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'companyList'   => array_values($this->companyList),
                'incomePaymentList' => $this->config['payments'],
            ]
        ];
        return json($result);
    }

    # 编辑婚庆子合同
    public function doCreate()
    {
        $this->model->startTrans();

        $param = $this->request->param();
        // 添加二销信息
        $suborder = json_decode($param['banquetSuborderList'], true);
        $suborder['order_id'] = $param['order_id'];
        $suborder['salesman'] = $this->user['id'];
        $result1 = $this->model->allowField(true)->save($suborder);

        // 添加收款信息
        $income = json_decode($param['banquet_incomeList'], true);
        if(empty($income['company_id'])) {
            $this->model->rollback();
            $result = [
                'code' => '400',
                'msg' => '请选择承办公司'
            ];
            return json($result);
        }

        $income['order_id'] = $param['order_id'];
        $income['user_id'] = $this->user['id'];
        $income['banquet_income_type'] = 5;
        $income['remark'] = $param['income_remark'];
        $receivable = new OrderBanquetReceivables();
        $result2 = $receivable->allowField(true)->save($income);

        if($result1 && $result2) {
            $this->model->commit();
            create_order_confirm($param['order_id'], $income['company_id'], $this->user['id'], 'income');
            $result = [
                'code' => '200',
                'msg' => '添加婚宴二销成功'
            ];
        } else {
            $this->model->rollback();
            $result = [
                'code' => '400',
                'msg' => '添加婚宴二销失败'
            ];
        }


        return json($result);
    }

    # 编辑婚宴子合同
    public function edit($id)
    {
        $fields = "create_time,update_time,delete_time";
        $data = OrderBanquetSuborder::field($fields, true)->get($id);
        if($data) {
            $result = [
                'code' => '200',
                'msg' => '获取数据成功',
                'data' => [
                    'companyList'   => array_values($this->companyList),
                    'banquetSuborderList' => $data
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
        $param = json_decode($param['banquetSuborderList'], true);
        if(!empty($param['id'])) {
            $action = '更新';
            $model = OrderBanquetSuborder::get($param['id']);
        } else {
            $action = '添加';
            $model = new OrderBanquetSuborder();
        }

        $model->startTrans();
        $model->user_id = $this->user['id'];
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