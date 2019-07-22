<?php
namespace app\index\model;

use think\Model;
use think\model\concern\SoftDelete;

class User extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public function userAuth()
    {
        return $this->hasOne('UserAuth');
    }

    /**
     * 获取后台用户详情
     * @param $id
     * @param bool $update
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUser($id,$update=false)
    {
        $cacheKey = 'user';
        $data = redis()->hGet($cacheKey, $id);
        $data = json_decode($data, true);
        if(empty($data) || $update) {
            $map[] = ['id', '=', $id];
            $data = self::where($map)->field('id,role_id,nickname,realname,dingding,mobile,email')->find()->toArray();
            $json = json_encode($data);
            $result = redis()->hSet($cacheKey, $id, $json);
        }

        return $data;
    }

    public static function getUsers($update=false)
    {
        $cacheKey = 'users';
        $data = redis()->get($cacheKey);
        if(empty($data) || $update) {
            $data = self::order('is_valid desc,sort desc,id asc')->column('id,nickname,realname', 'id');
            $json = json_encode($data);
            $result = redis()->set($cacheKey, $json);
        } else {

            $data = json_decode($data, true);
        }

        return $data;
    }


    public static function checkAuth($id, $auth)
    {
        $user = self::getUser($id);
        if (empty($user['role_id'])) {
            return [];
        }

        $authSet = self::getUserAuthSet($id);
        if(in_array($auth, $authSet)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getUserAuthSet($id, $update=false)
    {
        $user = self::getUser($id);
        if (empty($user['role_id'])) {
            return [];
        }

        $cacheKey = 'user_group';
        $data = redis()->hGet($cacheKey, $id);
        $data = json_decode($data, true);
        if(empty($data) || $update) {

            $authIds = [];
            $roleIds = explode(',', $user);
            foreach ($roleIds as $role) {
                $authIds[] = AuthGroup::getAuthGroupItems($role);
            }

            $authIds = array_unique($authIds);
            foreach ($authIds as $authId) {
                $data[] = Auth::getAuth($authId);
            }

            $json = json_encode($data);
            $result = redis()->hSet($cacheKey, $id, $json);
        }

        return $data;
    }

    public static function updateCache($id)
    {
        self::getUser($id, true);
        self::getUsers(true);
        self::getUserAuthSet($id, true);
    }
}