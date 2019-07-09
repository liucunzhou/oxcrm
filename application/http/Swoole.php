<?php
/**
 * Created by PhpStorm.
 * User: xiaozhu
 * Date: 2019/5/8
 * Time: 14:26
 */

namespace app\http;

use think\swoole\Server;

class Swoole extends Server
{
    protected $host = '121.42.184.177';
    protected $port = 9501;
    protected $serverType = 'socket';

    protected $option = [
        'worker_num'=> 4,
        'daemonize' => false,
        'backlog'  => 128,

    ];

    public function onOpen($server, $request)
    {
        echo "server: handshake success with fd{$request->fd}\n";
    }

    public function onReceive($server, $fd, $from_id, $data)
    {
        echo "This is receiving..";
        $data = json_decode($data, true);
        print_r($data);
        $server->send($fd, 'Swoole: '.$data);
    }

    public function onMessage($server, $frame)
    {

        // $data = json_decode($frame->data, true);
        // print_r($data);
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        // $server->push($data['from'], "已经发送给");
        // $server->push($data['to'], "从{$data['from']}发送给{$data['to']}");
        $server->push($frame->fd, "我在想你....");
    }

    public function onRequest($request, $response)
    {
        $response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");
    }

    public function onClose($ser, $fd)
    {
        echo "client {$fd} closed\n";
    }
}