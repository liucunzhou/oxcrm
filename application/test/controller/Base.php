<?php
namespace app\test\controller;

use think\Controller;

class Base extends Controller
{
    protected $model = null;
    // 分配类型
    public $user = [];
    protected function initialize(){

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
    }
}
