<?php

namespace app\http\middleware;

use think\facade\Session;

class Auth
{
    public function handle($request, \Closure $next)
    {
        $user = Session::get('user');
        if(empty($user)) {
            echo "<script>window.parent.location.href='/wash.php?s=ucenter.passport/login';</script>";
        }
        return $next($request);
    }
}
