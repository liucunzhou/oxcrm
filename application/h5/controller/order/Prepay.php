<?php

namespace app\h5\controller\order;


use app\common\model\MemberAllocate;
use app\common\model\User;
use app\h5\controller\Base;

class Prepay extends Base
{
    protected $staffs = [];
    protected $brands = [];
    protected $hotels = [];
    protected $carList = [];
    protected $sugarList = [];
    protected $wineList = [];
    protected $lightList = [];
    protected $dessertList = [];
    protected $ledList = [];
    protected $d3List = [];
    protected $packageList = [];
    protected $ritualList = [];
    protected $confirmStatusList = [0 => '待审核', 1 => '审核中', 2 => '审核通过', 3 => '审核驳回'];

    protected function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Order();

        ## 获取所有品牌、公司
        $this->brands = \app\common\model\Brand::getBrands();

        ## 套餐列表
        $this->packageList = \app\common\model\Package::getList();

        ## 套餐列表
        $this->ritualList = \app\common\model\Ritual::getList();

        ## 汽车列表
        $this->carList = \app\common\model\Car::getList();

        ## 酒水列表
        $this->wineList = \app\common\model\Wine::getList();

        ## 喜糖列表
        $this->sugarList = \app\common\model\Sugar::getList();

        ## 灯光列表
        $this->lightList = \app\common\model\Light::getList();

        ## 点心列表
        $this->dessertList = \app\common\model\Dessert::getList();

        ## led列表
        $this->ledList = \app\common\model\Led::getList();

        ## 3d列表
        $this->d3List = \app\common\model\D3::getList();

