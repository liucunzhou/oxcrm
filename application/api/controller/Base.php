<?php
/**
 * Created by PhpStorm.
 * User: tangyun
 * Date: 2019/8/17
 * Time: 18:33
 */

namespace app\api\controller;


use Firebase\JWT\JWT;
use think\Controller;
use think\facade\Request;

class Base extends Controller
{
    public $user = [];
    public $params = [];

    protected function initialize()
    {
        $params = Request::param();
        if(!isset($params['token'])) {
            echo xjson([
                'code'  => '400',
                'msg'   => 'token不能为空'
            ]);
            exit;
        } else {
            $this->params = $params;
        }

        $user = JWT::decode($params['token'], 'hongsi',['HS256']);
        if(empty($user)) {
            xjson([
                'code'   => '401',
                'msg'   => 'token解析失败'
            ]);
            exit;
        }

        $this->user = (array)$user;
    }

}