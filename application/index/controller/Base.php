<?php
namespace app\index\controller;


use think\Controller;
use think\facade\Request;

class Base extends Controller
{
    protected function initialize()
    {
        // 验证登录
        $user = session("user");
        if(!$user) $this->redirect('/index/passport/login');
        $this->assign('user', $user);

        if (Request::isMobile()) {
            $path = $this->app->getModulePath();
            $path .= 'mobile/';
            $this->view->config('view_path', $path);
        }
    }
}