<?php

namespace app\h5\controller\order;

use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\OrderBanquet;
use app\common\model\OrderBanquetReceivables;
use app\common\model\OrderBanquetSuborder;
use app\common\model\OrderCar;
use app\common\model\OrderConfirm;
use app\common\model\OrderD3;
use app\common\model\OrderDessert;
use app\common\model\OrderHotelItem;
use app\common\model\OrderHotelProtocol;
use app\common\model\OrderLed;
use app\common\model\OrderLight;
use app\common\model\OrderSugar;
use app\common\model\OrderWedding;
use app\common\model\OrderWeddingReceivables;
use app\common\model\OrderWine;
use app\common\model\Package;
use app\common\validate\OrderCommission;
use app\h5\controller\Base;
use app\common\model\BanquetHall;
use app\common\model\OrderEntire;
use app\common\model\User;
use app\common\model\Search;
use app\index\controller\organization\Audit;

class Order extends Base
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
    protected $confirmStatusList = [0 => '待审核', 1 => '审核中', 2 => '审核通过', 3 => '审核驳回', 13 => '审核撤销'];

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

    ##
    public function index()
    {
        $param = $this->request->param();
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 3;
        $param['page'] = isset($param['page']) ? (int)$param['page'] + 1 : 1;
        $config = [
            'page' => $param['page']
        ];

        ##  审核状态
        if (isset($param['check_status']) && $param['check_status'] != 'all' && $param['check_status'] != '') {
            $map[] = ['check_status', '=', $param['check_status']];
        }

        ## 签约公司
        if (isset($param['company_id']) && $param['company_id'] > 0) {
            $map[] = ['company_id', '=', $param['company_id']];
        }

        ## 时间类型
        if (isset($param['range']) && !empty($param['range'])) {
            $column = !empty($param['range_type']) ? $param['range_type'] : 'create_time';
            $range = format_date_range($param['range']);
            $map[] = [$column, 'between', $range];
        }

        ###  管理者还是销售
        if ($this->role['auth_type'] > 0) {
            ### 员工列表
            if (isset($param['user_id']) && !empty($param['user_id'])) {

                if ($param['user_id'] == 'all') {
                    $map[] = ['salesman', 'in', $this->staffs];
                } else if (is_numeric($param['user_id'])) {
                    $map[] = ['salesman', '=', $this->user['id']];
                } else {
                    $map[] = ['salesman', 'in', $param['user_id']];
                }

            } else {
                $map[] = ['salesman', '=', $this->user['id']];
            }
        } else {
            $map[] = ['salesman', '=', $this->user['id']];
        }


        $fields = "id,contract_no,company_id,news_type,sign_date,status,event_date,hotel_text,cooperation_mode,bridegroom,bridegroom_mobile,bride,bride_mobile,item_check_status";
        $model = new \app\common\model\Order();
        $userId = $this->user['id'];
        $model = $model->where($map)->whereOr('id', 'in', function ($query) use ($userId) {
            $query->table('tk_order_staff')->where('staff_id', '=', $userId)->field('order_id');
        });

        if (isset($param['keywords']) && !empty($param['keywords'])) {
            if (is_numeric($param['keywords'])) {
                $model->where('bride_mobile|bridegroom_mobile', 'like', "%{$param['keywords']}%");
            } else {
                $model->where('bride|bridegroom', 'like', "%{$param['keywords']}%");
            }
        }

        $list = $model->field($fields)->order('id desc')->paginate($param['limit'], false, $config);
        if (!$list->isEmpty()) {
            $list = $list->getCollection()->toArray();
            $newsTypes = $this->config['news_type_list'];
            $cooperationMode = $this->config['cooperation_mode'];

            foreach ($list as $key => &$value) {
                $value['company_id'] = $this->brands[$value['company_id']]['title'];
                $value['news_type'] = $newsTypes[$value['news_type']];
                $value['cooperation_mode'] = isset($cooperationMode[$value['cooperation_mode']]) ? $cooperationMode[$value['cooperation_mode']] : '-';
                $value['status'] = $this->confirmStatusList[$value['item_check_status']];
                $value['sign_date'] = substr($value['sign_date'], 0, 10);
                $value['event_date'] = substr($value['event_date'], 0, 10);
                $value['bridegroom_mobile'] = isset($value['bridegroom_mobile']) ? substr_replace($value['bridegroom_mobile'], '***', 3, 3) : '-';
                $value['bride_mobile'] = isset($value['bride_mobile']) ? substr_replace($value['bride_mobile'], '***', 3, 3) : '-';
            }
        } else {
            $list = [];
        }

        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'orderlist' => $list,
            ]
        ];
        return json($result);
    }

    ##
    public function detail()
    {
        $param = $this->request->param();
        // $fields = "id,contract_no,score,company_id,news_type,banquet_hall_name,sign_date,event_date,hotel_text,cooperation_mode,bridegroom,salesman,recommend_salesman,bridegroom_mobile,bride,bride_mobile,totals,earnest_money_date,earnest_money,middle_money_date,middle_money,tail_money_date,tail_money,remark";
        $order = $this->model->where('id', '=', $param['id'])->find();
        if (empty($order)) {
            $result = [
                'code' => '200',
                'msg' => '订单不存在'
            ];
            return json($result);
        }

        $orderId = $order->id;
        $companyId = $order->company_id;
        $order = $order->getData();
        $newsTypes = $this->config['news_type_list'];
        $cooperationMode = $this->config['cooperation_mode'];
        $order['contract_no'] = isset($order['contract_no']) ? $order['contract_no'] : '-';
        $order['company_id'] = $this->brands[$order['company_id']]['title'];
        $order['news_type'] = $newsTypes[$order['news_type']];
        $order['cooperation_mode'] = isset($cooperationMode[$order['cooperation_mode']]) ? $cooperationMode[$order['cooperation_mode']] : '-';
        $order['status'] = $this->confirmStatusList[$order['check_status']];
        $order['sign_date'] = $order['sign_date'] ? date('Y-m-d', $order['sign_date']) : '-';
        $order['event_date'] = $order['event_date'] ? date('Y-m-d', $order['event_date']) : '-';
        $order['earnest_money_date'] = $order['earnest_money_date'] ? date('Y-m-d', $order['earnest_money_date']) : '-';
        $order['middle_money_date'] = $order['middle_money_date'] ? date('Y-m-d', $order['middle_money_date']) : '-';
        $order['tail_money_date'] = $order['tail_money_date'] ? date('Y-m-d', $order['tail_money_date']) : '-';
        $order['bridegroom_mobile'] = isset($order['bridegroom_mobile']) ? substr_replace($order['bridegroom_mobile'], '***', 3, 3) : '-';
        $order['bride_mobile'] = isset($order['bride_mobile']) ? substr_replace($order['bride_mobile'], '***', 3, 3) : '-';
        $order['image'] = empty($order['image']) ? [] : explode(',', $order['image']);
        $order['receipt_img'] = empty($order['receipt_img']) ? [] : explode(',', $order['receipt_img']);
        $order['note_img'] = empty($order['note_img']) ? [] : explode(',', $order['note_img']);
        $staff = User::getUser($order['salesman']);
        $order['salesman'] = $staff['realname'];

        #### 审核流程
        // $audit = \app\common\model\Audit::where('company_id', '=', $companyId)->find();
        // $sequence = empty($audit) ? [] : json_decode($audit->content, true);
        #### 检测编辑和添加权限
        #### 不管是谁 只要存在未被审核的将视为不能编辑，驳回后会添加新的未审核，审核后会修改is_checked=1
        $where = [];
        $where[] = ['order_id', '=', $order['id']];
        // $where[] = ['company_id', '=', $order['company_id']];
        $where[] = ['user_id', '=', $this->user['id']];
        // $where[] = ['status', '=', 3];
        $confirmLast = OrderConfirm::where($where)->order('id desc')->find();
        if (empty($confirmLast)) {
            // 获取审核状态
            if ($order['check_status'] == '0') {
                // 待审核
                $edit = 0;
                $orderEdit = 0;
                $orderAdd = 0;
            } else if ($order['check_status'] == '1') {
                // 审核中
                $edit = 0;
                $orderEdit = 0;
                $orderAdd = 0;
            } else if ($order['check_status'] == '2') {
                // 审核通过
                $edit = 0;
                $orderEdit = 1;
                $orderAdd = 1;
            } else if ($order['check_status'] == '3') {
                // 审核驳回
                $edit = 1;
                $orderEdit = 1;
                $orderAdd = 1;
            } else {
                // 审核中
                $edit = 0;
                $orderEdit = 0;
                $orderAdd = 0;
            }
        } else {
            if ($confirmLast->status == '0') {
                // 待审核
                $edit = 0;
                $orderEdit = 0;
                $orderAdd = 0;
            } else if ($confirmLast->status == '1') {
                // 审核通过
                $edit = 0;
                $orderEdit = 1;
                $orderAdd = 1;
            } else if ($confirmLast->status == '2') {
                // 审核驳回
                $edit = 1;
                $orderEdit = 1;
                $orderAdd = 1;
            } else {
                // 审核中
                $edit = 0;
                $orderEdit = 0;
                $orderAdd = 0;
            }
        }

        #### 获取用户信息
        $member = Member::field('realname,mobile,source_text')->where('id', '=', $order->member_id)->find();

        #### 获取婚宴订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $banquet = \app\common\model\OrderBanquet::where($where)->order('id desc')->find();
        if (empty($banquet)) {
            $banquet = [];
        } else {
            $banquet['company_id'] = $this->brands[$banquet->company_id]['title'];
            $banquet['banquet_ritual_title'] = $this->ritualList[$banquet->banquet_ritual_id]['title'];
        }

        #### 酒店服务项目
        $where = [];
        $where['order_id'] = $param['id'];
        $hotelItem = \app\common\model\OrderHotelItem::where($where)->order('id desc')->find();
        if (empty($hotelItem)) $hotelItem = [];

        #### 获取婚宴二销订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $banquetSuborderList = \app\common\model\OrderBanquetSuborder::where($where)->select();
        foreach ($banquetSuborderList as &$row) {
            if ($row['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $row['edit'] = 1;
            } else {
                $row['edit'] = 0;
            }
            $row['receipt_img'] = empty($row['receipt_img']) ? [] : explode(',', $row['receipt_img']);
            $row['note_img'] = empty($row['note_img']) ? [] : explode(',', $row['note_img']);
        }

        #### 获取婚宴收款信息
        $banquetReceivableList = \app\common\model\OrderBanquetReceivables::where('order_id', '=', $param['id'])->select();

        #### 获取婚宴付款信息
        $banquetPaymentList = \app\common\model\OrderBanquetPayment::where('order_id', '=', $param['id'])->select();

        #### 获取婚庆订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $wedding = \app\common\model\OrderWedding::where($where)->order('id desc')->find();
        if (empty($wedding)) {
            $wedding = [];
        } else {
            $wedding['company_id'] = $this->brands[$wedding->company_id]['title'];
            $wedding['wedding_package_title'] = $this->packageList[$wedding->wedding_package_id]['title'];
            $wedding['wedding_ritual_title'] = $this->ritualList[$wedding->wedding_ritual_id]['title'];
        }

        #### 获取婚宴二销订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $weddingSuborderList = \app\common\model\OrderWeddingSuborder::where($where)->select();
        foreach ($weddingSuborderList as &$row) {
            if ($row['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $row['edit'] = 1;
            } else {
                $row['edit'] = 0;
            }
            $row['receipt_img'] = empty($row['receipt_img']) ? [] : explode(',', $row['receipt_img']);
            $row['note_img'] = empty($row['note_img']) ? [] : explode(',', $row['note_img']);
        }

        #### 获取婚宴收款信息
        $weddingReceivableList = \app\common\model\OrderWeddingReceivables::where('order_id', '=', $param['id'])->select();
        #### 获取婚庆付款信息
        $weddingPaymentList = \app\common\model\OrderWeddingPayment::where('order_id', '=', $param['id'])->select();

        #### 婚车
        $where = [];
        $where['order_id'] = $param['id'];
        $carList = \app\common\model\OrderCar::where($where)->select();
        foreach ($carList as $key => &$row) {
            $row['car_id'] = $this->carList[$row['car_id']]['title'];
            $row['is_master'] = $row['is_master'] == '1' ? '主车' : '跟车';
            if ($row['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $row['edit'] = 1;
            } else {
                $row['edit'] = 0;
            }
        }

        #### 喜糖
        $where = [];
        $where['order_id'] = $param['id'];
        $sugarList = \app\common\model\OrderSugar::where($where)->select();
        foreach ($sugarList as $key => &$row) {
            $row['sugar_id'] = $this->sugarList[$row['sugar_id']]['title'];
            if ($row['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $row['edit'] = 1;
            } else {
                $row['edit'] = 0;
            }
        }

        #### 酒水
        $where = [];
        $where['order_id'] = $param['id'];
        $wineList = \app\common\model\OrderWine::where($where)->select();
        foreach ($wineList as $key => &$row) {
            $row['wine_id'] = $this->wineList[$row['wine_id']]['title'];
            if ($row['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $row['edit'] = 1;
            } else {
                $row['edit'] = 0;
            }
        }

        #### 灯光
        $where = [];
        $where['order_id'] = $param['id'];
        $lightList = \app\common\model\OrderLight::where($where)->select();
        foreach ($lightList as $key => &$row) {
            $row['light_id'] = $this->lightList[$row['light_id']]['title'];
            if ($row['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $row['edit'] = 1;
            } else {
                $row['edit'] = 0;
            }
        }

        #### 点心
        $where = [];
        $where['order_id'] = $param['id'];
        $dessertList = \app\common\model\OrderDessert::where($where)->select();
        foreach ($dessertList as $key => &$row) {
            $row['dessert_id'] = $this->dessertList[$row['dessert_id']]['title'];
            if ($row['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $row['edit'] = 1;
            } else {
                $row['edit'] = 0;
            }
        }

        #### LED
        $where = [];
        $where['order_id'] = $param['id'];
        $ledList = \app\common\model\OrderLed::where($where)->select();
        foreach ($ledList as $key => &$row) {
            $row['led_id'] = $this->ledList[$row['led_id']]['title'];
            if ($row['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $row['edit'] = 1;
            } else {
                $row['edit'] = 0;
            }
        }

        #### 3D
        $where = [];
        $where['order_id'] = $param['id'];
        $d3List = \app\common\model\OrderD3::where($where)->select();
        foreach ($d3List as $key => &$row) {
            $row['d3_id'] = $this->d3List[$row['d3_id']]['title'];
            if ($row['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $row['edit'] = 1;
            } else {
                $row['edit'] = 0;
            }
        }

        #### 合同金额
        $contractPrice = [
            'id' => $orderId,
            'totals' => $order['totals'],
            'earnest_money_date' => $order['earnest_money_date'],
            'earnest_money' => $order['earnest_money'],
            'middle_money_date' => $order['middle_money_date'],
            'middle_money' => $order['middle_money'],
            'tail_money_date' => $order['tail_money_date'],
            'tail_money' => $order['tail_money']
        ];

        #### 支付方式列表
        $paymentConfig = $this->config['payments'];
        $paymentConfig = array_column($paymentConfig, 'title', 'id');

        #### 支付性质
        $paymentTypes = $this->config['payment_type_list'];
        $paymentTypes = array_column($paymentTypes, 'title', 'id');

        #### 合成收款信息
        $incomeList = [];
        foreach ($banquetReceivableList as $key => $value) {
            if ($value['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $value['edit'] = 1;
            } else {
                $value['edit'] = 0;
            }

            $incomeList[] = [
                'id' => $value['id'],
                'receivable_no' => $value['banquet_receivable_no'],
                'income_category' => '婚宴',
                'income_payment' => $paymentConfig[$value['banquet_income_payment']],
                'income_type' => $paymentTypes[$value['banquet_income_type']],
                'income_date' => $value['banquet_income_date'],
                'income_real_date' => $value['banquet_income_real_date'],
                'income_item_price' => $value['banquet_income_item_price'],
                'income_remark' => $value['remark'],
                'receipt_img' => empty($value['receipt_img']) ? [] : explode(',', $value['receipt_img']),
                'note_img' => empty($value['note_img']) ? [] : explode(',', $value['note_img']),
                'edit' => $value['edit']
            ];
        }
        foreach ($weddingReceivableList as $key => $value) {
            if ($value['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $value['edit'] = 1;
            } else {
                $value['edit'] = 0;
            }

            $incomeList[] = [
                'id' => $value['id'],
                'receivable_no' => $value['wedding_receivable_no'],
                'income_category' => '婚庆',
                'income_payment' => $paymentConfig[$value['wedding_income_payment']],
                'income_type' => $paymentTypes[$value['wedding_income_type']],
                'income_date' => $value['wedding_income_date'],
                'income_real_date' => $value['wedding_income_real_date'],
                'income_item_price' => $value['wedding_income_item_price'],
                'income_remark' => $value['remark'],
                'receipt_img' => empty($value['receipt_img']) ? [] : explode(',', $value['receipt_img']),
                'note_img' => empty($value['note_img']) ? [] : explode(',', $value['note_img']),
                'edit' => $value['edit']
            ];
        }

        #### 合成付款信息
        $paymentList = [];
        foreach ($banquetPaymentList as $key => $value) {
            if ($value['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $value['edit'] = 1;
            } else {
                $value['edit'] = 0;
            }

            $paymentList[] = [
                'id' => $value['id'],
                'payment_no' => $value['banquet_payment_no'],
                'pay_category' => '婚宴',
                'pay_type' => $paymentTypes[$value['banquet_pay_type']],
                'apply_pay_date' => $value['banquet_apply_pay_date'],
                'pay_real_date' => $value['banquet_pay_real_date'],
                'pay_item_price' => $value['banquet_pay_item_price'],
                'payment_remark' => $value['banquet_payment_remark'],
                'receipt_img' => empty($value['receipt_img']) ? [] : explode(',', $value['receipt_img']),
                'note_img' => empty($value['note_img']) ? [] : explode(',', $value['note_img']),
                'edit' => $value['edit']
            ];
        }
        foreach ($weddingPaymentList as $key => $value) {
            if ($value['user_id'] == $this->user['id'] && $orderEdit == 1) {
                $value['edit'] = 1;
            } else {
                $value['edit'] = 0;
            }

            $paymentList[] = [
                'id' => $value['id'],
                'payment_no' => $value['wedding_payment_no'],
                'pay_category' => '婚庆',
                'pay_type' => $paymentTypes[$value['wedding_pay_type']],
                'apply_pay_date' => $value['wedding_apply_pay_date'],
                'pay_real_date' => $value['wedding_pay_real_date'],
                'pay_item_price' => $value['wedding_pay_item_price'],
                'payment_remark' => $value['wedding_payment_remark'],
                'receipt_img' => empty($value['receipt_img']) ? [] : explode(',', $value['receipt_img']),
                'note_img' => empty($value['note_img']) ? [] : explode(',', $value['note_img']),
                'edit' => $value['edit']
            ];
        }


        #### 获取审核进度
        $result = [
            'code' => '200',
            'msg' => '获取成功',
            'data' => [
                'edit' => $orderEdit,
                'order' => [
                    'id' => $orderId,
                    'picker' => '',
                    'read' => '/h5/order.order/edit',
                    'api' => '/h5/order.order/doEdit',
                    'json' => $order,
                    'edit' => $edit,
                ],
                'member' => [
                    'id' => $orderId,
                    'picker' => '',
                    'read' => '',
                    'api' => '',
                    'json' => $member,
                    'edit' => 0,
                ],
                'banquet' => [
                    'id' => $orderId,
                    'picker' => '',
                    'read' => '/h5/order.banquet/edit',
                    'api' => '/h5/order.banquet/doEdit',
                    'json' => $banquet,
                    'edit' => $edit,
                ],
                'banquetSuborderList' => [
                    'id' => $orderId,
                    'picker' => '',
                    'read' => '/h5/order.banquet_suborder/edit',
                    'api' => '/h5/order.banquet_suborder/doEdit',
                    'array' => $banquetSuborderList,
                    'edit' => 0,
                ],
                'hotelItem' => [
                    'id' => $orderId,
                    'picker' => '',
                    'read' => '/h5/order.hotel_item/edit',
                    'api' => '/h5/order.hotel_item/doEdit',
                    'json' => $hotelItem,
                    'edit' => $edit,
                ],
                'wedding' => [
                    'id' => $orderId,
                    'picker' => '',
                    'read' => '/h5/order.wedding/edit',
                    'api' => '/h5/order.wedding/doEdit',
                    'json' => $wedding,
                    'edit' => $edit,
                ],
                'weddingSuborderList' => [
                    'id' => $orderId,
                    'picker' => '',
                    'read' => '/h5/order.wedding_suborder/edit',
                    'api' => '/h5/order.wedding_suborder/doEdit',
                    'array' => $weddingSuborderList,
                    'edit' => 0,
                ],
                'carList' => [
                    'id' => $orderId,
                    'picker' => '/h5/dictionary.car/getList',
                    'read' => '/h5/order.car/edit',
                    'api' => '/h5/order.car/doEdit',
                    'array' => $carList
                ],
                'wineList' => [
                    'id' => $orderId,
                    'picker' => '/h5/dictionary.wine/getList',
                    'read' => '/h5/order.wine/edit',
                    'api' => '/h5/order.wine/doEdit',
                    'array' => $wineList,
                    'edit' => 0,
                ],
                'sugarList' => [
                    'id' => $orderId,
                    'picker' => '/h5/dictionary.sugar/getList',
                    'read' => '/h5/order.sugar/edit',
                    'api' => '/h5/order.sugar/doEdit',
                    'array' => $sugarList
                ],
                'dessertList' => [
                    'id' => $orderId,
                    'picker' => '/h5/dictionary.dessert/getList',
                    'read' => '/h5/order.dessert/edit',
                    'api' => '/h5/order.dessert/doEdit',
                    'array' => $dessertList,
                    'edit' => 0,
                ],
                'lightList' => [
                    'id' => $orderId,
                    'picker' => '/h5/dictionary.light/getList',
                    'read' => '/h5/order.light/edit',
                    'api' => '/h5/order.light/doEdit',
                    'array' => $lightList,
                    'edit' => 0,
                ],
                'ledList' => [
                    'id' => $orderId,
                    'picker' => '/h5/dictionary.led/getList',
                    'read' => '/h5/order.led/edit',
                    'api' => '/h5/order.led/doEdit',
                    'array' => $ledList,
                    'edit' => 0,
                ],
                'd3List' => [
                    'id' => $orderId,
                    'picker' => '/h5/dictionary.d3/getList',
                    'read' => '/h5/order.d3/edit',
                    'api' => '/h5/order.d3/doEdit',
                    'array' => $d3List,
                    'edit' => 0,
                ],
                // 合同收款信息
                'contractPrice' => [
                    'id' => $orderId,
                    'picker' => '',
                    'read' => '/h5/order.contract/edit',
                    'api' => '/h5/order.contract/doEdit',
                    'json' => $contractPrice,
                    'edit' => $edit,
                ],
                // 订单收款信息
                'incomeList' => [
                    'id' => $orderId,
                    'picker' => '',
                    'read' => '/h5/order.income/edit',
                    'api' => '/h5/order.income/doEdit',
                    'array' => $incomeList
                ],
                // 订单付款信息
                'paymentList' => [
                    'id' => $orderId,
                    'picker' => '',
                    'read' => '/h5/order.payment/edit',
                    'api' => '/h5/order.payment/doEdit',
                    'array' => $paymentList,
                    'edit' => 0,
                ],
                'addItems' => [
                    'edit' => $orderAdd,
                    'addItems' => [
                        [
                            'id' => 'suborder',
                            'title' => '二销',
                            'children' => [
                                [
                                    'id' => 'banquetSuborder',
                                    'title' => '婚宴二销',
                                    'read' => '/h5/order.banquet_suborder/create',
                                    'api' => '/h5/order.banquet_suborder/doCreate'
                                ],
                                [
                                    'id' => 'weddingSuborder',
                                    'title' => '婚庆二销',
                                    'read' => '/h5/order.wedding_suborder/create',
                                    'api' => '/h5/order.wedding_suborder/doCreate'
                                ],
                            ]
                        ],
                        [
                            'id' => 'incomeAppend',
                            'title' => '收款',
                            'read' => '/h5/order.income/create',
                            'api' => '/h5/order.income/doCreate',
                            'children' => []
                        ],

                        [
                            'id' => 'paymentAppend',
                            'title' => '付款',
                            'read' => '/h5/order.payment/create',
                            'api' => '/h5/order.payment/doCreate',
                            'children' => []
                        ],
                    ]
                ]
            ]
        ];

        return json($result);
    }

    # 创建订单视图
    public function createOrder()
    {
        $param = $this->request->param();
        $allocate = MemberAllocate::get($param['id']);
        $fields = "id,realname,mobile,source_id,source_text";
        $member = Member::field($fields)->get($allocate['member_id']);

        $moduleList = [
            [
                'id' => 'banquet',
                'title' => '婚宴'
            ],
            [
                'id' => 'wedding',
                'title' => '婚庆'
            ],
            [
                'id' => 'hotelItem',
                'title' => '酒店服务项目'
            ],
            [
                'id' => 'car',
                'title' => '婚车'
            ],
            [
                'id' => 'sugar',
                'title' => '喜糖'
            ],
            [
                'id' => 'wine',
                'title' => '酒水'
            ],
            [
                'id' => 'dessert',
                'title' => '糕点'
            ],
            [
                'id' => 'light',
                'title' => '灯光'
            ],
            [
                'id' => 'led',
                'title' => 'LED'
            ],
            [
                'id' => 'd3',
                'title' => '3D'
            ],

        ];

        ## 获取套餐列表
        $packageList = \app\common\model\Package::getList();

        ## 获取仪式列表
        $ritualList = \app\common\model\Ritual::getList();

        $result = [
            'code' => '200',
            'msg' => '获取成功',
            'data' => [
                'member' => $member,          ## 客资信息
                'moduleList' => $moduleList,

                'companyList' => array_values($this->brands),    ##签约公司列表
                'newsTypeList' => array_values($this->config['news_type_list']),    ## 订单类型
                'cooperationModeList' => array_values($this->config['cooperation_mode']),  ## 合同模式

                'packageList' => array_values($packageList),
                'ritualList' => array_values($ritualList),

                'carList' => array_values($this->carList),
                'wineList' => array_values($this->wineList),
                'sugarList' => array_values($this->sugarList),
                'dessertList' => array_values($this->dessertList),
                'lightList' => array_values($this->lightList),
                'ledList' => array_values($this->ledList),
                'd3List' => array_values($this->d3List),

                'payments' => $this->config['payments'],
            ]
        ];
        return json($result);
    }

    # 创建订单逻辑
    public function doCreateOrder()
    {
        $param = $this->request->param();
        $allocate = MemberAllocate::get($param['id']);
        $member = \app\api\model\Member::get($allocate->member_id);
        $orderData = json_decode($param['order'], true);
        if($orderData['cooperation_mode'] != '1') {
            $orderValidate = new \app\common\validate\Order();
        } else {
            $orderValidate = new OrderCommission();
        }
        if(!$orderValidate->check($orderData)) {
            return json([
                'code' => '400',
                'msg' => $orderValidate->getError()
            ]);
        }

        // $orderData['news_type'] = $orderData['newsType'];
        $orderData['member_id'] = $member->id;
        $orderData['realname'] = $member->realname;
        $orderData['mobile'] = $member->mobile;
        $orderData['source_id'] = $member->source_id;
        $orderData['source_text'] = $member->source_text;
        $orderData['operate_id'] = $this->user['id'];
        $orderData['user_id'] = $this->user['id'];
        $orderData['salesman'] = $this->user['id'];
        $orderData['image'] = empty($orderData['imageArray']) ? '' : implode(',', $orderData['imageArray']);
        $orderData['receipt_img'] = empty($orderData['receipt_imgArray']) ? '' : implode(',', $orderData['receipt_imgArray']);
        $orderData['note_img'] = empty($orderData['note_imgArray']) ? '' : implode(',', $orderData['note_imgArray']);
        $OrderModel = new \app\common\model\Order();
        $result = $OrderModel->allowField(true)->save($orderData);
        if (!$result || !isset($OrderModel->id)) return json(['code' => '400', 'msg' => '创建失败']);
        $source['order'] = $OrderModel->toArray();


        ## banquet message
        if (!empty($param['banquet'])) {
            $data = json_decode($param['banquet'], true);
            $c1 = !empty($data['table_amount']) || !empty($data['table_price']);
            $c2 = !empty($data['wine_fee']) || !empty($data['service_fee']);
            $c3 = !empty($data['banquet_update_table']) || !empty($data['banquet_total']);
            $c4 = !empty($data['banquet_discount']) || !empty($data['banquet_totals']);
            $c5 = !empty($data['banquet_ritual_id']) || !empty($data['banquet_ritual_hall']);
            $c6 = !empty($data['banquet_other']) || !empty($data['banquet_remark']);
            if ($c1 || $c2 || $c3 || $c4 || $c5 || $c6) {
                $banquetValidate = new \app\common\validate\OrderBanquet();
                if(!$banquetValidate->check($data)) {
                    return json([
                        'code' => '400',
                        'msg' => $banquetValidate->getError()
                    ]);
                }
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $BanquetModel = new OrderBanquet();
                $BanquetModel->allowField(true)->save($data);
                $source['banquet'] = $BanquetModel->toArray();
            }
        }

        ## wedding message
        if (!empty($param['wedding'])) {
            $data = json_decode($param['wedding'], true);
            $c1 = !empty($data['wedding_package_id']) || !empty($data['wedding_package_price']);
            $c2 = !empty($data['wedding_ritual_id']) || !empty($data['wedding_ritual_hall']);
            $c3 = !empty($data['wedding_other']) || !empty($data['wedding_totals']);
            $c4 = !empty($data['wedding_remark']);
            if ($c1 || $c2 || $c3 || $c4) {
                $weddingValidate = new \app\common\validate\OrderWedding();
                if(!$weddingValidate->check($data)) {
                    return json([
                        'code' => '400',
                        'msg' => $weddingValidate->getError()
                    ]);
                }

                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $WeddingModel = new OrderWedding();
                $WeddingModel->allowField(true)->save($data);
                $source['wedding'] = $WeddingModel->toArray();
            }
        }

        ## 酒店服务项目
        if (!empty($param['hotelItem'])) {
            $data = json_decode($param['hotelItem'], true);
            $c1 = !empty($data['wedding_room']) || !empty($data['wedding_room_amount']);
            $c2 = !empty($data['part']) || !empty($data['part_amount']);
            $c3 = !empty($data['champagne']) || !empty($data['champagne_amount']);
            $c4 = !empty($data['tea']) || !empty($data['tea_amount']);
            $c5 = !empty($data['cake']) || !empty($data['cake_amount']);
            if ($c1 || $c2 || $c3 || $c4 || $c5) {
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $orderHotelItem = new OrderHotelItem();
                $orderHotelItem->allowField(true)->save($data);
                $source['hotelItem'] = $orderHotelItem->toArray();
            }
        }

        ## 酒店服务项目
        if (!empty($param['hotelProtocol'])) {
            $data = json_decode($param['hotelProtocol'], true);
            $c1 = !empty($data['table_price']) || !empty($data['table_amount']);
            $c2 = !empty($data['earnest_money']) || !empty($data['earnest_money_date']);
            $c3 = !empty($data['middle_money']) || !empty($data['earnest_money_date']);
            $c4 = !empty($data['tail_money']) || !empty($data['tail_money_date']);
            $c5 = !empty($data['cake']) || !empty($data['cake_amount']);
            $c6 = !empty($data['tea']) || !empty($data['tea_amount']);
            $c7 = !empty($data['champagne']) || !empty($data['champagne_amount']);
            $c8 = !empty($data['part']) || !empty($data['part_amount']);
            $c9 = !empty($data['wedding_room']) || !empty($data['wedding_room_amount']);
            $c10 = !empty($data['pay_hotel_totals']) || !empty($data['pay_hotel_totals']);
            if ($c1 || $c2 || $c3 || $c4 || $c5 || $c6 || $c7 || $c8 || $c9 || $c10) {
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $orderHotelProtocol = new OrderHotelProtocol();
                $orderHotelProtocol->allowField(true)->save($data);
                $source['hotelProtocol'] = $orderHotelProtocol->toArray();
            }
        }

        ## 婚车主车
        if (!empty($param['car'])) {
            $carData = json_decode($param['car'], true);
            if (!empty($carData['master_car_id'])) {
                $row = [];
                $row['company_id'] = $carData['company_id'];
                $row['is_master'] = 1;
                $row['is_suborder'] = 0;
                $row['car_id'] = $carData['master_car_id'];
                $row['car_price'] = $carData['master_car_price'];
                $row['car_amount'] = $carData['master_car_amount'];
                $row['service_hour'] = $carData['service_hour'];
                $row['service_distance'] = $carData['service_distance'];
                $row['car_contact'] = $carData['car_contact'];
                $row['car_mobile'] = $carData['car_mobile'];
                $row['arrive_time'] = $carData['arrive_time'];
                $row['arrive_address'] = $carData['arrive_address'];
                $row['car_remark'] = $carData['car_remark'];
                $row['salesman'] = $this->user['id'];;
                $row['order_id'] = $OrderModel->id;
                $row['operate_id'] = $this->user['id'];
                $row['user_id'] = $this->user['id'];
                $carModel = new OrderCar();
                $carModel->allowField(true)->save($row);
                $source['car'][] = $carModel->toArray();
            }
        }

        ## 婚车跟车
        if (!empty($param['car'])) {
            $carData = json_decode($param['car'], true);
            if (!empty($carData['slave_car_id'])) {
                $row = [];
                $row['order_id'] = $carData['order_id'];
                $row['company_id'] = $carData['company_id'];
                $row['is_master'] = 0;
                $row['is_suborder'] = 0;
                $row['car_id'] = $carData['slave_car_id'];
                $row['car_price'] = $carData['slave_car_price'];
                $row['car_amount'] = $carData['slave_car_amount'];
                $row['service_hour'] = $carData['service_hour'];
                $row['service_distance'] = $carData['service_distance'];
                $row['car_contact'] = $carData['car_contact'];
                $row['car_mobile'] = $carData['car_mobile'];
                $row['arrive_time'] = $carData['arrive_time'];
                $row['arrive_address'] = $carData['arrive_address'];
                $row['car_remark'] = $carData['slave_car_remark'];
                $row['create_time'] = time();
                $row['salesman'] = $this->user['id'];
                $row['order_id'] = $OrderModel->id;
                $row['operate_id'] = $this->user['id'];
                $row['user_id'] = $this->user['id'];
                $carModel = new OrderCar();
                $carModel->allowField(true)->save($row);
                $source['car'][] = $carModel->toArray();
            }
        }

        ## 喜糖
        if (!empty($param['sugar'])) {
            $sugar = json_decode($param['sugar'], true);
            foreach ($sugar as $data) {
                if (empty($data['sugar_id'])) continue;
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];

                $sugarModel = new OrderSugar();
                $data['salesman'] = $data['sugar_salesman'];
                $sugarModel->allowField(true)->save($data);
                $source['sugar'][] = $sugarModel->toArray();
            }
        }

        ## 酒水
        if (!empty($param['wine'])) {
            $wine = json_decode($param['wine'], true);
            foreach ($wine as $data) {
                if (empty($data['wine_id'])) continue;
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];

                $wineModel = new OrderWine();
                $param['salesman'] = $param['wine_salesman'];
                $wineModel->allowField(true)->save($data);
                $source['wine'][] = $wineModel->toArray();
            }
        }

        ## 灯光
        if (!empty($param['light'])) {
            $light = json_decode($param['light'], true);
            foreach ($light as $data) {
                if (empty($data['light_id'])) continue;
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];

                $lightModel = new OrderLight();
                $data['salesman'] = $data['light_salesman'];
                $lightModel->allowField(true)->save($data);
                $source['light'][] = $lightModel->toArray();
            }
        }

        ## 点心
        if (!empty($param['dessert'])) {
            $dessert = json_decode($param['dessert'], true);
            foreach ($dessert as $data) {
                if (empty($data['dessert_id'])) continue;
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];

                $dessertModel = new OrderDessert();
                $data['salesman'] = $data['dessert_salesman'];
                $dessertModel->allowField(true)->save($data);
                $source['dessert'][] = $dessertModel->toArray();
            }
        }

        ## led
        if (!empty($param['led'])) {
            $led = json_decode($param['led'], true);
            foreach ($led as $data) {
                if (empty($data['led_id'])) continue;
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];

                $ledModel = new OrderLed();
                $data['salesman'] = $data['led_salesman'];
                $ledModel->allowField(true)->save($data);
                $source['led'][] = $ledModel->toArray();
            }
        }

        ## 3D
        if (!empty($param['d3'])) {
            $d3 = json_decode($param['d3'], true);
            foreach ($d3 as $data) {
                if (empty($data['d3_id'])) continue;
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];

                $d3Model = new OrderD3();
                $data['salesman'] = $data['d3_salesman'];
                $d3Model->allowField(true)->save($data);
                $source['d3'][] = $d3Model->toArray();
            }
        }

        ## 收款信息
        if (!empty($param['income'])) {
            $income = json_decode($param['income'], true);
            $income['income_type'] = 1;
            if ($orderData['cooperation_mode']!='1') {
                $incomeValidate = new \app\common\validate\OrderIncome();
                if (!$incomeValidate->check($income)) {
                    return json([
                        'code' => '400',
                        'msg' => $incomeValidate->getError()
                    ]);
                }
            }

            if ($orderData['news_type'] == '2' || $orderData['news_type'] == '0') {
                // 婚宴收款或一站式
                $data = [];
                $data['banquet_receivable_no'] = $income['receivable_no'];
                $data['banquet_income_date'] = $income['income_date'];
                $data['banquet_income_payment'] = $income['income_payment'];
                $data['banquet_income_type'] = 1;
                $data['banquet_income_item_price'] = $income['income_item_price'];
                $data['remark'] = $income['income_remark'];
                $data['order_id'] = $OrderModel->id;
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
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
        $companyId = $orderData['company_id'];
        $orderId = $OrderModel->id;
        create_order_confirm($orderId, $companyId, $this->user['id'], 'order', "创建订单定金审核", $source);
        return json(['code' => '200', 'msg' => '创建成功']);
    }

    public function edit()
    {
        $param = $this->request->param();
        $fields = 'id,company_id,news_type,contract_no,sign_date,event_date,hotel_id,hotel_text,cooperation_mode,score,banquet_hall_name,recommend_salesman,remark,bridegroom,bridegroom_mobile,bride,bride_mobile,salesman';
        $order = \app\common\model\Order::field($fields)->get($param['id']);
        if (!empty($order->salesman)) {
            $staff = User::getUser($order->salesman);
            $order['salesman_realname'] = $staff['realname'];
        } else {
            $order['salesman_realname'] = '-';
        }
        if (!empty($order->company_id)) {
            $order['company_title'] = $this->brands[$order->company_id]['title'];
        } else {
            $order['company_title'] = '-';
        }
        if (!empty($order->news_type)) {
            $order['news_type_title'] = $this->config['news_type_list'][$order->news_type];
        }

        if (!empty($order->cooperation_mode)) {
            $order['cooperation_mode_title'] = $this->config['cooperation_mode'][$order->cooperation_mode];
        } else {
            $order['cooperation_mode_title'] = '-';
        }
        $result = [
            'code' => '200',
            'msg' => '获取信息成功',
            'data' => [
                'order' => $order,
                'companyList' => array_values($this->brands),    ##签约公司列表
                'newsTypeList' => array_values($this->config['news_type_list']),    ## 订单类型
                'cooperationModeList' => array_values($this->config['cooperation_mode']),  ## 合同模式
            ]
        ];

        return json($result);
    }

    public function doEdit()
    {
        $param = $this->request->param();
        $arr = json_decode($param['order'], true);
        $param = json_decode($param['order'], true);
        if (!isset($param['id']) || empty($param['id'])) {
            $result = [
                'code' => '400',
                'msg' => '缺少必要参数'
            ];
            return json($result);
        }

        $order = $this->model->where('id', '=', $param['id'])->find();
        $param['image'] = empty($param['image_Array']) ? '' : implode(',', $param['image_Array']);
        $param['receipt_img'] = empty($param['receipt_imgArray']) ? '' : implode(',', $param['receipt_imgArray']);
        $param['note_imgArray'] = empty($param['note_imgArray']) ? '' : implode(',', $param['note_imgArray']);
        $rs = $order->allowField(true)->save($param);

        // id,user_id,create_time,module,controller,action,id,page,content
        if ($rs) {
            $source['order'] = $order->toArray();
            $intro = '编辑订单信息审核';
            create_order_confirm($order->id, $order->company_id, $this->user['id'], 'order', $intro, $source);
            $result = [
                'code' => '200',
                'msg' => '编辑成功'
            ];
        } else {
            $result = [
                'code' => '400',
                'msg' => '编辑失败'
            ];
        }
        return json($result);
    }

    public function show()
    {
        $param = $this->request->param();

        $order = \app\common\model\Order::field('update_time,delete_time', true)->where('id', '=', $param['id'])->find();
        if (empty($order)) {
            $result = [
                'code' => '200',
                'msg' => '订单不存在'
            ];
            return json($result);
        }
        $checkItems = [];
        $this->assign('checkItems', $checkItems);

        $orderId = $order->id;
        $order = $order->getData();
        $newsTypes = $this->config['news_type_list'];
        $cooperationMode = $this->config['cooperation_mode'];
        $order['contract_no'] = isset($order['contract_no']) ? $order['contract_no'] : '-';
        $order['company_id'] = $this->brands[$order['company_id']]['title'];
        $order['news_type'] = $newsTypes[$order['news_type']];
        $order['cooperation_mode'] = isset($cooperationMode[$order['cooperation_mode']]) ? $cooperationMode[$order['cooperation_mode']] : '-';
        $order['status'] = $this->confirmStatusList[$order['check_status']];
        $order['sign_date'] = $order['sign_date'] ? date('Y-m-d', $order['sign_date']) : '-';
        $order['event_date'] = $order['event_date'] ? date('Y-m-d', $order['event_date']) : '-';
        $order['earnest_money_date'] = $order['earnest_money_date'] ? date('Y-m-d', $order['earnest_money_date']) : '-';
        $order['middle_money_date'] = $order['middle_money_date'] ? date('Y-m-d', $order['middle_money_date']) : '-';
        $order['tail_money_date'] = $order['tail_money_date'] ? date('Y-m-d', $order['tail_money_date']) : '-';
        $order['bridegroom_mobile'] = isset($order['bridegroom_mobile']) ? substr_replace($order['bridegroom_mobile'], '***', 3, 3) : '-';
        $order['bride_mobile'] = isset($order['bride_mobile']) ? substr_replace($order['bride_mobile'], '***', 3, 3) : '-';
        $order['image'] = empty($order['image']) ? [] : explode(',', $order['image']);
        $order['receipt_img'] = empty($order['receipt_img']) ? [] : explode(',', $order['receipt_img']);
        $order['note_img'] = empty($order['note_img']) ? [] : explode(',', $order['note_img']);

        $staff = User::getUser($order['salesman']);
        $order['salesman'] = $staff['realname'];

        #### 获取用户信息
        $member = Member::field('realname,mobile,source_text')->where('id', '=', $order->member_id)->find();

        #### 获取婚宴订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $banquet = \app\common\model\OrderBanquet::where($where)->order('id desc')->find();
        if (empty($banquet)) {
            $banquet = [];
        } else {
            $banquet['company_id'] = $this->brands[$banquet->company_id]['title'];
            $banquet['banquet_ritual_title'] = $this->ritualList[$banquet->banquet_ritual_id]['title'];
        }

        #### 酒店服务项目
        $where = [];
        $where['order_id'] = $param['id'];
        $hotelItem = \app\common\model\OrderHotelItem::where($where)->order('id desc')->find();
        if (empty($hotelItem)) $hotelItem = [];

        #### 酒店协议
        $where = [];
        $where['order_id'] = $param['id'];
        $hotelProtocol = \app\common\model\OrderHotelProtocol::where($where)->order('id desc')->find();
        if (empty($hotelProtocol)) $hotelProtocol = [];

        #### 获取婚宴二销订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $banquetSuborderList = \app\common\model\OrderBanquetSuborder::where($where)->select();
        foreach ($banquetSuborderList as &$row) {
            $row['receipt_img'] = empty($row['receipt_img']) ? [] : explode(',', $row['receipt_img']);
            $row['note_img'] = empty($row['note_img']) ? [] : explode(',', $row['note_img']);
        }

        #### 获取婚宴收款信息
        $banquetReceivableList = \app\common\model\OrderBanquetReceivables::where('order_id', '=', $param['id'])->select();

        #### 获取婚宴付款信息
        $banquetPaymentList = \app\common\model\OrderBanquetPayment::where('order_id', '=', $param['id'])->select();

        #### 获取婚庆订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $wedding = \app\common\model\OrderWedding::where($where)->order('id desc')->find();
        if (empty($wedding)) {
            $wedding = [];
        } else {
            $wedding['company_id'] = $this->brands[$wedding->company_id]['title'];
            $wedding['wedding_package_title'] = $this->packageList[$wedding->wedding_package_id]['title'];
            $wedding['wedding_ritual_title'] = $this->ritualList[$wedding->wedding_ritual_id]['title'];
        }

        #### 获取婚宴二销订单信息
        $where = [];
        $where['order_id'] = $param['id'];
        $weddingSuborderList = \app\common\model\OrderWeddingSuborder::where($where)->select();
        foreach ($weddingSuborderList as &$row) {
            $row['receipt_img'] = empty($row['receipt_img']) ? [] : explode(',', $row['receipt_img']);
            $row['note_img'] = empty($row['note_img']) ? [] : explode(',', $row['note_img']);
        }

        #### 获取婚宴收款信息
        $weddingReceivableList = \app\common\model\OrderWeddingReceivables::where('order_id', '=', $param['id'])->select();
        #### 获取婚庆付款信息
        $weddingPaymentList = \app\common\model\OrderWeddingPayment::where('order_id', '=', $param['id'])->select();

        #### 婚车
        $where = [];
        $where['order_id'] = $param['id'];
        $carList = \app\common\model\OrderCar::where($where)->select();
        foreach ($carList as $key => &$row) {
            $row['car_id'] = $this->carList[$row['car_id']]['title'];
            $row['is_master'] = $row['is_master'] == '1' ? '主车' : '跟车';
        }

        #### 喜糖
        $where = [];
        $where['order_id'] = $param['id'];
        $sugarList = \app\common\model\OrderSugar::where($where)->select();
        foreach ($sugarList as $key => &$row) {
            $row['sugar_id'] = $this->sugarList[$row['sugar_id']]['title'];
        }

        #### 酒水
        $where = [];
        $where['order_id'] = $param['id'];
        $wineList = \app\common\model\OrderWine::where($where)->select();
        foreach ($wineList as $key => &$row) {
            $row['wine_id'] = $this->wineList[$row['wine_id']]['title'];
        }

        #### 灯光
        $where = [];
        $where['order_id'] = $param['id'];
        $lightList = \app\common\model\OrderLight::where($where)->select();
        foreach ($lightList as $key => &$row) {
            $row['light_id'] = $this->lightList[$row['light_id']]['title'];
        }

        #### 点心
        $where = [];
        $where['order_id'] = $param['id'];
        $dessertList = \app\common\model\OrderDessert::where($where)->select();
        foreach ($dessertList as $key => &$row) {
            $row['dessert_id'] = $this->dessertList[$row['dessert_id']]['title'];
        }

        #### LED
        $where = [];
        $where['order_id'] = $param['id'];
        $ledList = \app\common\model\OrderLed::where($where)->select();
        foreach ($ledList as $key => &$row) {
            $row['led_id'] = $this->ledList[$row['led_id']]['title'];
        }

        #### 3D
        $where = [];
        $where['order_id'] = $param['id'];
        $d3List = \app\common\model\OrderD3::where($where)->select();
        foreach ($d3List as $key => &$row) {
            $row['d3_id'] = $this->d3List[$row['d3_id']]['title'];
        }

        #### 合同金额
        $contractPrice = [
            'id' => $orderId,
            'totals' => $order['totals'],
            'earnest_money_date' => $order['earnest_money_date'],
            'earnest_money' => $order['earnest_money'],
            'middle_money_date' => $order['middle_money_date'],
            'middle_money' => $order['middle_money'],
            'tail_money_date' => $order['tail_money_date'],
            'tail_money' => $order['tail_money']
        ];

        #### 支付方式列表
        $paymentConfig = $this->config['payments'];
        $paymentConfig = array_column($paymentConfig, 'title', 'id');

        #### 支付性质
        $paymentTypes = $this->config['payment_type_list'];
        $paymentTypes = array_column($paymentTypes, 'title', 'id');

        #### 合成收款信息
        $incomeList = [];
        foreach ($banquetReceivableList as $key => $value) {
            $incomeList[] = [
                'id' => $value['id'],
                'receivable_no' => $value['banquet_receivable_no'],
                'income_category' => '婚宴',
                'income_payment' => $paymentConfig[$value['banquet_income_payment']],
                'income_type' => $paymentTypes[$value['banquet_income_type']],
                'income_date' => $value['banquet_income_date'],
                'income_real_date' => $value['banquet_income_real_date'],
                'income_item_price' => $value['banquet_income_item_price'],
                'income_remark' => $value['remark'],
                'receipt_img' => empty($value['receipt_img']) ? [] : explode(',', $value['receipt_img']),
                'note_img' => empty($value['note_img']) ? [] : explode(',', $value['note_img']),
            ];
        }
        foreach ($weddingReceivableList as $key => $value) {
            $incomeList[] = [
                'id' => $value['id'],
                'receivable_no' => $value['wedding_receivable_no'],
                'income_category' => '婚庆',
                'income_payment' => $paymentConfig[$value['wedding_income_payment']],
                'income_type' => $paymentTypes[$value['wedding_income_type']],
                'income_date' => $value['wedding_income_date'],
                'income_real_date' => $value['wedding_income_real_date'],
                'income_item_price' => $value['wedding_income_item_price'],
                'income_remark' => $value['remark'],
                'receipt_img' => empty($value['receipt_img']) ? [] : explode(',', $value['receipt_img']),
                'note_img' => empty($value['note_img']) ? [] : explode(',', $value['note_img']),
            ];
        }

        #### 合成付款信息
        $paymentList = [];
        foreach ($banquetPaymentList as $key => $value) {
            $paymentList[] = [
                'id' => $value['id'],
                'payment_no' => $value['banquet_payment_no'],
                'pay_category' => '婚宴',
                'pay_type' => $paymentTypes[$value['banquet_pay_type']],
                'apply_pay_date' => $value['banquet_apply_pay_date'],
                'pay_real_date' => $value['banquet_pay_real_date'],
                'pay_item_price' => $value['banquet_pay_item_price'],
                'payment_remark' => $value['banquet_payment_remark'],
                'pay_to_company' => $value['banquet_pay_to_company'],
                'pay_to_account' => $value['banquet_pay_to_account'],
                'pay_to_bank' => $value['banquet_pay_to_bank'],
                'receipt_img' => empty($value['receipt_img']) ? [] : explode(',', $value['receipt_img']),
                'note_img' => empty($value['note_img']) ? [] : explode(',', $value['note_img']),
            ];
        }
        foreach ($weddingPaymentList as $key => $value) {
            $paymentList[] = [
                'id' => $value['id'],
                'payment_no' => $value['wedding_payment_no'],
                'pay_category' => '婚庆',
                'pay_type' => $paymentTypes[$value['wedding_pay_type']],
                'apply_pay_date' => $value['wedding_apply_pay_date'],
                'pay_real_date' => $value['wedding_pay_real_date'],
                'pay_item_price' => $value['wedding_pay_item_price'],
                'payment_remark' => $value['wedding_payment_remark'],
                'pay_to_company' => $value['wedding_pay_to_company'],
                'pay_to_account' => $value['wedding_pay_to_account'],
                'pay_to_bank' => $value['wedding_pay_to_bank'],
                'receipt_img' => empty($value['receipt_img']) ? [] : explode(',', $value['receipt_img']),
                'note_img' => empty($value['note_img']) ? [] : explode(',', $value['note_img']),
            ];
        }

        $addItems = [
            [
                'id' => 'suborder',
                'title' => '二销',
                'children' => [
                    [
                        'id' => 'banquetSuborder',
                        'title' => '婚宴二销',
                        'read' => '/h5/order.banquet_suborder/create',
                        'api' => '/h5/order.banquet_suborder/doCreate'
                    ],
                    [
                        'id' => 'weddingSuborder',
                        'title' => '婚庆二销',
                        'read' => '/h5/order.wedding_suborder/create',
                        'api' => '/h5/order.wedding_suborder/doCreate'
                    ],
                ]
            ],
            [
                'id' => 'income',
                'title' => '收款',
                'read' => '/h5/order.income/create',
                'api' => '/h5/order.income/doCreate',
                'children' => []
            ],
            [
                'id' => 'payment',
                'title' => '付款',
                'read' => '/h5/order.payment/create',
                'api' => '/h5/order.payment/doCreate',
                'children' => []
            ],
            [
                'id' => 'hotelProtocol',
                'title' => '酒店协议',
                'read' => '/h5/order.hotel_protocol/create',
                'api' => '/h5/order.hotel_protocol/doCreate',
                'children' => []
            ],
            [
                'id' => 'hotelItem',
                'title' => '酒店服务项目',
                'read' => '/h5/order.hotel_item/create',
                'api' => '/h5/order.hotel_item/doCreate',
                'children' => []
            ],
            [
                'id' => 'car',
                'title' => '婚车',
                'read' => '/h5/order.car/create',
                'api' => '/h5/order.car/doCreate',
                'children' => []
            ],
            [
                'id' => 'wine',
                'title' => '酒水',
                'read' => '/h5/order.wine/create',
                'api' => '/h5/order.wine/doCreate',
                'children' => []
            ],
            [
                'id' => 'sugar',
                'title' => '喜糖',
                'read' => '/h5/order.sugar/create',
                'api' => '/h5/order.sugar/doCreate',
                'children' => []
            ],
            [
                'id' => 'dessert',
                'title' => '糕点',
                'read' => '/h5/order.dessert/create',
                'api' => '/h5/order.dessert/doCreate',
                'children' => []
            ],
            [
                'id' => 'light',
                'title' => '灯光',
                'read' => '/h5/order.light/create',
                'api' => '/h5/order.light/doCreate',
                'children' => []
            ],
            [
                'id' => 'led',
                'title' => 'Led',
                'read' => '/h5/order.led/create',
                'api' => '/h5/order.led/doCreate',
                'children' => []
            ],
            [
                'id' => '3d',
                'title' => '3D',
                'read' => '/h5/order.d3/create',
                'api' => '/h5/order.d3/doCreate',
                'children' => []
            ],
        ];

        if (empty($banquet) || 1) {
            $banquetItem = [
                [
                    'id' => 'banquet',
                    'title' => '婚宴',
                    'read' => '/h5/order.banquet/create',
                    'api' => '/h5/order.banquet/doCreate',
                    'children' => []
                ]
            ];
            $addItems = array_merge($banquetItem, $addItems);
        }

        if (empty($wedding) || 1) {
            $weddingItem = [
                [
                    'id' => 'wedding',
                    'title' => '婚庆',
                    'read' => '/h5/order.wedding/create',
                    'api' => '/h5/order.wedding/doCreate',
                    'children' => []
                ]
            ];
            $addItems = array_merge($weddingItem, $addItems);
        }

        #### 获取审核进度
        $result = [
            'code' => '200',
            'msg' => '获取成功',
            'data' => [
                'order' => $order,
                'member' => $member,
                'banquet' => $banquet,
                'banquetSuborderList' => $banquetSuborderList,
                'hotelItem' => $hotelItem,
                'hotelProtocol' => $hotelProtocol,
                'wedding' => $wedding,
                'weddingSuborderList' => $weddingSuborderList,
                'carList' => $carList,
                'wineList' => $wineList,
                'sugarList' => $sugarList,
                'dessertList' => $dessertList,
                'lightList' => $lightList,
                'ledList' => $ledList,
                'd3List' => $d3List,
                // 合同收款信息
                'contractPrice' => $contractPrice,
                // 订单收款信息
                'incomeList' => $incomeList,
                // 订单付款信息
                'paymentList' => $paymentList,
                'addItems' => $addItems
            ]
        ];

        return json($result);
    }

    public function doEditOrder()
    {
        $param = $this->request->param();

        $post = json_decode($param['order'], true);
        $post['cooperation_mode'] = $post['cooperation_mode'] + 1;
        if($post['cooperation_mode'] != '1') {
            $orderValidate = new \app\common\validate\Order();
        } else {
            $orderValidate = new OrderCommission();
        }
        if(!$orderValidate->check($post)) {
            return json([
                'code' => '400',
                'msg' => $orderValidate->getError()
            ]);
        }

        $post['image'] = empty($post['imageArray']) ? '' : implode(',', $post['imageArray']);
        $post['receipt_img'] = empty($post['receipt_imgArray']) ? '' : implode(',', $post['receipt_imgArray']);
        $post['note_img'] = empty($post['note_imgArray']) ? '' : implode(',', $post['note_imgArray']);
        $post['item_check_status'] = 0;
        unset($post['create_time']);
        unset($post['update_time']);
        unset($post['delete_time']);
        $order = \app\common\model\Order::get($post['id']);
        $result = $order->allowField(true)->save($post);
        $source['order'] = $order->toArray();

        ## banquet message
        if (!empty($param['banquet'])) {
            $data = json_decode($param['banquet'], true);
            $c1 = !empty($data['table_amount']) || !empty($data['table_price']);
            $c2 = !empty($data['wine_fee']) || !empty($data['service_fee']);
            $c3 = !empty($data['banquet_update_table']) || !empty($data['banquet_total']);
            $c4 = !empty($data['banquet_discount']) || !empty($data['banquet_totals']);
            $c5 = !empty($data['banquet_ritual_id']) || !empty($data['banquet_ritual_hall']);
            $c6 = !empty($data['banquet_other']) || !empty($data['banquet_remark']);
            if ($c1 || $c2 || $c3 || $c4 || $c5 || $c6) {
                $banquetValidate = new \app\common\validate\OrderBanquet();
                if(!$banquetValidate->check($data)) {
                    return json([
                        'code' => '400',
                        'msg' => $banquetValidate->getError()
                    ]);
                }
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['item_check_status'] = 0;
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);
                $banquet = OrderBanquet::get($data['id']);
                $banquet->allowField(true)->save($data);
                $source['banquet'] = $banquet->toArray();
            }
        }

        ## wedding message
        if (!empty($param['wedding'])) {
            $data = json_decode($param['wedding'], true);
            $c1 = !empty($data['wedding_package_id']) || !empty($data['wedding_package_price']);
            $c2 = !empty($data['wedding_ritual_id']) || !empty($data['wedding_ritual_hall']);
            $c3 = !empty($data['wedding_other']) || !empty($data['wedding_totals']);
            $c4 = !empty($data['wedding_remark']);
            if ($c1 || $c2 || $c3 || $c4) {
                $weddingValidate = new \app\common\validate\OrderWedding();
                if(!$weddingValidate->check($data)) {
                    return json([
                        'code' => '400',
                        'msg' => $weddingValidate->getError()
                    ]);
                }
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['item_check_status'] = 0;
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);
                $wedding = OrderWedding::get($data['id']);
                $wedding->allowField(true)->save($data);
                $source['wedding'] = $wedding->toArray();
            }
        }

        ## 酒店服务项目
        if (!empty($param['hotelItem'])) {
            $data = json_decode($param['hotelItem'], true);
            $c1 = !empty($data['wedding_room']) || !empty($data['wedding_room_amount']);
            $c2 = !empty($data['part']) || !empty($data['part_amount']);
            $c3 = !empty($data['champagne']) || !empty($data['champagne_amount']);
            $c4 = !empty($data['tea']) || !empty($data['tea_amount']);
            $c5 = !empty($data['cake']) || !empty($data['cake_amount']);
            if ($c1 || $c2 || $c3 || $c4 || $c5) {
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['item_check_status'] = 0;
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);
                $hotelItem = OrderHotelItem::get($data['id']);
                $hotelItem->allowField(true)->save($data);
                $source['hotelItem'] = $hotelItem->toArray();
            }
        }

        ## 酒店服务项目
        if (!empty($param['hotelProtocol'])) {
            $data = json_decode($param['hotelProtocol'], true);
            $c1 = !empty($data['table_price']) || !empty($data['table_amount']);
            $c2 = !empty($data['earnest_money']) || !empty($data['earnest_money_date']);
            $c3 = !empty($data['middle_money']) || !empty($data['earnest_money_date']);
            $c4 = !empty($data['tail_money']) || !empty($data['tail_money_date']);
            $c5 = !empty($data['cake']) || !empty($data['cake_amount']);
            $c6 = !empty($data['tea']) || !empty($data['tea_amount']);
            $c7 = !empty($data['champagne']) || !empty($data['champagne_amount']);
            $c8 = !empty($data['part']) || !empty($data['part_amount']);
            $c9 = !empty($data['wedding_room']) || !empty($data['wedding_room_amount']);
            $c10 = !empty($data['pay_hotel_totals']) || !empty($data['pay_hotel_totals']);
            if ($c1 || $c2 || $c3 || $c4 || $c5 || $c6 || $c7 || $c8 || $c9 || $c10) {
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);
                $orderHotelProtocol = new OrderHotelProtocol();
                $orderHotelProtocol->allowField(true)->save($data);
                $source['hotelProtocol'] = $orderHotelProtocol->toArray();
            }
        }

        ## 婚车
        if (!empty($param['car'])) {
            $car = json_decode($param['car'], true);
            foreach ($car as $data) {
                if (empty($data['car_id'])) continue;
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['salesman'] = $data['sugar_salesman'];
                $data['item_check_status'] = 0;
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);
                $car = Car::get($data['id']);
                $car->allowField(true)->save($data);
                $source['car'][] = $car->toArray();
            }
        }

        ## 喜糖
        if (!empty($param['sugar'])) {
            $sugar = json_decode($param['sugar'], true);
            foreach ($sugar as $data) {
                if (empty($data['sugar_id'])) continue;
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['salesman'] = $data['sugar_salesman'];
                $data['item_check_status'] = 0;
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);
                $sugar = OrderSugar::get($data['id']);
                $sugar->allowField(true)->save($data);
                $source['sugar'][] = $sugar->toArray();
            }
        }

        ## 酒水
        if (!empty($param['wine'])) {
            $wine = json_decode($param['wine'], true);
            foreach ($wine as $data) {
                if (empty($data['wine_id'])) continue;
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['salesman'] = $this->user['id'];
                $data['item_check_status'] = 0;
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);
                $wine = OrderWine::get($data['id']);
                $wine->allowField(true)->save($data);
                $source['wine'][] = $wine->toArray();
            }
        }

        ## 灯光
        if (!empty($param['light'])) {
            $light = json_decode($param['light'], true);
            foreach ($light as $data) {
                if (empty($data['light_id'])) continue;
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['salesman'] = $this->user['id'];
                $data['item_check_status'] = 0;
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);

                $light = OrderLight::get($data['id']);
                $light->allowField(true)->save($data);
                $source['light'][] = $light->toArray();
            }
        }

        ## 点心
        if (!empty($param['dessert'])) {
            $dessert = json_decode($param['dessert'], true);
            foreach ($dessert as $data) {
                if (empty($data['dessert_id'])) continue;
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['salesman'] = $this->user['id'];
                $data['item_check_status'] = 0;
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);

                $dessert = OrderDessert::get($data['id']);
                $dessert->allowField(true)->save($data);
                $source['dessert'][] = $dessert->toArray();
            }
        }

        ## led
        if (!empty($param['led'])) {
            $led = json_decode($param['led'], true);
            foreach ($led as $data) {
                if (empty($data['led_id'])) continue;
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['salesman'] = $this->user['id'];
                $data['item_check_status'] = 0;
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);

                $led = OrderLed::get($data['id']);
                $led->allowField(true)->save($data);
                $source['led'][] = $led->toArray();
            }
        }

        ## 3D
        if (!empty($param['d3'])) {
            $d3 = json_decode($param['d3'], true);
            foreach ($d3 as $data) {
                if (empty($data['d3_id'])) continue;
                $data['order_id'] = $post['id'];
                $data['operate_id'] = $this->user['id'];
                $data['user_id'] = $this->user['id'];
                $data['salesman'] = $this->user['id'];
                $data['item_check_status'] = 0;
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);

                $d3 = OrderD3::get($data['id']);
                $d3->allowField(true)->save($data);
                $source['d3'][] = $d3->toArray();
            }
        }

        ## 收款信息
        if (!empty($param['income'])) {
            $income = json_decode($param['income'], true);
            $income['income_type'] = 1;
            if($post['cooperation_mode'] != 1) {
                $incomeValidate = new \app\common\validate\OrderIncome();
                if (!$incomeValidate->check($income)) {
                    return json([
                        'code' => '400',
                        'msg' => $incomeValidate->getError()
                    ]);
                }
            }
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
                $data['item_check_status'] = 0;

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
                $data['item_check_status'] = 0;

                $income = OrderWeddingReceivables::get($income['id']);
                $income->allowField(true)->save($data);
                $source['weddingIncome'][] = $income->toArray();
            }
        }

        // 根据公司创建审核流程
        create_order_confirm($post['id'], $post['company_id'], $this->user['id'], 'order', "编辑订单定金审核", $source);
        return json(['code' => '200', 'msg' => '编辑订单成功']);
    }
}