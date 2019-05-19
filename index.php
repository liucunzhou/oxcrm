<?php
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', false);
define('BUILD_DIR_SECURE', false);
// 定义应用目录
define('BIND_MODULE', 'Admin');
define('BUILD_CONTROLLER_LIST','System,Memeber,News,Auth');
define('APP_PATH','./application/');
define('SITE_PATH',__DIR__);

// 引入ThinkPHP入口文件
require './core/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单