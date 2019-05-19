<?php /*a:1:{s:57:"/data/platform/application/index/view/passport/login.html";i:1555753614;}*/ ?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="E5W,微信开源开发框架,小程序开发框架" name="keywords"/>
    <meta content="E5W，为移动应用提供后端运营管理的CMS系统" name="description"/>
    <title>E5W系统</title>
    <link href="/static/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/static/css/font-awesome.min.css" />
    <link rel="stylesheet" href="/static/css/e5ws.css?<?php echo rand(); ?>">
    <script src="/static/js/jquery-1.11.3.min.js"></script>
    <script src="/static/layer/layer.js"></script>
    <script src="/static/js/passport.js?<?php echo rand(); ?>"></script>
</head>
<body>
    <div class="wrap">
        <div class="scope">
            <img src="/static/images/login_bg.png"/>
        </div>
        <form class="login-form" action="<?php echo url('doLogin'); ?>" method="post">
            <div class="form form-body">
                <h6>欢迎使用E5W系统!</h6>
                <div class="inputs">
                    <div class="control-group">
                        <label class="control-label" for="nickname">用户名</label>
                        <div class="controls">
                            <span class="icon icon-user focus"></span>
                            <input type="text" class="input" id="nickname" name="nickname" placeholder="请输入用户名">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="password">密码</label>
                        <div class="controls">
                            <span class="icon icon-key focus"></span>
                            <input type="password" class="input" id="password" name="password" placeholder="请输入密码">
                        </div>
                    </div>
                </div>
                <div class="controls warning"></div>
                <div class="control-group">
                    <div class="controls">
                        <input type="checkbox" id="checkbox"/>
                        <label for="checkbox">自动登录</label>
                    </div>
                    <div class="controls">
                        <button type="submit" class="btn btn-login">登 录</button>
                    </div>
                    <div class="controls">
                        还没账号?
                    </div>
                    <div class="controls">
                        <a class="btn btn-register" href="http://phome.e5ws.com/index.php?s=/w0/Home/User/register.html">
                            立 即 注 册
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>