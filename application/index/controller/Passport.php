<?php
namespace app\index\controller;


use app\common\model\Operate;
use app\common\model\OperateLog;
use app\common\model\UserAuth;
use think\Controller;
use think\facade\Request;
use think\facade\Session;


class Passport extends Controller
{
    public function login()
    {
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doLogin()
    {
        $post = Request::post();
        $Validate = new \app\index\validate\User();
        $valid = $Validate->check($post);
        if(!$valid) {
            $this->result([], 500, $Validate->getError());
        }

        $Passport = new \app\common\model\Passport();
        $user = $Passport->checkUserPassword($post);
        if(!$user) {
            $this->result([], 500, '密码不正确');
        }

//        if (Request::isMobile()) {
//            $path = url('Index/User/info');
//        } else {
//            $path = url('Index/Index/index');
//        }
        $path = url('Index/Index/index');

        if($user['is_valid'] == 0){
            $this->result([], 500, '账号已经下线');
        }

        $auth = UserAuth::getUserLogicAuth($user['id']);
        if(empty($auth)) {
            $this->result([], 500, '尚未开通权限,请联系管理员开通权限!');
        }

        Session::set('user', $user);
        // 加入登录记录
        $ip = Request::ip();
        $action = Request::path();
        // OperateLog::append($user, $action, '-', $ip);
        OperateLog::dologinLog($user, $ip);

        $data = [
            'redirect' => $path
        ];
        $this->result($data, 200, '登录成功');
    }

    public function logout()
    {
        Session::delete("user");
        $this->redirect('passport/login');
    }
}