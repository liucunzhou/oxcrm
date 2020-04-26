<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2020/4/25
 * Time: 22:56
 */

namespace app\h5\controller\order;


use app\common\model\OrderConfirm;
use app\common\model\User;
use app\h5\controller\Base;

class Confirm extends Base
{
    protected function initialize()
    {
        return parent::initialize();
    }

    # comnpany_id,创建时的审核进程
    public function getConfirmSequence()
    {
        $param = $this->request->param();
        $audit = \app\common\model\Audit::where('company_id', '=', $param['company_id'])->find();
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
        $orderModel = new \app\common\model\Order();
        $order = $orderModel->where('id', '=', $param['id'])->find();

        $audit = \app\common\model\Audit::where('company_id', '=', $order->company_id)->find();
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
        $where['order_id'] = $param['id'];
        $orderConfirm = new OrderConfirm();
        $confirmRs = $orderConfirm->where($where)->column('id,status,content','confirm_item_id');

        $isEdit = 0;
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
                        $isEdit = 1;
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
                'isEdit'        => $isEdit
            ]
        ];

        return json($result);
    }
}