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
    // 分配类型
    protected $allocateTypes = [];
    public $user = [];
    public $role = [];
    protected function initialize(){
        $config = config();
        $crmConfig = $config['crm'];
        $this->allocateTypes = $crmConfig['allocate_type_list'];

        $token = $this->request->header("token");
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
}
