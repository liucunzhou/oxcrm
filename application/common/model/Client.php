<?php


namespace app\common\model;


class Client
{
    public $msg = '';
    public $data = [];

    public function sendMessage()
    {
        $cli = new \swoole_client(SWOOLE_SOCK_TCP);
        if(!$cli->connect('121.42.184.177', 9501)) {

            return false;
        } else {
            echo 'connect success';
        }

        if(!empty($this->data)) {
            $rel = $cli->send(json_encode($this->data));
        } else {
            $rel = $cli->send($this->msg);
        }

        $cli->recv();
        $cli->close();

        return $rel;
    }
}