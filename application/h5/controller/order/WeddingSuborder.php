<?php
namespace app\h5\controller\order;

use app\common\model\Brand;
use app\common\model\OrderWeddingReceivables;
use app\common\model\OrderWeddingSuborder;
use app\h5\controller\Base;

class WeddingSuborder extends Base
{
    protected $model = null;
    protected $companyList = [];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new OrderWeddingSuborder();
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
        $source['weddingSuborder'][] = $this->model->toArray();

        $income = json_decode($param['wedding_incomeList'], true);
        $income['order_id'] = $param['order_id'];
        $income['user_id'] = $this->user['id'];
        $income['wedding_income_type'] = 5;
        $income['remark'] = $income['income_remark'];
        $income['wedding_receivable_no'] = $income['receivable_no'];
        $income['contract_img'] = implode(',', $income['imageArray']);
        $income['receipt_img'] = implode(',', $income['receipt_imgArray']);
        $income['note_img'] = implode(',', $income['note_img_imgArray']);
        $receivable = new OrderWeddingReceivables();
        $result2 = $receivable->allowField(true)->save($income);
        $source['weddingIncome'][] = $receivable->toArray();

        if($result1 && $result2) {
            $this->model->commit();
            create_order_confirm($param['order_id'], $suborder['company_id'], $this->user['id'], 'suborder', "创建婚庆二销订单收款审核", $source);
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
                    'companyList'   => array_values($this->companyList),
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
        $model = OrderWeddingSuborder::get($param['id']);
        $intro = "编辑婚庆二销订单";

        $model->startTrans();
        $model->wedding_items = json_encode($param['items']);
        $model->user_id = $this->user['id'];
        $result1 = $model->save($param);
        $source['weddingSuborder'][] = $model->toArray();
        if($result1) {
            $model->commit();
            create_order_confirm($model->order_id, $model->company_id, $this->user['id'], 'suborder', $intro, $source);
            return json(['code'=>'200', 'msg'=> '更新成功']);
        } else {
            $model->rollback();
            return json(['code'=>'500', 'msg'=> '更新失败']);
        }
    }
}