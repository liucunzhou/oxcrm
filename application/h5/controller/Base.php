<?php

namespace app\h5\controller;

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
    protected function initialize(){
        $config = config();
        $crmConfig = $config['crm'];
        $this->allocateTypes = $crmConfig['allocate_type_list'];
        /**
        $token = $this->request->header("token");
        $decode = JWT::decode($token, '');

        if(empty($decode['id'])) {
            return json();
        }

        $where['id'] = $decode['id'];
        $this->user = User::where($where)->find();
        if(empty($this->user)) {
            return json();
        }
         * **/
    }
}
