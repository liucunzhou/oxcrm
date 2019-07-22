<?php
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

    }

    public function onReceive($server, $fd, $from_id, $data)
    {

    }

    public function onMessage($server, $frame)
    {
        $message = json_decode($frame->data, true);
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        switch ($message['action']) {
            case 'login':
                $this->doLogin($frame, $message['data']);
                break;

            case 'chat':
                $this->chat($server, $frame, $message['data']);
                break;
        }
    }

    public function onRequest($request, $response)
    {

    }

    public function onClose($server, $fd)
    {
        $this->doLogout($fd);
    }

    /**
     * {
     *  'action':'login',
     *  'data':{
     *      'user':{
     *          'id':'1',
     *          'nickname' : 'xxx',
     *          'realname' : 'zz'
     *      }
     *   }
     * }
     */
    protected function doLogin($frame, $data)
    {
        if($data['user']['id']) {
            $redis = redis();
            // 对应fd和用户id
            $hashKey = 'fd-user';
            while ($redis->hSet($hashKey, $frame->fd, $data['user']['id'])) ;

            // 对应用户id和fd
            $hashKey = 'user-fd';
            while ($redis->hSet($hashKey, $data['user']['id'], $frame->fd)) ;

            // 设置用户登录时间
            $hashKey = 'user-online-data:' . $data['user']['id'];
            $onlineTime = $redis->hGet($hashKey, 'start_time');
            if (empty($onlineTime)) {
                while ($redis->hMSet($hashKey, ['start_time' => time()])) ;
            }
        }
    }

    /**
     * 用户退出
     * @param $fd
     */
    protected function doLogout($fd)
    {
        $redis = redis();
        ### 删除fd关联的用户id
        $hashKey = 'fd-user';
        $userId = $redis->hGet($hashKey, $fd);
        if($userId) {
            while($redis->hDel($hashKey, $fd));

            ### 删除用户关联的fd
            $hashKey = 'user-fd';
            while($redis->hDel($hashKey, $userId));

            ### 设置用户退出时间
            $hashKey = 'user-online-data:' . $userId;
            while ($redis->hMSet($hashKey, ['end_time' => time()])) ;
        }
    }

    /**
     * {
     *  'action' : 'chat',
     *  'data'  : {
     *      'from' : {
     *          'id' : 'id',
     *          'realname' : 'realname',
     *          'nickname' : 'nickname'
     *      },
     *      'to' : {
     *          'id' : 'id',
     *          'realname' : 'realname',
     *          'nickname' : 'nickname'
     *      },
     *      'type' : '$type',
     *      'content' : 'content'
     *  }
     * }
     * $type text|image|voice|video 默认text
     * @param $server
     * @param $frame
     */
    protected function chat($server, $frame, $data)
    {
        $redis = redis();
        $hashKey = 'user-fd';
        $toFd = $redis->hGet($hashKey, $data['to']['id']);
        $toFd && $server->push($toFd, $data['content']);
    }
}