        if (isset($this->role['auth_type']) && $this->role['auth_type'] > 0) {
            $this->staffs = User::getUsersByDepartmentId($this->user['department_id']);
        }
    }

    # 创建订单
    public function create()
    {
        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'companyList' => array_values($this->brands),    ##签约公司列表
                'newsTypeList' => array_values($this->config['news_type_list']),
                'payments' => $this->config['payments'],
            ]
        ];

        return json($result);
    }

    # 创建订单
    public function doCreate()
    {
        $param = $this->request->param();
        $allocate = MemberAllocate::get($param['id']);
        $member = \app\api\model\Member::get($allocate->member_id);

        ### 格式化数据
        $orderData = json_decode($param['order'], true);
        // $orderData['news_type'] = $orderData['newsType'];
        $orderData['complete'] = 99;
        $orderData['prepay_money'] = $orderData['income_item_price'];
        $orderData['member_id'] = $member->id;
        $orderData['realname'] = $member->realname;
        $orderData['mobile'] = $member->mobile;
        $orderData['source_id'] = $member->source_id;
        $orderData['source_text'] = $member->source_text;
        $orderData['operate_id'] = $this->user['id'];
        $orderData['user_id'] = $this->user['id'];
        $orderData['salesman'] = $this->user['id'];
        $orderData['image'] = empty($orderData['imageArray']) ? '' : implode(',', $orderData['imageArray']);

        $OrderModel = new \app\common\model\Order();
        $result = $OrderModel->allowField(true)->save($orderData);
        if (!$result || !isset($OrderModel->id)) {
            return json(['code' => '400', 'msg' => '创建失败']);
        }
        $source['order'] = $OrderModel->toArray();
        if (!empty($param['income'])) {
            $income = json_decode($param['income'], true);
            if ($orderData['news_type'] == '2' || $orderData['news_type'] == '0') {
                // 婚宴收款
                $data = [];
                $data['banquet_receivable_no'] = $income['receivable_no'];
                $data['banquet_income_date'] = $income['income_date'];
                $data['banquet_income_payment'] = $income['income_payment'];
                $data['banquet_income_type'] = 4;
                $data['banquet_income_item_price'] = $income['income_item_price'];
                $data['remark'] = $income['income_remark'];
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['image'] = empty($income['image']) ? '' : implode(',', $income['image']);
                $data['receipt_img'] = empty($income['receipt_imgArray']) ? '' : implode(',', $income['receipt_imgArray']);
                $data['note_img'] = empty($income['note_imgArray']) ? '' : implode(',', $income['note_imgArray']);

                $receivableModel = new OrderBanquetReceivables();
                $receivableModel->allowField(true)->save($data);
                $source['banquetIncome'][] = $receivableModel->toArray();
            } else {
                // 婚庆收款
                $data = [];
                $data['wedding_receivable_no'] = $income['receivable_no'];
                $data['wedding_income_date'] = $income['income_date'];
                $data['wedding_income_payment'] = $income['income_payment'];
                $data['wedding_income_type'] = 1;
                $data['wedding_income_item_price'] = $income['income_item_price'];
                $data['remark'] = $income['income_remark'];
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['receipt_img'] = empty($income['receipt_imgArray']) ? '' : implode(',', $income['receipt_imgArray']);
                $data['note_img'] = empty($income['note_imgArray']) ? '' : implode(',', $income['note_imgArray']);

                $receivableModel = new OrderWeddingReceivables();
                $receivableModel->allowField(true)->save($data);
                $source['weddingIncome'][] = $receivableModel->toArray();
            }
        }

        // 根据公司创建审核流程
        create_order_confirm($OrderModel->id, $orderData['company_id'], $this->user['id'], 'prepay', "创建意向金审核", $source);
        return json(['code' => '200', 'msg' => '创建成功']);
    }

    public function doEdit()
    {
        $param = $this->request->param();

        $post = json_decode($param['order'], true);
        unset($post['create_time']);
        unset($post['update_time']);
        unset($post['delete_time']);

        $post['image'] = empty($post['image']) ? '' : implode(',', $post['image']);
        $order = \app\common\model\Order::get($post['id']);
        $order->allowField(true)->save($post);
        $source['order'] = $order->toArray();

        ## 收款信息
        if (!empty($param['income'])) {
            $income = json_decode($param['income'], true);

            if ($post['news_type'] == '2' || $post['news_type'] == '0') {
                // 婚宴收款
                $data = [];
                $data['banquet_receivable_no'] = $income['receivable_no'];
                $data['banquet_income_date'] = $income['income_date'];
                $data['banquet_income_payment'] = $income['income_payment'];
                $data['banquet_income_type'] = 1;
                $data['banquet_income_item_price'] = $income['income_item_price'];
                $data['remark'] = $income['income_remark'];
                $data['order_id'] = $post['od'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['receipt_img'] = empty($income['receipt_imgArray']) ? '' : implode(',', $income['receipt_imgArray']);
                $data['note_img'] = empty($income['note_imgArray']) ? '' : implode(',', $income['note_imgArray']);
                $data['item_check_status']  = 0;

                $income = OrderBanquetReceivables::get($income['id']);
                $income->allowField(true)->save($data);
                $source['banquetIncome'][] = $income->toArray();
            } else {
                // 婚庆收款
                $data = [];
                $data['wedding_receivable_no'] = $income['receivable_no'];
                $data['wedding_income_date'] = $income['income_date'];
                $data['wedding_income_payment'] = $income['income_payment'];
                $data['wedding_income_type'] = 1;
                $data['wedding_income_item_price'] = $income['income_item_price'];
                $data['remark'] = $income['income_remark'];
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['receipt_img'] = empty($income['receipt_imgArray']) ? '' : implode(',', $income['receipt_imgArray']);
                $data['note_img'] = empty($income['note_imgArray']) ? '' : implode(',', $income['note_imgArray']);
                $data['item_check_status']  = 0;

                $income = OrderWeddingReceivables::get($income['id']);
                $income->allowField(true)->save($data);
                $source['weddingIncome'][] = $income->toArray();
            }
        }

        // 根据公司创建审核流程
        create_order_confirm($post['id'], $post['company_id'], $this->user['id'], 'order', "编辑意向金审核", $source);
        return json(['code' => '200', 'msg' => '编辑订单成功']);

    }
}