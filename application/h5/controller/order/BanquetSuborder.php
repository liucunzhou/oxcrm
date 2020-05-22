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
        if(empty($suborder['company_id'])) {
            $this->model->rollback();
            $result = [
                'code' => '400',
                'msg' => '请选择承办公司'
            ];
            return json($result);
        }
        $suborder['order_id'] = $param['order_id'];
        $suborder['salesman'] = $this->user['id'];
        $result1 = $this->model->allowField(true)->save($suborder);
        $source['banquetSuborder'][] = $this->model->toArray();

        // 添加收款信息
        $income = json_decode($param['banquet_incomeList'], true);
        $income['order_id'] = $param['order_id'];
        $income['user_id'] = $this->user['id'];
        $income['banquet_income_type'] = 5;
        $income['remark'] = $income['income_remark'];
        $income['banquet_receivable_no'] = $income['receivable_no'];
        $income['contract_img'] = implode(',', $income['contact_img']);
        $income['receipt_img'] = implode(',', $income['receipt_img']);
        $income['note_img'] = implode(',', $income['note_img']);

        $receivable = new OrderBanquetReceivables();
        $result2 = $receivable->allowField(true)->save($income);
        $source['banquetIncome'][] = $receivable->toArray();

        if($result1 && $result2) {
            $this->model->commit();
            create_order_confirm($param['order_id'], $suborder['company_id'], $this->user['id'], 'suborder', "创建婚宴二销订单收款审核", $source);
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
        $action = '更新';

        $suborder = json_decode($param['banquetSuborderList'], true);
        unset($suborder['create_time']);
        unset($suborder['update_time']);
        unset($suborder['delete_time']);

        $model = OrderBanquetSuborder::get($suborder['id']);
        $intro = "编辑婚宴二销订单";
        $model->startTrans();
        $model->user_id = $this->user['id'];
        $suborder['item_check_status']  = 0;
        $result1 = $model->save($suborder);
        $source['banquetSuborder'][] = $model->toArray();

        // 添加收款信息
        $income = json_decode($param['banquet_incomeList'], true);
        unset($income['create_time']);
        unset($income['update_time']);
        unset($income['delete_time']);

        $income['user_id'] = $this->user['id'];
        $income['banquet_income_type'] = 5;
        $income['remark'] = $income['income_remark'];
        $income['banquet_receivable_no'] = $income['receivable_no'];
        $income['contract_img'] = is_array($income['contact_img']) ? implode(',', $income['contact_img']) : $income['contact_img'];
        $income['receipt_img'] = is_array($income['receipt_img']) ? implode(',', $income['receipt_img']) : $income['receipt_img'];
        $income['note_img'] = is_array($income['note_img']) ? implode(',', $income['note_img']) : $income['note_img'];
        $income['item_check_status'] = 0;

        $receivable = OrderBanquetReceivables::get($income['id']);
        $result2 = $receivable->allowField(true)->save($income);
        $source['banquetIncome'][] = $receivable->toArray();

        if($result1 || $result2) {
            $model->commit();
            create_order_confirm($model->order_id, $model->company_id, $this->user['id'], 'suborder', $intro, $source);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            $model->rollback();
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }
}