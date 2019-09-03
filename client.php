<?php
if (!isset($argv[1])) return die();
$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

$client->on('connect', function($cli) {
    $cli->send("Hello world\n");
});
$client->on('receive', function($cli, $data) {
    echo "Received: ".$data."\n";
});
$client->on('error', function($cli) {
    echo "Connect failed\n";
});
$client->on('close', function($cli) {
    echo "Connection close\n";
});

$client->connect('121.42.184.177', 9501, 0.5);
die();
$cli = new swoole_http_client('121.42.184.177', 9501);
$cli->setHeaders(['Trace-Id' => md5(time()),]);
$cli->on('message', function ($_cli, $frame) {
    print_r($frame);
});


$message = json_decode($argv[1], true);
$cli->upgrade('/', function ($cli) use ($message) {
    if (!isset($message['content'])) return false;
    $data = [
        'action'    => 'chat',
        'data'      => [
            'to'    => [
                'id'    => $message['to']['id'],
                'nickname' => $message['to']['nickname'],
                'realname'  => $message['to']['realname']
            ],
            'content'   => $message['content']
        ]
    ];
    $json = json_encode($data);
    $cli->push($json);
});
die();
$client = new swoole_client(SWOOLE_SOCK_TCP);
if (!$client->connect('121.42.184.177', 9501, 0.5)) {
    die('connect failed.');
} else {
    echo "\n连接成功\n";
}

if (!$client->send('Hello world')) {
    die('send failed.');
} else {
    echo "\n send success \n";
}
$client->close();