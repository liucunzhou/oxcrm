<?php
namespace app\common\model;

use think\facade\Request;
use think\facade\Session;
use think\Model;
use think\model\concern\SoftDelete;

class OperateLog extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function dologinLog($user, $current)
    {

        $data['user_id'] = $user['id'];
        $data['realname'] = $user['realname'];
        $data['action'] = Request::path();
        $data['origin_content'] = '-';
        $data['updated_content'] = $current;
        $data['create_time'] = time();
        self::insert($data);
    }

    public static function appendTo(&$Model)
    {
        $user = Session::get("user");
        if(empty($user)) return false;

        $originArr = $Model->getOrigin();
        $origin = json_encode($originArr);

        $currentArr = $Model->getData();
        $current = json_encode($currentArr);

        $data['user_id'] = $user['id'];
        $data['realname'] = $user['realname'];
        $data['action'] = Request::path();
        $data['origin_content'] = $origin;
        $data['updated_content'] = $current;
        $data['create_time'] = time();
        self::insert($data);
    }
}