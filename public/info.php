<?php
phpinfo();
exit;
$c = 10;
while($c--) {
    go(function () {
        //这里使用 sleep 5 来模拟一个很长的命令
        co::exec("sleep 5");
    });
}

new swoole_server();
echo "run complete...";