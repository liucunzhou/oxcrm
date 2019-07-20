<?php
namespace app\index\controller;

use app\index\model\SwooleClient;
use think\Controller;

class Test extends Controller
{
    public function send()
    {
        $SwooleClient = new SwooleClient();
        $data = [
            'action'    => 'chat',
            'data'      => [
                'to'    => [
                    'id'    => '38',
                    'nickname' => 'liucunzhou',
                    'realname'  => 'liucunzhou'
                ],
                'content'   => 'Hi! liucunzhou123...'
            ]
        ];

        $result = $SwooleClient->send(json_encode($data)."\n");
        var_dump($result);
    }
}