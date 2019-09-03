<?php
namespace app\http;


class Task
{
    public function sendMessage($server, $data){
        echo "start...";
        $redis = redis();
        $hashKey = 'user-fd';
        $toFd = $redis->hGet($hashKey, $data['to']['id']);
        $toFd && $server->push($toFd, $data['content']);
    }
}