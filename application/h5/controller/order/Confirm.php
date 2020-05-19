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
use app\common\model\OrderConfirmComment;
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

    /**
     * 我审核的
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
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

        if($param['keyword']!='') {
            $keyword = $param['keyword'];
            $model = $model->where('order_id', 'in', function ($query) use ($keyword) {
                $query->table('tk_order')->where('bridegroom|bride|bridegroom_mobile|bride_mobile', 'like', "%{$keyword}%")->field('id');
            });
        }

        // 所属||签约公司
        if (is_numeric($param['company_id'])) {
            $where[] = ['company_id', '=', $param['company_id']];
        }

        // 审核状态
        if (is_numeric($param['check_status'])) {
            $where[] = ['status', '=', $param['check_status']];
        }

        ### 员工列表
        /**
        if ($this->role['auth_type'] > 0) {
            if (isset($param['user_id']) && !empty($param['user_id'])) {
                // $user_id = explode(',',$param['user_id']);
                if ($param['user_id'] == 'all') {
                    $map[] = ['confirm_user_id', 'in', $this->staffs];
                } else if (is_numeric($param['user_id'])) {
                    $map[] = ['confirm_user_id', '=', $this->user['id']];
                } else {
                    $map[] = ['confirm_user_id', 'in', $param['user_id']];
                }

            } else {
                $where[] = ['confirm_user_id', '=', $this->user['id']];
            }

        } else {
            $where[] = ['confirm_user_id', '=', $this->user['id']];
        }
        **/
        $where[] = ['confirm_user_id', '=', $this->user['id']];

        ### range
        if (isset($param['range']) && !empty($param['range'])) {
            $range = format_date_range($param['range']);
            $where[] = ['create_time', 'between', $range];
        }

        $model = $model->where($where)->order('id desc');
        $list = $model->paginate($param['limit'], false, $config);

        if ($list->isEmpty()) {
            $result = [
                'code' => '200',
                'msg' => '暂无审核数据'
            ];
        } else {
            $data = [];
            foreach ($list as $key => $confirm) {
                // 判断跳转路径
                $source = json_decode($confirm->source, true);
                if (empty($source)) continue;
                $order = \app\common\model\Order::get($confirm->order_id);
                $key = key($source);
                if ($key == 'order') {
                    if ($order->complete == '99') {
                        // 意向金
                        $path = '/pages/addOrderItems/earnestMoney/earnestMoney';
                    } else {
                        $path = '/pages/addOrderItems/order/order';
                    }
                } else if ($key == 'banquet') {
                    $path = '/pages/addOrderItems/banquet/banquet';
                } else if ($key == 'banquetSuborder') {
                    $path = '/pages/addOrderItems/banquetSuborder/banquetSuborder';
                } else if ($key == 'wedding') {
                    $path = '/pages/addOrderItems/wedding/wedding';
                } else if ($key == 'weddingSuborder') {
                    $path = '/pages/addOrderItems/weddingSuborder/weddingSuborder';
                } else if ($key == 'banquetPayment' || $key == 'weddingPayment') {
                    $path = '/pages/addOrderItems/payment/payment';
                } else if ($key == 'banquetIncome' || $key == 'weddingIncome') {
                    $path = '/pages/addOrderItems/income/income';
                } else if ($key == 'hotelItem') {
                    $path = '/pages/addOrderItems/hotelItem/hotelItem';
                } else if ($key == 'hotelProtocol') {
                    $path = '/pages/addOrderItems/hotelProtocol/hotelProtocol';
                } else if ($key == 'car') {
                    $path = '/pages/addOrderItems/car/car';
                } else if ($key == 'wine') {
                    $path = '/pages/addOrderItems/wine/wine';
                } else if ($key == 'sugar') {
                    $path = '/pages/addOrderItems/sugar/sugar';
                } else if ($key == 'dessert') {
                    $path = '/pages/addOrderItems/dessert/dessert';
                } else if ($key == 'light') {
                    $path = '/pages/addOrderItems/light/light';
                } else if ($key == 'led') {
                    $path = '/pages/addOrderItems/led/led';
                } else if ($key == 'd3') {
                    $path = '/pages/addOrderItems/3d/3d';
                } else {
                    $path = '';
                }

                $data[] = [
                    'id' => $confirm->id,
                    'title' => $confirm->confirm_intro,
                    'create_time' => $confirm->create_time,
                    'status' => $this->confirmStatusList[$confirm->status],
                    'company' => $companies[$confirm->company_id]['title'],
                    'news_type' => $newsTypes[$order->news_type],
                    'path'  => $path,
                    'user' => $users[$confirm->user_id]['realname'],
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
    public function confirmMine()
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

        $confirm = new OrderConfirm();
        $where = [];
        if (isset($param['user_id'])) {
            $where[] = ['user_id', '=', $param['user_id']];
        } else {
            $where[] = ['user_id', '=', $this->user['id']];
        }
        // $where[] = ['order_id', '=', $param['order_id']];
        $confirmList = $confirm->where($where)->order('create_time desc')->paginate($param['limit']);
        $order = \app\common\model\Order::get($param['order_id']);

        $list = [];
        foreach ($confirmList as $key => $confirm) {
            $confirmNo = $confirm->confirm_no;
            if (!isset($list[$confirmNo])) {
                $order = \app\common\model\Order::get($confirm->order_id);
                $list[$confirmNo]['id'] = $confirm->id;
                $list[$confirmNo]['confirm_no'] = $confirm->confirm_no;
                $list[$confirmNo]['confirm_intro'] = $confirm->confirm_intro;
                $list[$confirmNo]['status'] = $this->confirmStatusList[$confirm->status];
                $list[$confirmNo]['start_time'] = $confirm->create_time;
                $list[$confirmNo]['company'] = $companies[$confirm->company_id]['title'];
                $list[$confirmNo]['news_type'] = $newsTypes[$order->news_type];
                $list[$confirmNo]['user'] = $users[$confirm->user_id]['realname'];

                // 判断跳转路径
                $source = json_decode($confirm->source, true);
                if (empty($source)) continue;
                foreach ($source as $key => $value) {
                    if ($key == 'order') {
                        if ($order->complete == '99') {
                            // 意向金
                            $list[$confirmNo]['path'] = '/pages/addOrderItems/earnestMoney/earnestMoney';
                        } else {
                            $list[$confirmNo]['path'] = '/pages/addOrderItems/order/order';
                        }
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

    # 订单的审核列表
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
        $order = \app\common\model\Order::get($param['order_id']);

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
                        if ($order->complete == '99') {
                            // 意向金
                            $list[$confirmNo]['path'] = '/pages/addOrderItems/earnestMoney/earnestMoney';
                        } else {
                            $list[$confirmNo]['path'] = '/pages/addOrderItems/order/order';
                        }
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

    ## 订单的审核列表,type=confirm_user
    public function detail()
    {
        $param = $this->request->param();
        $confirm = OrderConfirm::get($param['id']);
        $origin = json_decode($confirm->source, true);
        $source = [];
        $orderObj = \app\common\model\Order::get($confirm->order_id);

        $key = key($origin);
        if ($key == 'order') {
            $source = $origin;
            if (isset($source['order'])) {
                $orderData = $source['order'];
                $source['order']['image'] = images_to_array($orderData['image']);
                $source['order']['receipt_img'] = images_to_array($orderData['receipt_img']);
                $source['order']['note_img'] = images_to_array($orderData['note_img']);
            }

            if (isset($source['banquetIncome'])){
                $income = $source['banquetIncome'][0];
                unset($source['banquetIncome']);
                $source['income'] = [
                    "id" =>  $income['id'],
                    "receivable_no" => $income['banquet_receivable_no'],
                    "income_date" => $income['banquet_income_date'],
                    "income_payment" => $income['banquet_income_payment'],
                    "income_type" => $income['banquet_income_type'],
                    "income_item_price" => $income['banquet_income_item_price'],
                    "income_remark" => $income['remark'],
                    "order_id" => $income['order_id'],
                    "receipt_imgArray" => images_to_array($income['receipt_img']),
                    "note_imgArray" => images_to_array($income['note_img']),
                ];
            }

            if (isset($source['weddingIncome'])){
                $income = $source['weddingIncome'][0];
                unset($source['weddingIncome']);
                $source['income'] = [
                    "id" =>  $income['id'],
                    "receivable_no" => $income['wedding_receivable_no'],
                    "income_date" => $income['wedding_income_date'],
                    "income_payment" => $income['wedding_income_payment'],
                    "income_type" => $income['wedding_income_type'],
                    "income_item_price" => $income['wedding_income_item_price'],
                    "income_remark" => $income['remark'],
                    "order_id" => $income['order_id'],
                    "receipt_imgArray" => images_to_array($income['receipt_img']),
                    "note_imgArray" => images_to_array($income['note_img']),
                ];
            }

            if (isset($source['car'])) {
                $cars = $source['car'];
                unset($source['car']);
                foreach ($cars as $v) {
                    $source['car']['company_id'] = $v['company_id'];
                    $source['car']['is_suborder'] = $v['is_suborder'];
                    $source['car']['service_hour'] = $v['service_hour'];
                    $source['car']['service_distance'] = $v['service_distance'];
                    $source['car']['arrive_time'] = $v['arrive_time'];
                    $source['car']['arrive_address'] = $v['arrive_address'];
                    $source['car']['car_remark'] = $v['car_remark'];
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
            }


            if ($orderObj->complete == '99') {
                // 意向金订单
                $editApi = '/h5/order.prepay/doEdit';
            } else {
                $editApi = '/h5/order.order/doEditOrder';
            }
            $backendApi = '/h5/order.confirm/backend';


        } else if ($key == 'banquet') {
            $value = $origin['banquet'];
            $source['banquet'] = [];
            $source['banquet']['id'] = $value['id'];
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
            $source['income']['contact_img'] = images_to_array($income['contact_img']);
            $source['income']['receipt_img'] = images_to_array($income['receipt_img']);
            $source['income']['note_img'] = images_to_array($income['note_img']);
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

            $editApi = '/h5/order.wedding/doEdit';
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
            $source['income']['contact_img'] = images_to_array($income['contact_img']);
            $source['income']['receipt_img'] = images_to_array($income['receipt_img']);
            $source['income']['note_img'] = images_to_array($income['note_img']);
            $editApi = '/h5/order.wedding_suborder/doEdit';
            $backendApi = '/h5/order.confirm/backend';

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
            $source['payment']['receipt_img'] = images_to_array($value['receipt_img']);
            $source['payment']['note_img'] = images_to_array($value['note_img']);
            $editApi = '/h5/order.payment/doEdit';
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
            $source['payment']['receipt_img'] = images_to_array($value['receipt_img']);
            $source['payment']['note_img'] = images_to_array($value['note_img']);
            $editApi = '/h5/order.payment/doEdit';
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
            $source['income']["receipt_img"] = images_to_array($value['receipt_img']);
            $source['income']["note_img"] = images_to_array($value['note_img']);
            $source['income_category'] = "婚宴";
            $editApi = '/h5/order.income/doEdit';
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
            $source['income']["receipt_img"] = images_to_array($value['receipt_img']);
            $source['income']["note_img"] = images_to_array($value['note_img']);
            $source['income_category'] = "婚庆";
            $editApi = '/h5/order.income/doEdit';
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
            $editApi = '/h5/order.hotel_item/doEdit';
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
            $source['hotelProtocol']['image'] = images_to_array($value['image']);

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
                $source['car']['car_remark'] = $v['car_remark'];
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


        $commentApi = 'h5/order.comment/create';
        $acceptApi = 'h5/order.confirm/doAccept';
        $rejectApi = 'h5/order.confirm/doReject';
        if ($confirm->status == '0') {
            // 待审核
            if ($param['type']=='confirm_user') {
                // 审核者
                $buttons = [
                    [
                        'id' => 'accept',
                        'label' => '同意',
                        'api' =>$acceptApi
                    ],
                    [
                        'id' => 'reject',
                        'label' => '拒绝',
                        'api' =>$rejectApi
                    ],
                ];

            } else {
                $buttons = [
                    [
                        'id' => 'backend',
                        'label' => '撤销',
                        'api' => $backendApi
                    ]
                ];
            }
        } else if ($confirm->status == '1') {
            // 审核通过
            if ($param['type']=='confirm_user') {
                // 审核者
                $buttons = [];
            } else {
                $buttons = [
                    [
                        'id' => 'update',
                        'label' => '更新',
                        'api' => $editApi
                    ]
                ];
            }
        } else if ($confirm->status == '2') {
            if ($param['type']=='confirm_user') {
                // 审核者
                $buttons = [];
            } else {
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
            }
        } else if ($confirm->status == '13') {
            if ($param['type']=='confirm_user') {
                // 审核者
                $buttons = [];
            } else {
                $buttons = [
                    [
                        'id' => 'update',
                        'label' => '更新',
                        'api' => $editApi
                    ]
                ];
            }
        } else {
            $buttons = [];
        }

        $commentbtn = [
            [
                'id' => 'comment',
                'label' => '评论',
                'api' =>$commentApi
            ]
        ];
        $buttons = array_merge($commentbtn, $buttons);

        // var_dump($confirm->confirm_user_id);
        $user = User::getUser($confirm->confirm_user_id);
        $confirmData = [
            'confirm_user_id' => $user['id'],
            'realname' => $user['realname'],
            'avatar' => $user['avatar'],
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
        $where['order_id'] = $confirm->order_id;
        $orderConfirm = new OrderConfirm();
        $confirmRs = $orderConfirm->where($where)->column('id,status,content,update_time,create_time,image', 'confirm_item_id');
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
                    case 13:
                        $status = '审核撤销';
                        break;
                    default:
                        $status = '待审核';
                }
                $content = $confirmRs[$key]['content'];
                $confirmTime = date('m-d H:i',$confirmRs[$key]['update_time']);
                $image = images_to_array($confirmRs[$key]['image']);
                $comments = [];
                $commentModel = new OrderConfirmComment();
                $where = [];
                $where[] = ['confirm_id', '=', $confirmRs[$key]['id']];
                $confirmComments = $commentModel->where($where)->order('id desc')->select();
                foreach ($confirmComments as $comment) {
                    $cuser = $staffs[$comment['user_id']];
                    if($cuser['user_id'] == $this->user['id']) {
                        $realname = $cuser['realname'];
                    } else {
                        $realname = '我自己';
                    }
                    $comments[] = [
                        'id'        => $comment['id'],
                        'realname'  => $realname,
                        'avatar'    => $cuser['avatar'],
                        'content'   => $comment['content'],
                        'image'     => images_to_array($comment['image'])
                    ];
                }
            } else {
                $status = '待审核';
                $content = '';
                $confirmTime = '';
                $image = [];
                $comments = [];
            }


            $confirmList[] = [
                'id' => $key,
                'title' => $sequence[$key]['title'],
                'status' => $status,
                'content' => $content,
                'image' => $image,
                'confirm_time' => $confirmTime,
                'managerList' => $managerList,
                'comments'  => $comments
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
                'newsTypeList'  => $this->config['news_type_list'],
                'cooperationModeList'  => array_values($this->config['cooperation_mode']),
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
                'packageList'  => array_values($this->packageList),
            ]
        ];

        return json($result);
    }

    public function backend()
    {
        $param = $this->request->param();

        $confirm = OrderConfirm::get($param['order_confirm_id']);
        $rs = OrderConfirm::where('id','=', $param['order_confirm_id'])->update(['status' => 13]);

        $source = json_decode($confirm->source, true);
        foreach ($source as $key=>$value) {
            switch ($key) {
                case 'order':
                    $where = [];
                    $where[] = ['id', '=', $value['id']];
                    \app\common\model\Order::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'banquet':
                    $where = [];
                    $where[] = ['id', '=', $value['id']];
                    \app\common\model\OrderBanquet::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'banquetSuborder':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderBanquetSuborder::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'banquetIncome':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderBanquetReceivables::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'banquetPayment':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderBanquetPayment::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'wedding':
                    $where = [];
                    $where[] = ['id', '=', $value['id']];
                    \app\common\model\OrderWedding::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'weddingSuborder':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderWeddingSuborder::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'weddingIncome':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderWeddingReceivables::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'weddingPayment':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderWeddingPayment::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'hotelItem':
                    $where = [];
                    $where[] = ['id', '=', $value['id']];
                    \app\common\model\OrderHotelItem::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'hotelProtocol':
                    $where = [];
                    $where[] = ['id', '=', $value['id']];
                    \app\common\model\OrderHotelProtocol::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'car':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    $where[] = ['id', '=', $value[1]['id']];
                    \app\common\model\OrderCar::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'wine':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderWine::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'sugar':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderSugar::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'dessert':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderDessert::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'light':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderLight::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'led':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderLed::where($where)->update(['item_check_status'=>13]);
                    break;

                case 'd3':
                    $where = [];
                    $where[] = ['id', '=', $value[0]['id']];
                    \app\common\model\OrderD3::where($where)->update(['item_check_status'=>13]);
                    break;
            }
        }

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
        !isset($param['type']) && $param['type'] = 'order';
        $where = [];
        $where[] = ['company_id', '=', $param['company_id']];
        $where[] = ['timing', '=', $param['type']];
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

    # 来源-积分-合同审核确认，执行逻辑
    public function doAccept()
    {
        $param = $this->request->param();

        $model = new OrderConfirm();
        ## 获取订单信息
        $confirm = $model->where('id', '=', $param['id'])->find();
        $data = $confirm->getData();
        $confirm->content = $param['content'];
        $confirm->status = 1;
        $confirm->operate_id = $this->user['id'];
        $result = $confirm->save();
        if ($result) {
            $newConfirm = new OrderConfirm();
            $newConfirm->where('confirm_no', '=', $confirm->confirm_no)->update(['status' => 1, 'is_checked' => 1]);

            ## 获取当前配置
            $where = [];
            $where[] = ['company_id', '=', $confirm->company_id];
            $where[] = ['timing', '=', $confirm->confirm_type];
            $auditModel = new \app\common\model\Audit();
            $audit = $auditModel->where($where)->find();
            $sequence = json_decode($audit->content, true);
            $current = $confirm->confirm_item_id;
            $next_confirm_item_id = get_next_confirm_item($current, $sequence);
            if (!is_null($next_confirm_item_id)) {
                $config = config();
                $auditConfig = $config['crm']['check_sequence'];
                unset($data['id']);
                unset($data['create_time']);
                unset($data['update_time']);
                unset($data['delete_time']);
                if ($auditConfig[$next_confirm_item_id]['type'] == 'staff') {
                    // 指定人员审核
                    foreach ($sequence[$next_confirm_item_id] as $row) {
                        $data['is_checked'] = 0;
                        $data['status'] = 0;
                        $data['confirm_item_id'] = $next_confirm_item_id;
                        $data['confirm_user_id'] = $row;
                        $orderConfirm = new \app\common\model\OrderConfirm();
                        $orderConfirm->allowField(true)->save($data);
                    }
                } else {
                    $user = \app\common\model\User::getUser($confirm->user_id);
                    // 指定角色审核
                    foreach ($sequence[$next_confirm_item_id] as $row) {
                        $staff = \app\common\model\User::getRoleManager($row, $user);
                        $data['is_checked'] = 0;
                        $data['status'] = 0;
                        $data['confirm_item_id'] = $next_confirm_item_id;
                        $data['confirm_user_id'] = $staff['id'];
                        $orderConfirm = new \app\common\model\OrderConfirm();
                        $orderConfirm->allowField(true)->save($data);
                    }
                }
                \app\common\model\Order::where('id', '=', $confirm->order_id)->update(['check_status' => 1]);
                $this->updateItemStatus($confirm->source, 1);
            } else {

                \app\common\model\Order::where('id', '=', $confirm->order_id)->update(['check_status' => 2]);
                $this->updateItemStatus($confirm->source, 2);
            }

            $json = ['code' => '200', 'msg' => '完成审核是否继续?'];
        } else {
            $json = ['code' => '500', 'msg' => '完成失败是否继续?'];
        }

        return json($json);
    }

    public function doReject()
    {
        $param = $this->request->param();

        $model = new OrderConfirm();
        ## 获取订单信息
        $confirm = $model->where('id', '=', $param['id'])->find();
        $orderId = $confirm->order_id;
        $confirm->content = $param['content'];
        $confirm->status = 2;
        $result = $confirm->save();
        $model->where('confirm_no', '=', $confirm->confirm_no)->update(['is_checked' => 1]);
        \app\common\model\Order::where('id', '=', $orderId)->update(['check_status' => 3]);
        $this->updateItemStatus($confirm->source, 3);
        if ($result) {
            $json = ['code' => '200', 'msg' => '完成审核是否继续?'];
        } else {
            $json = ['code' => '500', 'msg' => '完成失败是否继续?'];
        }
        return json($json);
    }

    protected function updateItemStatus($origin, $status)
    {
        if (empty($origin)) return false;
        $origin = json_decode($origin, true);

        $data['item_check_status'] = $status;
        foreach ($origin as $key => $row) {
            $whereId = [];
            $whereId['id'] = $row['id'];
            switch ($key) {
                case 'order':
                    \app\common\model\Order::where($whereId)->update($data);
                    break;

                case 'banquet':
                    \app\common\model\OrderBanquet::where($whereId)->update($data);
                    break;

                case 'banquetIncome':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderBanquetReceivables::where($where)->update($data);
                    }
                    break;

                case 'banquetPayment':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderBanquetPayment::where($where)->update($data);
                    }
                    break;

                case 'banquetSuborder':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderBanquetSuborder::where($where)->update($data);
                    }
                    break;

                case 'wedding':
                    \app\common\model\OrderWedding::where($whereId)->update($data);
                    break;

                case 'weddingIncome':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderWeddingReceivables::where($where)->update($data);
                    }
                    break;

                case 'weddingPayment':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderWeddingPayment::where($where)->update($data);
                    }
                    break;

                case 'weddingSuborder':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderWeddingSuborder::where($where)->update($data);
                    }
                    break;

                case 'hotelItem':
                    \app\common\model\OrderHotelItem::where($whereId)->update($data);
                    break;

                case 'hotelProtocol':
                    \app\common\model\OrderHotelProtocol::where($whereId)->update($data);
                    break;

                case 'car':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderCar::where($where)->update($data);
                    }
                    break;

                case 'wine':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderWine::where($where)->update($data);
                    }
                    break;

                case 'sugar':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderSugar::where($where)->update($data);
                    }
                    break;

                case 'dessert':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderDessert::where($where)->update($data);
                    }
                    break;

                case 'light':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderLight::where($where)->update($data);
                    }
                    break;

                case 'led':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderLed::where($where)->update($data);
                    }
                    break;

                case 'd3':
                    foreach ($row as $line) {
                        $where = [];
                        $where['id'] = $line['id'];
                        \app\common\model\OrderD3::where($where)->update($data);
                    }
                    break;
            }
        }
    }
}