<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class AuthGroup extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'modify_time';

    ### 软删除设置
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;


    public static function getRoles()
    {
        $list = self::column('id,title,auth_set');

        return $list;
    }

    public static function getNames($roles, $croles)
    {
        $str = '';
        if(!empty($croles)){
            $croles = explode(",", $croles);
            foreach($croles as $roleId){
                $roleArr[] = $roles[$roleId]['title'];
            }

            $str = implode(',', $roleArr);
        } else {
            $str = "-";
        }

        return $str;
    }

    public static function getAuthGroup($id, $update=false)
    {
        $cacheKey = 'auth_group';
        $data = redis()->hGet($cacheKey, $id);
        $data = json_decode($data, true);
        if(empty($data) || $update) {
            $map[] = ['id', '=', $id];
            $data = self::where($map)->field('id,title,auth_set')->find()->toArray();
            $json = json_encode($data);
            $result = redis()->hSet($cacheKey, $id,$json);
        }

        return $data;
    }

    /**
     * 获取分组下的权限集合
     * @param $id
     * @return array
     */
    public static function getAuthGroupItems($id)
    {
        $group = self::getAuthGroup($id);
        $data = explode(',', $group['auth_set']);

        return $data;
    }

    /**
     * 更新权限分组信息
     * @param $id
     */
    public static function updateCache($id)
    {
        self::getAuthGroup($id, true);
    }
}