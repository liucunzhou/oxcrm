<?php

namespace app\h5\controller;

use app\common\model\User;
use think\Controller;
use think\facade\Config;
use think\Request;

class Base extends Controller
{
    protected $allocateTypes = [];
    public $user = [];
    protected function initialize(){
        $files = get_included_files();
        // print_r($files);
        // exit;
        $this->allocateTypes = Config::get('crm');
        var_dump($this->allocateTypes);
        exit;

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
