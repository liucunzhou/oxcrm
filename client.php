<?php
if (!isset($argv[1])) return die();
$cli = new swoole_http_client('121.42.184.177', 9501);
$cli->setHeaders(['Trace-Id' => md5(time()),]);
$cli->on('message', function ($_cli, $frame) {

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