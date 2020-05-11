<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2020/4/25
 * Time: 22:56
 */

namespace app\h5\controller\order;


use app\common\model\Brand;
use app\common\model\OrderConfirm;
use app\common\model\User;
use app\h5\controller\Base;

class Confirm extends Base
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
    protected $confirmStatusList = [0 => '待审核', 1 => '审核通过', 2 => '审核驳回', 13 => '审核撤销'];

    public function myConfirmed()
    {
        $param = $this->request->param();
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 5;
        $param['page'] = isset($param['page']) ? $param['page'] + 1 : 1;
        $config = [
            'page' => $param['page']
        ];
        $users = User::getUsers();
        $newsTypes = $this->config['news_type_list'];
        $companies = Brand::getBrands();

        $model = new OrderConfirm();
        // $model->
        $where = [];
        if ($param['type'] == '0') {
            $where[] = ['status', '=', 0];
        } else {
            if (is_numeric($param['status'])) {
                $where[] = ['status', '=', 0];
            } else {
                $where[] = ['status', '<>', 0];
            }
        }
        // $where[] = ['confirm_user_id', '=', $this->user['id']];
        $model = $model->where($where)->order('id desc');
        $list = $model->paginate($param['limit'], false, $config);

        if ($list->isEmpty()) {
            $result = [
                'code' => '200',
                'msg' => '暂无审核数据'
            ];
        } else {
            $data = [];
            foreach ($list as $key => $value) {
                $order = \app\common\model\Order::get($value->order_id);
                $data[] = [
                    'title' => $value['confirm_intro'],
                    'create_time' => $value['create_time'],
                    'status' => $this->confirmStatusList[$value['status']],
                    'company' => $companies[$value['company_id']]['title'],
                    'news_type' => $newsTypes[$order['news_type']],
                    'user' => $users[$value['user_id']]['realname']
                ];
            }

            $result = [
                'code' => '200',
                'msg' => '获取数据成功',
                'totals' => $list->total(),
                'data' => [
                    'confirmList' => $data
                ]
            ];
        }

        return json($result);
    }

    # 我审核的

    public function confirmMine()
    {
        $param = $this->request->param();
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 100;
        $param['page'] = isset($param['page']) ? $param['page'] + 1 : 1;
        $config = [
            'page' => $param['page']
        ];
        $users = User::getUsers();
        $newsTypes = $this->config['news_type_list'];
        $companies = Brand::getBrands();
        $model = new OrderConfirm();
        // $model->
        $where = [];
        // $where[] = ['confirm_user_id', '=', $this->user['id']];
        $model = $model->where($where)->order('id desc');
        $list = $model->paginate($param['limit'], false, $config);


        if ($list->isEmpty()) {
            $result = [
                'code' => '200',
                'msg' => '暂无审核数据'
            ];
        } else {
            $data = [];
            foreach ($list as $key => $value) {
                $order = \app\common\model\Order::get($value->order_id);
                $data[] = [
                    'title' => $value['confirm_intro'],
                    'create_time' => $value['create_time'],
                    'status' => $this->confirmStatusList[$value['status']],
                    'company' => $companies[$value['company_id']]['title'],
                    'news_type' => $newsTypes[$order['news_type']],
                    'user' => $users[$value['user_id']]['realname']
                ];
            }

            $result = [
                'code' => '200',
                'msg' => '获取数据成功',
                'totals' => $list->total(),
                'data' => [
                    'confirmList' => $data
                ]
            ];
        }

        return json($result);
    }

    # 审核我的

    public function orderConfirms()
    {
        $param = $this->request->param();

        $confirm = new OrderConfirm();
        $where = [];
        if (isset($param['user_id'])) {
            $where[] = ['user_id', '=', $param['user_id']];
        } else {
            $where[] = ['user_id', '=', $this->user['id']];
        }
        $where[] = ['order_id', '=', $param['order_id']];
        $confirmList = $confirm->where($where)->order('create_time desc')->select();

        $list = [];
        foreach ($confirmList as $key => $confirm) {
            $confirmNo = $confirm->confirm_no;
            if (!isset($list[$confirmNo])) {
                $list[$confirmNo]['id'] = $confirm->id;
                $list[$confirmNo]['confirm_no'] = $confirm->confirm_no;
                $list[$confirmNo]['confirm_intro'] = $confirm->confirm_intro;
                $list[$confirmNo]['status'] = $this->confirmStatusList[$confirm->status];
                $list[$confirmNo]['start_time'] = $confirm->create_time;

                // 判断跳转路径
                $source = json_decode($confirm->source, true);
                if (empty($source)) continue;
                foreach ($source as $key => $value) {
                    if ($key == 'order') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/order/order';
                        break;
                    } else if ($key == 'banquet') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/banquet/banquet';
                        break;
                    } else if ($key == 'banquetSuborder') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/banquetSuborder/banquetSuborder';
                        break;
                    } else if ($key == 'wedding') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/wedding/wedding';
                        break;
                    } else if ($key == 'weddingSuborder') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/weddingSuborder/weddingSuborder';
                        break;
                    } else if ($key == 'banquetPayment' || $key == 'weddingPayment') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/payment/payment';
                        break;
                    } else if ($key == 'banquetIncome' || $key == 'weddingIncome') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/income/income';
                        break;
                    } else if ($key == 'hotelItem') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/hotelItem/hotelItem';
                        break;
                    } else if ($key == 'hotelProtocol') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/hotelProtocol/hotelProtocol';
                        break;
                    } else if ($key == 'car') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/car/car';
                        break;
                    } else if ($key == 'wine') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/wine/wine';
                        break;
                    } else if ($key == 'sugar') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/sugar/sugar';
                        break;
                    } else if ($key == 'dessert') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/dessert/dessert';
                        break;
                    } else if ($key == 'light') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/light/light';
                        break;
                    } else if ($key == 'led') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/led/led';
                        break;
                    } else if ($key == 'd3') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/3d/3d';
                        break;
                    }
                }
            } else {
                if ($list[$confirmNo]['status'] == '待审核') {
                    $list[$confirmNo]['status'] = '审核中';
                }
                $list[$confirmNo]['start_time'] = $confirm->create_time;
            }
        }

        $result = [
            'code' => '200',
            'msg' => '获取审核列表成功',
            'data' => [
                'list' => array_values($list)
            ]
        ];
        return json($result);
    }

    ## 订单的审核列表

    public function detail()
    {
        $param = $this->request->param();

        $confirm = OrderConfirm::get($param['id']);
        $origin = json_decode($confirm->source, true);
        $source = [];

        $key = key($origin);
        if ($key == 'order') {
            $source = $origin;
            $editApi = '/h5/order.order/doEdit';
            $backendApi = '/h5/order.confirm/backend';
        } else if ($key == 'banquet') {
            $value = $origin['banquet'];
            $source['banquet'] = [];
            $source['banquet']['id'] = $value['order_id'];
            $source['banquet']['company_id'] = $value['company_id'];
            $source['banquet']['order_id'] = $value['order_id'];
            $source['banquet']['user_id'] = $value['user_id'];
            $source['banquet']['table_amount'] = $value['table_amount'];
            $source['banquet']['table_price'] = $value['table_price'];
            $source['banquet']['wine_fee'] = $value['wine_fee'];
            $source['banquet']['service_fee'] = $value['service_fee'];
            $source['banquet']['banquet_update_table'] = $value['banquet_update_table'];
            $source['banquet']['banquet_discount'] = $value['banquet_discount'];
            $source['banquet']['banquet_ritual_id'] = $value['banquet_ritual_id'];
            $source['banquet']['banquet_ritual_hall'] = $value['banquet_ritual_hall'];
            $source['banquet']['banquet_totals'] = $value['banquet_totals'];
            $source['banquet']['banquet_totals'] = $value['banquet_totals'];
            $source['banquet']['banquet_other'] = $value['banquet_other'];
            $source['banquet']['banquet_remark'] = $value['banquet_remark'];
            $editApi = '/h5/order.banquet/doedit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'banquetSuborder') {
            $value = $origin;
            $source = [];
            $suborder = $value['banquetSuborder'][0];
            $source['suborder']['id'] = $suborder['id'];
            $source['suborder']['company_id'] = $suborder['company_id'];
            $source['suborder']['order_id'] = $suborder['order_id'];
            $source['suborder']['salesman'] = $suborder['salesman'];
            $source['suborder']['table_price'] = $suborder['table_price'];
            $source['suborder']['table_amount'] = $suborder['table_amount'];
            $source['suborder']['wedding_order_no'] = $suborder['wedding_order_no'];
            $source['suborder']['banquet_totals'] = $suborder['banquet_totals'];
            $source['suborder']['sub_banquet_remark'] = $suborder['sub_banquet_remark'];

            $income = $value['banquetIncome'][0];
            $source['income']['id'] = $income['id'];
            $source['income']['user_id'] = $income['user_id'];
            $source['income']['order_id'] = $income['order_id'];
            $source['income']['receivable_no'] = $income['receivable_no'];
            $source['income']['banquet_income_payment'] = $income['banquet_income_payment'];
            $source['income']['banquet_income_type'] = $income['banquet_income_type'];
            $source['income']['banquet_income_date'] = $income['banquet_income_date'];
            $source['income']['banquet_income_item_price'] = $income['banquet_income_item_price'];
            $source['income']['image'] = !empty($income['contact_img']) ? explode(",", $income['contact_img']) : [];
            $source['income']['receipt_img'] = !empty($income['receipt_img']) ? explode(",", $income['receipt_img']) : [];
            $source['income']['note_img'] = !empty($income['note_img']) ? explode(",", $income['note_img']) : '';
            $source['income']['income_remark'] = $income['income_remark'];

            $editApi = '/h5/order.banquet_suborder/doEdit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'wedding') {
            $value = $origin['wedding'];
            $source['wedding'] = [];
            $source['wedding']['company_id'] = $value['company_id'];
            $source['wedding']['order_id'] = $value['order_id'];
            $source['wedding']['user_id'] = $value['user_id'];
            $source['wedding']['wedding_package_id'] = $value['wedding_package_id'];
            $source['wedding']['wedding_package_price'] = $value['wedding_package_price'];
            $source['wedding']['wedding_ritual_id'] = $value['wedding_ritual_id'];
            $source['wedding']['wedding_ritual_hall'] = $value['wedding_ritual_hall'];
            $source['wedding']['is_new_product'] = $value['is_new_product'];
            $source['wedding']['new_product_no'] = $value['new_product_no'];
            $source['wedding']['wedding_other'] = $value['wedding_other'];
            $source['wedding']['wedding_total'] = $value['wedding_total'];
            $source['wedding']['wedding_remark'] = $value['wedding_remark'];

            $editApi = '/h5/order.wedding/doedit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'weddingSuborder') {
            $value = $origin;
            $source = [];
            $suborder = $value['weddingSuborder'][0];
            $source['suborder']['id'] = $suborder['id'];
            $source['suborder']['company_id'] = $suborder['company_id'];
            $source['suborder']['order_id'] = $suborder['order_id'];
            $source['suborder']['salesman'] = $suborder['salesman'];
            $source['suborder']['wedding_order_no'] = $suborder['wedding_order_no'];
            $source['suborder']['wedding_totals'] = $suborder['wedding_totals'];
            $source['suborder']['sub_wedding_remark'] = $suborder['sub_wedding_remark'];

            $income = $value['weddingIncome'][0];
            $source['income']['id'] = $income['id'];
            $source['income']['user_id'] = $income['user_id'];
            $source['income']['order_id'] = $income['order_id'];
            $source['income']['receivable_no'] = $income['receivable_no'];
            $source['income']['wedding_income_payment'] = $income['wedding_income_payment'];
            $source['income']['wedding_income_type'] = $income['wedding_income_type'];
            $source['income']['wedding_income_date'] = $income['wedding_income_date'];
            $source['income']['wedding_income_item_price'] = $income['wedding_income_item_price'];
            $source['income']['income_remark'] = $income['income_remark'];
            $source['income']['image'] = explode(",", $income['contact_img']);
            $source['income']['receipt_img'] = explode(",", $income['receipt_img']);
            $source['income']['note_img'] = explode(",", $income['note_img']);

        } else if ($key == 'banquetPayment') {
            $value = $origin['banquetPayment'];
            $value = $value[0];
            $source['payment'] = [];
            $source['payment']['id'] = $value['id'];
            $source['payment']['order_id'] = $value['order_id'];
            $source['payment']['user_id'] = $value['id'];
            $source['payment']['payment_no'] = $value['banquet_payment_no'];
            $source['payment']['pay_type'] = $value['banquet_pay_type'];
            $source['payment']['apply_pay_date'] = $value['banquet_apply_pay_date'];
            $source['payment']['pay_item_price'] = $value['banquet_pay_item_price'];
            $source['payment']['payment_remark'] = $value['banquet_payment_remark'];
            $source['payment']['pay_to_company'] = $value['banquet_pay_to_company'];
            $source['payment']['pay_to_account'] = $value['banquet_pay_to_account'];
            $source['payment']['pay_to_bank'] = $value['banquet_pay_to_bank'];
            $source['payment']['receipt_img'] = $value['receipt_img'];
            $source['payment']['note_img'] = $value['note_img'];
            $editApi = '/h5/order.payment/doedit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'weddingPayment') {
            $value = $origin['weddingPayment'];
            $value = $value[0];
            $source['payment'] = [];
            $source['payment']['id'] = $value['id'];
            $source['payment']['order_id'] = $value['order_id'];
            $source['payment']['user_id'] = $value['id'];
            $source['payment']['payment_no'] = $value['wedding_payment_no'];
            $source['payment']['pay_type'] = $value['wedding_pay_type'];
            $source['payment']['apply_pay_date'] = $value['wedding_apply_pay_date'];
            $source['payment']['pay_item_price'] = $value['wedding_pay_item_price'];
            $source['payment']['payment_remark'] = $value['wedding_payment_remark'];
            $source['payment']['pay_to_company'] = $value['wedding_pay_to_company'];
            $source['payment']['pay_to_account'] = $value['wedding_pay_to_account'];
            $source['payment']['pay_to_bank'] = $value['wedding_pay_to_bank'];
            $source['payment']['receipt_img'] = $value['receipt_img'];
            $source['payment']['note_img'] = $value['note_img'];
            $editApi = '/h5/order.payment/doedit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'banquetIncome') {
            $value = $origin['banquetIncome'];
            $value = $value[0];
            $source['income'] = [];
            $source['income']["id"] = $value["id"];
            $source['income']["user_id"] = $value["user_id"];
            $source['income']["order_id"] = $value["order_id"];
            $source['income']["receivable_no"] = $value["banquet_receivable_no"];
            $source['income']["income_date"] = $value["banquet_income_date"];
            $source['income']["income_real_date"] = $value["banquet_income_real_date"];
            $source['income']["income_payment"] = $value["banquet_income_payment"];
            $source['income']["income_type"] = $value["banquet_income_type"];
            $source['income']["income_item_price"] = $value["banquet_income_item_price"];
            $source['income']["income_remark"] = $value["remark"];
            $source['income']["receipt_img"] = explode(',', $value["receipt_img"]);
            $source['income']["note_img"] = explode(',', $value["note_img"]);
            $source['income_category'] = "婚宴";
            $editApi = '/h5/order.banquet/doedit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'weddingIncome') {
            $value = $origin['weddingIncome'];
            $value = $value[0];
            $source['income'] = [];
            $source['income']["id"] = $value["id"];
            $source['income']["user_id"] = $value["user_id"];
            $source['income']["order_id"] = $value["order_id"];
            $source['income']["receivable_no"] = $value["wedding_receivable_no"];
            $source['income']["income_date"] = $value["wedding_income_date"];
            $source['income']["income_real_date"] = $value["wedding_income_real_date"];
            $source['income']["income_payment"] = $value["wedding_income_payment"];
            $source['income']["income_type"] = $value["wedding_income_type"];
            $source['income']["income_item_price"] = $value["wedding_income_item_price"];
            $source['income']["income_remark"] = $value["remark"];
            $source['income']["receipt_img"] = explode(',', $value["receipt_img"]);
            $source['income']["note_img"] = explode(',', $value["note_img"]);
            $source['income_category'] = "婚庆";
            $editApi = '/h5/order.wedding/doedit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'hotelItem') {
            $value = $origin['hotelItem'];
            $source['hotelItem'] = [];
            $source['hotelItem']["id"] = $value["id"];
            $source['hotelItem']["user_id"] = $value["user_id"];
            $source['hotelItem']["order_id"] = $value["order_id"];
            $source['hotelItem']["wedding_room_amount"] = $value["wedding_room_amount"];
            $source['hotelItem']["wedding_room"] = $value["wedding_room"];
            $source['hotelItem']["part_amount"] = $value["part_amount"];
            $source['hotelItem']["part"] = $value["part"];
            $source['hotelItem']["champagne_amount"] = $value["champagne_amount"];
            $source['hotelItem']["champagne"] = $value["champagne"];
            $source['hotelItem']["tea_amount"] = $value["tea_amount"];
            $source['hotelItem']["tea"] = $value["tea"];
            $source['hotelItem']["cake_amount"] = $value["cake_amount"];
            $source['hotelItem']["cake"] = $value["cake"];
            $editApi = '/h5/order.wedding/doEdit';
            $backendApi = '/h5/order.confirm/backend';
        } else if ($key == 'hotelProtocol') {
            $value = $origin['hotelProtocol'];
            $source['hotelProtocol'] = [];
            $source['hotelProtocol']["id"] = $value["id"];
            $source['hotelProtocol']["user_id"] = $value["user_id"];
            $source['hotelProtocol']["order_id"] = $value["order_id"];
            $source['hotelProtocol']["table_price"] = $value["table_price"];
            $source['hotelProtocol']["table_amount"] = $value["table_amount"];
            $source['hotelProtocol']["wedding_room_amount"] = $value["wedding_room_amount"];
            $source['hotelProtocol']["wedding_room"] = $value["wedding_room"];
            $source['hotelProtocol']["part_amount"] = $value["part_amount"];
            $source['hotelProtocol']["part"] = $value["part"];
            $source['hotelProtocol']["champagne_amount"] = $value["champagne_amount"];
            $source['hotelProtocol']["champagne"] = $value["champagne"];
            $source['hotelProtocol']["tea_amount"] = $value["tea_amount"];
            $source['hotelProtocol']["tea"] = $value["tea"];
            $source['hotelProtocol']["cake_amount"] = $value["cake_amount"];
            $source['hotelProtocol']["cake"] = $value["cake"];
            $source['hotelProtocol']["tail_money_date"] = $value["tail_money_date"];
            $source['hotelProtocol']["tail_money"] = $value["tail_money"];
            $source['hotelProtocol']["middle_money_date"] = $value["middle_money_date"];
            $source['hotelProtocol']["middle_money"] = $value["middle_money"];
            $source['hotelProtocol']["earnest_money_date"] = $value["earnest_money_date"];
            $source['hotelProtocol']["earnest_money"] = $value["earnest_money"];
            $editApi = '/h5/order.hotel_protocol/doEdit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'car') {
            $value = $origin['car'];
            $source = [];
            ## 婚车主车
            foreach ($value as $v) {
                $source['car']['company_id'] = $v['company_id'];
                $source['car']['is_suborder'] = $v['is_suborder'];
                $source['car']['service_hour'] = $v['service_hour'];
                $source['car']['service_distance'] = $v['service_distance'];
                $source['car']['arrive_time'] = $v['arrive_time'];
                $source['car']['arrive_address'] = $v['arrive_address'];
                $source['car']['car_remark'] = $v['master_car_remark'];
                $source['car']['salesman'] = $v['car_salesman'];
                $source['car']['order_id'] = $v['order_id'];
                $source['car']['user_id'] = $v['user_id'];
                if($v['is_master']) {
                    $source['car']['master_order_id'] = $v['id'];
                    $source['car']['master_car_id'] = $v['car_id'];
                    $source['car']['master_car_price'] = $v['car_price'];
                    $source['car']['master_car_amount'] = $v['car_amount'];
                } else {
                    $source['car']['slave_order_id'] = $v['id'];
                    $source['car']['slave_car_id'] = $v['car_id'];
                    $source['car']['slave_car_price'] = $v['car_price'];
                    $source['car']['slave_car_amount'] = $v['car_amount'];
                }
            }

            $editApi = '/h5/order.car/doEdit';
            $backendApi = '/h5/order.confirm/backend';
        } else if ($key == 'wine') {
            $value = $origin['wine'];
            $source = [];
            foreach ($value as $v) {
                $source['wine'][] = [
                    'id' => $v['id'],
                    'order_id' => $v['order_id'],
                    'wine_id' => $v['wine_id'],
                    'wine_amount' => $v['wine_amount'],
                    'wine_price' => $v['wine_price'],
                    'wine_remark' => $v['wine_remark'],
                ];
            }
            $editApi = '/h5/order.wine/doEdit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'sugar') {
            $value = $origin['sugar'];
            $source = [];
            foreach ($value as $v) {
                $source['sugar'][] = [
                    'id' => $v['id'],
                    'order_id' => $v['order_id'],
                    'sugar_id' => $v['sugar_id'],
                    'sugar_amount' => $v['sugar_amount'],
                    'sugar_price' => $v['sugar_price'],
                    'sugar_remark' => $v['sugar_remark'],
                ];
            }
            $editApi = '/h5/order.sugar/doEdit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'dessert') {
            $value = $origin['dessert'];
            $source = [];
            foreach ($value as $v) {
                $source['dessert'][] = [
                    'id' => $v['id'],
                    'order_id' => $v['order_id'],
                    'dessert_id' => $v['dessert_id'],
                    'dessert_amount' => $v['dessert_amount'],
                    'dessert_price' => $v['dessert_price'],
                    'dessert_remark' => $v['dessert_remark'],
                ];
            }
            $editApi = '/h5/order.dessert/doEdit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'light') {
            $value = $origin['light'];
            $source = [];
            foreach ($value as $v) {
                $source['light'][] = [
                    'id' => $v['id'],
                    'order_id' => $v['order_id'],
                    'light_id' => $v['light_id'],
                    'light_amount' => $v['light_amount'],
                    'light_price' => $v['light_price'],
                    'light_remark' => $v['light_remark'],
                ];
            }
            $editApi = '/h5/order.light/doEdit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'led') {
            $value = $origin['led'];
            $source = [];
            foreach ($value as $v) {
                $source['led'][] = [
                    'id' => $v['id'],
                    'order_id' => $v['order_id'],
                    'led_id' => $v['led_id'],
                    'led_amount' => $v['led_amount'],
                    'led_price' => $v['led_price'],
                    'led_remark' => $v['led_remark'],
                ];
            }
            $editApi = '/h5/order.led/doEdit';
            $backendApi = '/h5/order.confirm/backend';

        } else if ($key == 'd3') {
            $value = $origin['d3'];
            $source = [];
            foreach ($value as $v) {
                $source['d3'][] = [
                    'id' => $v['id'],
                    'order_id' => $v['order_id'],
                    'd3_id' => $v['d3_id'],
                    'd3_amount' => $v['d3_amount'],
                    'd3_price' => $v['d3_price'],
                    'd3_remark' => $v['d3_remark'],
                ];
            }
            $editApi = '/h5/order.d3/doEdit';
            $backendApi = '/h5/order.confirm/backend';
        }


        if ($confirm->status == '0') {
            $buttons = [
                [
                    'id' => 'backend',
                    'label' => '撤销',
                    'api' => $backendApi
                ]
            ];
        } else if ($confirm->status == '1') {
            $buttons = [
                [
                    'id' => 'update',
                    'label' => '更新',
                    'api' => $editApi
                ]
            ];
        } else if ($confirm->status == '2') {
            $buttons = [
                [
                    'id' => 'backout',
                    'label' => '撤销',
                    'api' => $backendApi
                ],
                [
                    'id' => 'update',
                    'label' => '更新',
                    'api' => $editApi
                ]
            ];
        } else {
            $buttons = [];
        }

        $confirmData = [
            'confirm_no' => $confirm->confirm_no,
            'confirm_intro' => $confirm->confirm_intro,
            'status' => $this->confirmStatusList[$confirm->status]
        ];

        $where = [];
        $where[] = ['company_id', '=', $confirm->company_id];
        $where[] = ['timing', '=', $confirm->confirm_type];
        $audit = \app\common\model\Audit::where($where)->find();
        if (empty($audit)) {
            $result = [
                'code' => '400',
                'msg' => '尚未设置审核顺序'
            ];
            return json($result);
        }

        if (empty($audit->content)) {
            $result = [
                'code' => '400',
                'msg' => '尚未设置审核顺序'
            ];
            return json($result);
        }

        ### 获取所有审核的列表
        $where = [];
        $where['confirm_no'] = $confirm->confirm_no;
        $where['order_id'] = $param['id'];
        $orderConfirm = new OrderConfirm();
        $confirmRs = $orderConfirm->where($where)->column('id,status,content', 'confirm_item_id');

        $avatar = 'https://www.yusivip.com/upload/commonAppimg/hs_app_logo.png';
        $staffs = User::getUsers(false);
        ## 审核全局列表
        $sequence = $this->config['check_sequence'];
        $auth = json_decode($audit->content, true);
        $confirmList = [];
        foreach ($auth as $key => $row) {
            $managerList = [];
            $type = $sequence[$key]['type'];
            if ($type == 'role') {
                // 获取角色
                foreach ($row as $v) {
                    $user = User::getRoleManager($v, $this->user);
                    $managerList[] = [
                        'id' => $user['id'],
                        'realname' => $user['realname'],
                        'avatar' => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            } else {
                foreach ($row as $v) {
                    if (!isset($staffs[$v])) continue;
                    $user = $staffs[$v];
                    $managerList[] = [
                        'id' => $user['id'],
                        'realname' => $user['realname'],
                        'avatar' => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            }

            if ($confirmRs[$key]) {
                switch ($confirmRs[$key]['status']) {
                    case 0:
                        $status = '待审核';
                        break;
                    case 1:
                        $status = '审核通过';
                        break;
                    case 2:
                        $status = '审核驳回';
                        break;
                    default:
                        $status = '待审核';
                }
                $content = $confirmRs[$key]['content'];
            } else {
                $status = '待审核';
                $content = '';
            }

            $confirmList[] = [
                'id' => $key,
                'title' => $sequence[$key]['title'],
                'status' => $status,
                'content' => $content,
                'managerList' => $managerList
            ];
        }

        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'confirm' => $confirmData,
                'detail' => $source,
                'buttons' => $buttons,
                'incomeTypeList' => $this->config['payment_type_list'],
                'incomePaymentList' => $this->config['payments'],
                'confirmList' => $confirmList,
                'companyList' => array_values($this->brands),
                'carList' => array_values($this->carList),
                'wineList' => array_values($this->wineList),
                'sugarList' => array_values($this->sugarList),
                'dessertList' => array_values($this->dessertList),
                'lightList' => array_values($this->lightList),
                'ledList' => array_values($this->ledList),
                'd3List' => array_values($this->d3List),
                'ritualList'  => array_values($this->ritualList),
                'packageList'  => array_values($this->packageList)
            ]
        ];

        return json($result);
    }

    public function backend()
    {
        $params = $this->request->param();

        $where['id'] = $params['id'];
        $rs = OrderConfirm::where($where)->update(['status' => 13]);

        if ($rs) {
            $result = [
                'code' => '200',
                'msg' => '撤销成功'
            ];
        } else {
            $result = [
                'code' => '400',
                'msg' => '撤销失败'
            ];
        }

        return json($result);
    }

    public function getConfirmSequence()
    {
        $param = $this->request->param();
        $where = [];
        $where[] = ['company_id', '=', $param['company_id']];
        $where[] = ['timing', '=', 'order'];
        $audit = \app\common\model\Audit::where($where)->find();

        if (empty($audit)) {
            $result = [
                'code' => '400',
                'msg' => '尚未设置审核顺序'
            ];
            return json($result);
        }

        if (empty($audit->content)) {
            $result = [
                'code' => '400',
                'msg' => '尚未设置审核顺序'
            ];
            return json($result);
        }

        $avatar = 'https://www.yusivip.com/upload/commonAppimg/hs_app_logo.png';
        $staffs = User::getUsers(false);
        ## 审核全局列表
        $sequence = $this->config['check_sequence'];
        $auth = json_decode($audit->content, true);
        $confirmList = [];
        foreach ($auth as $key => $row) {
            $managerList = [];
            $type = $sequence[$key]['type'];
            if ($type == 'role') {
                // 获取角色
                foreach ($row as $v) {
                    $user = User::getRoleManager($v, $this->user);
                    $managerList[] = [
                        'id' => $user['id'],
                        'realname' => $user['realname'],
                        'avatar' => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            } else {
                foreach ($row as $v) {
                    if (!isset($staffs[$v])) continue;
                    $user = $staffs[$v];
                    $managerList[] = [
                        'id' => $user['id'],
                        'realname' => $user['realname'],
                        'avatar' => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            }
            $confirmList[] = [
                'id' => $key,
                'title' => $sequence[$key]['title'],
                'managerList' => $managerList
            ];
        }

        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'confirmList' => $confirmList
            ]
        ];

        return json($result);
    }

    # comnpany_id,创建时的审核进程

    public function getConfirmStep()
    {
        $param = $this->request->param();

        $where = [];
        $where[] = ['order_id', '=', $param['id']];
        // $where[] = ['company_id', '=', $order->company_id];
        $where[] = ['user_id', '=', $this->user['id']];
        // $where[] = ['is_checked', '=', '0'];
        $orderConfirm = OrderConfirm::where($where)->order('id desc')->find();
        if (empty($orderConfirm)) {
            $result = [
                'code' => '200',
                'msg' => '此单已锁',
                'data' => [
                    'confirmList' => []
                ]
            ];

            return json($result);
        }

        $where = [];
        $where[] = ['company_id', '=', $orderConfirm->company_id];
        $where[] = ['timing', '=', $orderConfirm->confirm_type];
        $audit = \app\common\model\Audit::where($where)->find();
        if (empty($audit)) {
            $result = [
                'code' => '400',
                'msg' => '尚未设置审核顺序'
            ];
            return json($result);
        }

        if (empty($audit->content)) {
            $result = [
                'code' => '400',
                'msg' => '尚未设置审核顺序'
            ];
            return json($result);
        }

        ### 获取所有审核的列表
        $where = [];
        $where['confirm_no'] = $orderConfirm->confirm_no;
        $where['order_id'] = $param['id'];
        $orderConfirm = new OrderConfirm();
        $confirmRs = $orderConfirm->where($where)->column('id,status,content', 'confirm_item_id');

        $avatar = 'https://www.yusivip.com/upload/commonAppimg/hs_app_logo.png';
        $staffs = User::getUsers(false);
        ## 审核全局列表
        $sequence = $this->config['check_sequence'];
        $auth = json_decode($audit->content, true);
        $confirmList = [];
        foreach ($auth as $key => $row) {
            $managerList = [];
            $type = $sequence[$key]['type'];
            if ($type == 'role') {
                // 获取角色
                foreach ($row as $v) {
                    $user = User::getRoleManager($v, $this->user);
                    $managerList[] = [
                        'id' => $user['id'],
                        'realname' => $user['realname'],
                        'avatar' => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            } else {
                foreach ($row as $v) {
                    if (!isset($staffs[$v])) continue;
                    $user = $staffs[$v];
                    $managerList[] = [
                        'id' => $user['id'],
                        'realname' => $user['realname'],
                        'avatar' => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            }

            if ($confirmRs[$key]) {
                switch ($confirmRs[$key]['status']) {
                    case 0:
                        $status = '待审核';
                        break;
                    case 1:
                        $status = '审核通过';
                        break;
                    case 2:
                        $status = '审核驳回';
                        break;
                    default:
                        $status = '待审核';
                }
                $content = $confirmRs[$key]['content'];
            } else {
                $status = '待审核';
                $content = '';
            }

            $confirmList[] = [
                'id' => $key,
                'title' => $sequence[$key]['title'],
                'status' => $status,
                'content' => $content,
                'managerList' => $managerList
            ];
        }

        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'confirmList' => $confirmList,
            ]
        ];

        return json($result);
    }

    # 订单ID 参数id

    protected function initialize()
    {
        parent::initialize();

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
    }
}