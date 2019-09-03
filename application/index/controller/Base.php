<?php
namespace app\index\controller;


use think\Controller;
use think\facade\Request;

class Base extends Controller
{
    public $user = [];

    protected function initialize()
    {
        // 验证登录
        $user = session("user");

        if(!$user) $this->redirect('/index/passport/login', ['parent'=>1]);
        $this->user = $user;
        $this->assign('user', $user);

        // 监控登陆的端口
//        if (Request::isMobile()) {
//            $path = $this->app->getModulePath();
//            $path .= 'mobile/';
//            $this->view->config('view_path', $path);
//        }
    }

}