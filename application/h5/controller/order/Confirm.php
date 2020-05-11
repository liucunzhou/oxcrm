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
    protected $confirmStatusList = [0 => '待审核', 1 => '审核通过', 2 => '审核驳回', 13 => '审核撤销'];

    protected function initialize()
    {
        return parent::initialize();
    }

    # 我审核的
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

    # 审核我的
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

    ## 订单的审核列表
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
                    }else if ($key == 'hotelProtocol') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/light/light';
                        break;
                    } else if ($key == 'sugar') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/sugar/sugar';
                        break;
                    } else if ($key == 'dessert') {
                        $list[$confirmNo]['path'] = '/pages/addOrderItems/path/path';
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

    public function detail()
    {
        $param = $this->request->param();

        $confirm = OrderConfirm::get($param['id']);
        $origin = json_decode($confirm->source, true);
        $source = [];
        foreach ($origin as $key =>$value) {
            if ($key == 'order') {

            } else if ($key == 'banquet') {

            } else if ($key == 'banquetSuborder') {

            } else if ($key == 'wedding') {

            } else if ($key == 'weddingSuborder') {

            } else if ($key == 'banquetPayment') {

            } else if ($key == 'weddingPayment') {

            } else if ($key == 'banquetIncome' ) {
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
                $source['income']["remark"] = $value["remark"];
                $source['income']["receipt_img"] = $value["receipt_img"];
                $source['income']["note_img"] = $value["note_img"];
            } else if ($key == 'weddingIncome') {
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
                $source['income']["remark"] = $value["remark"];
                $source['income']["receipt_img"] = $value["receipt_img"];
                $source['income']["note_img"] = $value["note_img"];

            } else if ($key == 'hotelItem') {

            } else if ($key == 'hotelProtocol') {

            } else if ($key == 'car') {

            } else if ($key == 'wine') {

            }else if ($key == 'hotelProtocol') {

            } else if ($key == 'sugar') {

            } else if ($key == 'dessert') {

            } else if ($key == 'light') {

            } else if ($key == 'led') {

            } else if ($key == 'd3') {

            }
        }

        if($confirm->status == '0') {
            $buttons = [
                'backout'   => '撤销'
            ];
        } else if ($confirm->status == '1') {
            $buttons = [
                'update'   => '更新'
            ];
        } else if ($confirm->status == '2') {
            $buttons = [
                'backout'  => '撤销',
                'update'   => '更新'
            ];
        } else {
            $buttons = [];
        }

        $confirmData = [
            'confirm_no'    => $confirm->confirm_no,
            'confirm_intro' => $confirm->confirm_intro,
            'status'        => $this->confirmStatusList[$confirm->status]
        ];
        $result = [
            'code'  => '200',
            'msg'   => '获取数据成功',
            'data'  => [
                'confirm'   => $confirmData,
                'detail'    => $source,
                'buttons'   => $buttons
            ]
        ];

        return json($result);
    }

    # comnpany_id,创建时的审核进程
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

    # 订单ID 参数id
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
}