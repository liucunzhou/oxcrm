<?php
namespace app\index\controller;


use think\Controller;
use think\facade\Request;
use think\facade\Session;

use vod\Request\V20170321\GetPlayInfoRequest;
use vod\Request\V20170321\GetVideoPlayAuthRequest;


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

        $Passport = new \app\index\model\Passport();
        $isPassword = $Passport->checkUserPassword($post);
        if(!$isPassword) {
            $this->result([], 500, '密码不正确');
        }

        Session::set('user', $post);
        $data = [
            'redirect' => url('Index/Index/index')
        ];
        $this->result($data, 200, '登录成功');
    }

    public function logout()
    {
        Session::delete("user");
        $this->redirect('Index/Passport/login');
    }
}