<?php
/**
 * Created by PhpStorm.
 * User: xiaozhu
 * Date: 2019/5/9
 * Time: 16:56
 */

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class UserAuth extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    /**
     * 获取业务逻辑权限
     * @param $id
     * @param bool $update
     * @return array|mixed|null|\PDOStatement|string|Model
     */
    public static function getUserLogicAuth($id, $update=false)
    {

        $cacheKey = 'user_logic_auth';
        $data = redis()->hGet($cacheKey, $id);
        $data = json_decode($data, true);
        if(empty($data) || $update) {
            $map[] = ['user_id', '=', $id];
            $data = self::where($map)->find();
            if (!empty($data)) {
                $data = $data->toArray();
                $json = json_encode($data);
                $result = redis()->hSet($cacheKey, $id, $json);
            }
        }

        return $data;
    }

    public static function getUserAuthSet($id, $update=false)
    {
        $userAuth = self::getUserLogicAuth($id);
        $group = $userAuth['role_ids'];
        $authSet = AuthGroup::getAuthGroup($group);

        return $authSet;
    }

    public static function updateCache($id)
    {
        self::getUserLogicAuth($id, true);
        self::getUserAuthSet($id, true);
    }
}