<?php
// print_r($argv);
$content = isset($argv[1]) ? $argv[1] : '你就是个傻子';
$cli = new swoole_http_client('121.42.184.177', 9501);
$cli->setHeaders(['Trace-Id' => md5(time()),]);
$cli->on('message', function ($_cli, $frame) {
    // var_dump($frame);
});

$cli->upgrade('/', function ($cli) use ($content) {
    // echo $cli->body;
    $data = [
        'action'    => 'chat',
        'data'      => [
            'to'    => [
                'id'    => '38',
                'nickname' => 'liucunzhou',
                'realname'  => 'liucunzhou'
            ],
            'content'   => $content
        ]
    ];
    $json = json_encode($data);
    $cli->push($json);
    // $cli->close();
});