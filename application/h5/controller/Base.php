<?php

namespace app\h5\controller;

use app\common\model\AuthGroup;
use app\common\model\User;
use Firebase\JWT\JWT;
use think\Controller;
use think\facade\Config;
use think\Request;

class Base extends Controller
{
    protected $model = null;
    // 分配类型
    protected $allocateTypes = [];
    public $user = [];
    public $role = [];
    public $config = [];
    protected function initialize(){
        $config = config();
        $this->config = $config['crm'];
        $this->allocateTypes = $this->config['allocate_type_list'];
        $token = $this->request->header("token");
        if(empty($token)) $token = $this->request->param('token');

        if( empty($token) ){
            $arr = [
                'code'  => '405',
                'msg'   =>  'token为空',
            ];
            return json($arr);
        }
        $decode = JWT::decode($token, 'hongsi', ['HS256']);

        if(!isset($decode->id) && $decode->id > 0) {
            $arr = [
                'code'  => '405',
                'msg'   =>  'token解析失败',
            ];
            return json($arr);
        }

        $where['id'] = $decode->id;
        $this->user = User::where($where)->find();
        if(empty($this->user)) {
            $arr = [
                'code'  => '405',
                'msg'   =>  '获取用户信息失败',
            ];
            return json($arr);
        }
        if($this->user['role_id'] > 0) {
            $this->role = AuthGroup::getAuthGroup($this->user['role_id']);
        }
    }

    protected function getConfirmProcess($companyId, $timing='order')
    {
        $where = [];
        $where[] = ['company_id', '=', $companyId];
        $where[] = ['timing', '=', $timing];
        $audit = \app\common\model\Audit::where($where)->find();

        if (empty($audit)) {
            return [];
        }

        if (empty($audit->content)) {
            return [];
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

        return $confirmList;
    }

}
