<?php
namespace app\index\controller;

use app\common\model\Client;
use think\Controller;

class Test extends Controller
{
    public function send()
    {
        $Client = new Client();
        $Client->data = [
            'action'    => 'sendMessage',
            'message'   => [
                'to'    => [
                    'id'    => '1',
                    'nickname' => 'liucunzhou',
                    'realname'  => 'liucunzhou'
                ],
                'content'   => 'Hi! ...'
            ]
        ];

        $result = $Client->sendMessage();
        var_dump($result);
    }

    public function index() {
        $client = new \swoole_client(SWOOLE_TCP | SWOOLE_ASYNC);
        $client->on("connect", function($cli) {
            $cli->send("hello world\n");
        });

        $client->on("receive", function($cli, $data) {
            echo "received: $data\n";
            sleep(1);
            $cli->send("hello\n");
        });

        $client->on("close", function($cli){
            echo "closed\n";
        });

        $client->on("error", function($cli){
            exit("error\n");
        });

        $client->connect('121.42.184.177', 9501, 0.5);
    }
}