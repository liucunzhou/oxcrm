<?php
namespace app\common\model;

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
        $map[] = ['id', '=', $id];
        $data = self::where($map)->field('id,role_id,department_id,nickname,realname,dingding,mobile,email,avatar')->find()->toArray();

        return $data;
    }

    public static function getUserByNo($userNo, $update=false)
    {
        $data = self::where(['user_no'=>$userNo])->column('user_no,id,role_id,department_id,nickname,realname,dingding,mobile,email,province_id,city_id,avatar', 'user_no');
        return $data;
    }

    public static function getUsers($withTrashed=true,$update=false)
    {
        if($withTrashed) {
            $data = self::withTrashed()->order('is_valid desc,sort desc,id asc')->column('id,role_id,department_id,nickname,realname,dingding,mobile,email,province_id,city_id,avatar', 'id');
        } else {
            $data = self::order('is_valid desc,sort desc,id asc')->column('id,role_id,department_id,nickname,realname,dingding,mobile,email,province_id,city_id,avatar', 'id');
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

        $data = AuthGroup::getAuthGroup($user['role_id']);
        return $data;
    }

    public static function getUsersByRole($roleId)
    {
        $data = self::where(['role_id'=>$roleId])->column('user_no,id,role_id,department_id,nickname,realname,dingding,mobile,email,province_id,city_id', 'user_no');

        return $data;
    }

    public static function getUsersByDepartmentId($departmentId, $withTrashed=true)
    {
        $departments = Department::getTree($departmentId);
        if(empty($departments)) return false;
        $map = [];
        if(count($departments) > 1) {
            $map[] = ['department_id', 'in', $departments];
        } else {
            $map[] = ['department_id', '=', $departments[0]];
        }
        if($withTrashed) {
            $users = self::withTrashed()->where($map)->column('id');
        } else {
            $users = self::where($map)->column('id');
        }

        return $users;
    }

    public static function getUsersInfoByDepartmentId($departmentId, $withTrashed=true, $fields = '')
    {
        if(empty($fields)) {
            $fields = 'id,role_id,department_id,nickname,realname';
        }

        $departments = Department::getTree($departmentId);
        if(empty($departments)) return false;
        $map = [];
        if(count($departments) > 1) {
            $map[] = ['department_id', 'in', $departments];
        } else {
            $map[] = ['department_id', '=', $departments[0]];
        }

        if($withTrashed) {
            $users = self::withTrashed()->where($map)->column($fields);
        } else {
            $users = self::where($map)->column($fields);
        }

        return $users;
    }

    public static function getTopManager($user) {
        if(empty($user['department_id'])) return 0;

        $department = Department::getDepartment($user['department_id']);
        $path = explode('-',$department['path']);

        $managerDeparentId = $path[1];
        $managers = User::where(['department_id'=>$managerDeparentId])->column('id');
        if(empty($managers)) {
            return 0;
        } else {
            return $managers[0];
        }
    }

    public static function getRoleManager($roleId, $user) {
        $department = Department::getDepartment($user['department_id']);
        $path = explode('-',$department['path']);
        $departments = array_filter($path);
        rsort($departments);

        $where = [];
        $where[] = ['role_id', '=', $roleId];
        $where[] = ['department_id', 'in', $departments];
        $user = self::where($where)->order('')->find();

        return $user;
    }

    public static function updateCache($id)
    {
        self::getUser($id, true);
        self::getUserAuthSet($id, true);
        self::getUsers(true);
    }

    public static function updateUserList()
    {
        self::getUsers(true);
    }
}