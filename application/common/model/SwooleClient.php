<?php
namespace app\common\model;

use Swoole\Client;

class SwooleClient extends Client
{

    public function __construct(int $sock_type=SWOOLE_SOCK_TCP, $sync_type = SWOOLE_SOCK_SYNC, string $connectionKey = '')
    {
        parent::__construct(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC, 'system');
        $host = config("swoole.host");
        $port = config("swoole.port");
        $this->connect($host, $port);
    }

    public function sendMessage($data)
    {
        $result = $this->send($data);
        return $result;
    }

}