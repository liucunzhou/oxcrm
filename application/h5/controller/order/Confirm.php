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
    protected $confirmStatusList = [0=>'待审核', 1=>'审核通过', 2=>'审核驳回'];

    protected function initialize()
    {
        return parent::initialize();
    }

    # 我审核的
    public function myConfirmed()
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

        if($list->isEmpty()) {
            $result = [
                'code'  => '200',
                'msg'   => '暂无审核数据'
            ];
        } else {
            $data = [];
            foreach ($list as $key => $value) {
                $order = \app\common\model\Order::get($value->order_id);
                $data[]  = [
                    'title'         => $value['confirm_intro'],
                    'create_time'   => $value['create_time'],
                    'status'        => $this->confirmStatusList[$value['status']],
                    'company'       => $companies[$value['company_id']]['title'],
                    'news_type'     => $newsTypes[$order['news_type']],
                    'user'          => $users[$value['user_id']]['realname']
                ];
            }

            $result = [
                'code'  => '200',
                'msg'   => '获取数据成功',
                'data'  => [
                    'confirmList'   => $data
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

        if($list->isEmpty()) {
            $result = [
                'code'  => '200',
                'msg'   => '暂无审核数据'
            ];
        } else {
            $data = [];
            foreach ($list as $key => $value) {
                $order = \app\common\model\Order::get($value->order_id);
                $data[]  = [
                    'title'         => $value['confirm_intro'],
                    'create_time'   => $value['create_time'],
                    'status'        => $this->confirmStatusList[$value['status']],
                    'company'       => $companies[$value['company_id']]['title'],
                    'news_type'     => $newsTypes[$order['news_type']],
                    'user'          => $users[$value['user_id']]['realname']
                ];
            }

            $result = [
                'code'  => '200',
                'msg'   => '获取数据成功',
                'data'  => [
                    'confirmList'   => $data
                ]
            ];
        }

        return json($result);
    }

    # comnpany_id,创建时的审核进程
    public function getConfirmSequence()
    {
        $param = $this->request->param();
        $where = [];
        $where[] = ['company_id', '=', $param['company_id']];
        $where[] = ['timing', '=', 'income'];
        $audit = \app\common\model\Audit::where($where)->find();

        if(empty($audit)) {
            $result = [
                'code'  => '400',
                'msg'   => '尚未设置审核顺序'
            ];
            return json($result);
        }

        if(empty($audit->content)) {
            $result = [
                'code'  => '400',
                'msg'   => '尚未设置审核顺序'
            ];
            return json($result);
        }

        $avatar = 'https://www.yusivip.com/upload/commonAppimg/hs_app_logo.png';
        $staffs = User::getUsers(false);
        ## 审核全局列表
        $sequence = $this->config['check_sequence'];
        $auth = json_decode($audit->content, true);
        $confirmList = [];
        foreach ($auth as $key=>$row) {
            $managerList = [];
            $type = $sequence[$key]['type'];
            if($type == 'role') {
                // 获取角色
                foreach ($row as $v)
                {
                    $user = User::getRoleManager($v, $this->user);
                    $managerList[] = [
                        'id'        => $user['id'],
                        'realname'  => $user['realname'],
                        'avatar'    => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            } else {
                foreach ($row as $v) {
                    if(!isset($staffs[$v])) continue;
                    $user = $staffs[$v];
                    $managerList[] = [
                        'id'        => $user['id'],
                        'realname'  => $user['realname'],
                        'avatar'    => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            }
            $confirmList[] = [
                'id'    => $key,
                'title' => $sequence[$key]['title'],
                'managerList'   => $managerList
            ];
        }

        $result = [
            'code'  => '200',
            'msg'   => '获取数据成功',
            'data'  => [
                'confirmList'   => $confirmList
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
        if(empty($orderConfirm)) {
            $result = [
                'code'  => '200',
                'msg'   => '此单已锁',
                'data'  => [
                    'confirmList'   => []
                ]
            ];

            return json($result);
        }

        $where = [];
        $where[] = ['company_id', '=', $orderConfirm->company_id];
        $where[] = ['timing', '=', $orderConfirm->confirm_type];
        $audit = \app\common\model\Audit::where($where)->find();
        if(empty($audit)) {
            $result = [
                'code'  => '400',
                'msg'   => '尚未设置审核顺序'
            ];
            return json($result);
        }

        if(empty($audit->content)) {
            $result = [
                'code'  => '400',
                'msg'   => '尚未设置审核顺序'
            ];
            return json($result);
        }

        ### 获取所有审核的列表
        $where = [];
        $where['confirm_no'] = $orderConfirm->confirm_no;
        $where['order_id'] = $param['id'];
        $orderConfirm = new OrderConfirm();
        $confirmRs = $orderConfirm->where($where)->column('id,status,content','confirm_item_id');

        $avatar = 'https://www.yusivip.com/upload/commonAppimg/hs_app_logo.png';
        $staffs = User::getUsers(false);
        ## 审核全局列表
        $sequence = $this->config['check_sequence'];
        $auth = json_decode($audit->content, true);
        $confirmList = [];
        foreach ($auth as $key=>$row) {
            $managerList = [];
            $type = $sequence[$key]['type'];
            if($type == 'role') {
                // 获取角色
                foreach ($row as $v)
                {
                    $user = User::getRoleManager($v, $this->user);
                    $managerList[] = [
                        'id'        => $user['id'],
                        'realname'  => $user['realname'],
                        'avatar'    => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            } else {
                foreach ($row as $v) {
                    if(!isset($staffs[$v])) continue;
                    $user = $staffs[$v];
                    $managerList[] = [
                        'id'        => $user['id'],
                        'realname'  => $user['realname'],
                        'avatar'    => $user['avatar'] ? $user['avatar'] : $avatar
                    ];
                }
            }

            if($confirmRs[$key]) {
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
                'id'        => $key,
                'title'     => $sequence[$key]['title'],
                'status'    => $status,
                'content'   => $content,
                'managerList'   => $managerList
            ];
        }

        $result = [
            'code'  => '200',
            'msg'   => '获取数据成功',
            'data'  => [
                'confirmList'   => $confirmList,
            ]
        ];

        return json($result);
    }
}