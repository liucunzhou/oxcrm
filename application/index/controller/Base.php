<?php
namespace app\index\controller;


use think\Controller;

class Base extends Controller
{
    protected function initialize()
    {
        // 验证登录
        $user = session("user");
        if(!$user) $this->redirect('/index/passport/login');
        $this->assign('user', $user);

    }
}