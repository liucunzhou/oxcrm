<?php
namespace app\api\controller;

use app\common\model\OperateLog;
use app\common\model\UserAuth;
use Firebase\JWT\JWT;
use think\Controller;
use think\facade\Request;

class Passport extends Controller
{
    public function doLogin()
    {
        $post = Request::param();
        $Validate = new \app\index\validate\User();
        $valid = $Validate->check($post);
        if(!$valid) {
            return xjson([
                'code'  => '500',
                'msg'   => $Validate->getError()
            ]);
        }

        ### 密码检测
        $Passport = new \app\common\model\Passport();
        $user = $Passport->checkUserPassword($post);
        if(!$user) {
            return xjson([
                'code'  => '500',
                'msg'   => '账号或者密码错误'
            ]);
        } else if($user['is_valid'] == 0){
            return xjson([
                'code'  => '500',
                'msg'   => '账号已经下线'
            ]);
        }

        ### 验证权限
        $auth = UserAuth::getUserLogicAuth($user['id']);
        if(empty($auth)) {
            return xjson([
                'code'  => '500',
                'msg'   => '尚未开通权限,请联系管理员开通权限!'
            ]);
        }

        ### 记录登陆logo
        $ip = Request::ip();
        OperateLog::appendTo($user, $ip);

        // print_r($post);
        unset($user['password']);
        $token = JWT::encode($user, 'hongsi');

        return xjson([
            'code'  => 200,
            'msg'   => '登陆成功',
            'result' => [
                'token' => $token,
                'user'  => $user
            ]
        ]);
    }
}