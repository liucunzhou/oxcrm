<?php
namespace app\index\controller;

use app\common\model\MemberAllocate;
use app\http\Swoole;
use app\common\model\AuthGroup;
use app\common\model\Department;
use app\common\model\UserAuth;
use think\facade\Request;
use think\Session;
use app\common\model\Region;

class User extends Base
{
    public function index()
    {
        if(Request::isAjax()) {
            // 获取权限、部门列表
            $roles = AuthGroup::getRoles();
            $departments = Department::getDepartments();

            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];

            if (isset($get['id']) && !empty($get['id'])) {
                $get['id'] = trim($get['id']);
                $map[] = ['id', '=', $get['id']];
            }

            if (isset($get['role_id']) && !empty($get['role_id'])) {
                $get['role_id'] = trim($get['role_id']);
                $map[] = ['role_id', '=', $get['role_id']];
            }

            if (isset($get['nickname']) && !empty($get['nickname'])) {
                $get['nickname'] = trim($get['nickname']);
                $map[] = ['nickname', 'like', "%{$get['nickname']}%"];
            }

            if (isset($get['realname']) && !empty($get['realname'])) {
                $get['realname'] = trim($get['realname']);
                $map[] = ['realname', 'like', "%{$get['realname']}%"];
            }

            if (isset($get['mobile']) && !empty($get['mobile'])) {
                $get['mobile'] = trim($get['mobile']);
                $map[] = ['mobile', 'like', "%{$get['mobile']}%"];
            }

            if (isset($get['department_id']) && $get['department_id'] > 0) {
                $departmentIds = Department::getTree($get['department_id']);
                $map[] = ['department_id', 'in', $departmentIds];
            }

            $list = model('user')->where($map)->order('is_valid desc,sort desc,id asc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            foreach ($data as &$value) {
                $departmentId = $value['department_id'];
                $value['department_id'] = $departments[$departmentId]['title'];
                $value['is_valid'] = $value['is_valid'] ? '在线' : '下线';
                $value['role_id'] = $roles[$value['role_id']]['title'];
            }

            $result = [
                'code'  => 0,
                'msg'   => '获取数据成功',
                'count' => $list->total(),
                'data'  => $list->getCollection()
            ];
            return json($result);
        } else {
            $roles = \app\common\model\AuthGroup::getRoles();
            $this->assign('roles', $roles);

            ### 获取部门分组
            $departments = \app\common\model\Department::getDepartments();
            $this->assign('departments', $departments);
            return $this->fetch();
        }
    }

    public function addUser()
    {
        $provinces = Region::getProvinceList();
        $this->assign('provinces', $provinces);

        return $this->fetch('edit_user');
    }

    public function editUser()
    {
        $get = Request::param();
        $data = \app\common\model\User::get($get['id']);
        $this->assign('data', $data);

        $provinces = Region::getProvinceList();
        $this->assign('provinces', $provinces);

        $cities = [];
        if($data['province_id']) {
            $cities = Region::getCityList($data['province_id']);
        }
        $this->assign('cities', $cities);

        return $this->fetch();
    }

    public function doEditUser()
    {
        $post = Request::post();
        $post['nickname'] = trim($post['nickname']);
        $post['realname'] = trim($post['realname']);
        $post['mobile'] = trim($post['mobile']);
        $post['email'] = trim($post['email']);
        if($post['id']) {
            // $user = \app\common\model\User::getByNickname($post['nickname']);
            $action = '编辑用户';
            $Model = \app\common\model\User::get($post['id']);
            if(empty($Model->user_no)) {
                $userNo = microtime().rand(100000,1000000);
                $Model->user_no = md5($userNo);
            }

        } else {

            $action = '添加用户';
            $Model = new \app\common\model\User();
            $userNo = microtime().rand(100000,1000000);
            $Model->user_no = md5($userNo);
        }

        if($post['password']) {
            $post['password'] = md5($post['password']);
        } else {
            unset($post['password']);
        }
        $result = $Model->save($post);

        if($result) {
            if(empty($post['id'])) {
                $post['id'] = $Model->id;
            }  else {
                UserAuth::updateCache($post['id']);
            }
            ### 更新用户信息缓存
            $Model::updateCache($post['id']);

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功', 'redirect'=>$post['referer']]);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function editAuth()
    {
        $get = Request::param();
        $data = UserAuth::getUserLogicAuth($get['id']);
        !empty($data['role_ids']) && $data['role_ids'] = explode(',', $data['role_ids']);
        !empty($data['store_ids']) && $data['store_ids'] = explode(',', $data['store_ids']);
        !empty($data['source_ids']) && $data['source_ids'] = explode(',', $data['source_ids']);
        $this->assign('data', $data);

        ### 获取部门分组
        $departments = \app\common\model\Department::getDepartments();
        $this->assign('departments', $departments);

        ### 获取所有角色
        $roles = AuthGroup::getRoles();
        $this->assign('roles', $roles);

        ### 按品牌进行分组的门店
        $brands = \app\common\model\Store::getStoresGroupByBrand();
        $this->assign('brands', $brands);

        ### 按平台进行分组的门店
        $platforms = \app\common\model\Source::getSouresGroupByPlatform();
        $this->assign('platforms', $platforms);

        return $this->fetch();
    }

    public function doEditAuth()
    {
        $post = Request::post();

        $Model = new \app\common\model\UserAuth();
        $where[] = ['user_id','=',$post['user_id']];
        $newObj = $Model->where($where)->find();
        !empty($newObj) && $Model = $newObj;

        $Model->user_id = $post['user_id'];
        $Model->department_id = $post['department_id'];
        !empty($post['role']) && $Model->role_ids = $post['role'];
        !empty($post['store']) && $Model->store_ids = implode(',', $post['store']);
        !empty($post['source']) && $Model->source_ids = implode(',', $post['source']);
        $Model->show_visit_log = $post['show_visit_log'];
        $Model->is_show_entire_mobile = $post['is_show_entire_mobile'];
        $result = $Model->save($post);

        if($result) {
            ### 更新用户信息缓存
            $user = \app\common\model\User::get($post['user_id']);
            $user->department_id = $post['department_id'];
            $user->role_id = $post['role'];
            $user->save();
            ### 更新该用户的权限缓存
            $Model::updateCache($post['user_id']);
            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> '用户权限成功', 'redirect'=>$post['referer']]);
        } else {
            return json(['code'=>'500', 'msg'=> '用户权限失败']);
        }
    }

    public function deleteUser()
    {
        $get = Request::param();
        $Model = \app\common\model\User::get($get['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\User::updateUserList();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }

    public function info()
    {

        // $user1 = session('user');
        $user = \think\facade\Session::get("user");

        $roles = AuthGroup::getRoles();
        $this->assign('role', $roles[$user['role_id']]);
        $this->assign("user", $user);
        return $this->fetch();
    }

    public function repassword()
    {
        return $this->fetch();
    }

    public function doRepassword()
    {
        $post = Request::post();
        $user = \think\facade\Session::get("user");
        $user = \app\common\model\User::get($user['id']);
        $post['password'] = md5($post['password']);
        if ($user['password'] != $post['password']) {
            return json(['code'=>'500', 'msg'=>'请输入原密码']);
        }
        $user->password = md5($post['newpassword']);
        $result = $user->save();

        if($result) {
            return json(['code'=>'200', 'msg'=>'修改密码成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'修改密码失败']);
        }
    }

    public function assignDepartmentToStaff()
    {
        $post = Request::param();
        if(empty($post['department_id'])) {
            return json(['code'=>'500', 'msg'=>'请选择部门']);
        }

        if(empty($post['ids'])) {
            return json(['code'=>'500', 'msg'=>'请选择员工']);
        }

        $ids = explode(',',  $post['ids']);
        foreach($ids as $key=>$val) {
            $map = [];
            $map[] = ['user_id', '=', $val];
            $Auth = UserAuth::where($map)->find();
            if(!empty($Auth)) {
                $Auth->department_id = $post['department_id'];
                $Auth->save();
            } else {
                $data = [];
                $data['user_id'] = $val;
                $data['department_id'] = $post['department_id'];
                $data['create_time'] = time();
                $Auth = new UserAuth();
                $Auth->insert($data);
            }

            // 同步到用户信息
            $user = \app\common\model\User::get($val);
            $user->save(['department_id'=>$post['department_id']]);
        }

        return json(['code'=>'200', 'msg'=>'设置用户部门成功']);
    }

    public function assignRoleToStaff()
    {
        $post = Request::param();
        if(empty($post['role_id'])) {
            return json(['code'=>'500', 'msg'=>'请选择角色']);
        }

        if(empty($post['ids'])) {
            return json(['code'=>'500', 'msg'=>'请选择员工']);
        }

        $ids = explode(',',  $post['ids']);
        foreach($ids as $key=>$val) {
            $map = [];
            $map[] = ['user_id', '=', $val];
            $Auth = UserAuth::where($map)->find();
            if(!empty($Auth)) {
                $Auth->role_ids = $post['role_id'];
                $Auth->save();
            } else {
                $data = [];
                $data['user_id'] = $val;
                $data['role_ids'] = $post['role_id'];
                $data['create_time'] = time();
                $Auth = new UserAuth();
                $Auth->insert($data);
            }

            // 同步到用户信息
            $user = \app\common\model\User::get($val);
            $user->save(['role_id' => $post['role_id']]);

            // 更新用户缓存
            \app\common\model\User::updateCache($val);
            UserAuth::updateCache($val);
        }

        return json(['code'=>'200', 'msg'=>'设置用户部门成功']);
    }

    public function merge()
    {
        $post = Request::param();
        $ids = explode(',', $post['ids']);

        $map[] = ['id', 'in', $ids];
        $userNos = \app\common\model\User::where($map)->column("user_no");
        $userNoStr = implode(',', $userNos);
        $UserModel = new \app\common\model\User();
        $result = $UserModel->save(['user_nos'=>$userNoStr,'ids'=>$post['ids']], $map);
        if($result) {

            foreach ($ids as $id) {
                \app\common\model\User::updateCache($id);
            }

            return json(['code'=>'200', 'msg'=>'合并成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'合并失败']);
        }
    }

    public function leave()
    {
        $staffs = \app\common\model\User::getUsers();
        $this->assign('staffs', $staffs);

        return $this->fetch();
    }

    public function doLeave()
    {
        $request = Request::param();
        if($request['id'] < 1) {
            return json(['code'=>'500', 'msg'=>'请选择要分配的员工']);
        }

        if($request['staff'] < 1) {
            return json(['code'=>'500', 'msg'=>'请选择要分配给的员工']);
        }

        $where = [];
        $where[] = ['user_id', '=', $request['id']];
        $allocates = MemberAllocate::where($where)->select();

        $totals = 0;
        foreach ($allocates as $allocate) {
            $data = $allocate->getData();
            $rs = MemberAllocate::updateAllocateData($request['staff'], $allocate->member_id, $data);
            if ($rs) {
                $totals = $totals + 1;
            }
        }

        if ($totals > 0) {
            $result = true;
        } else {
            $result = false;
        }

        if($result) {
            return json(['code'=>'200', 'msg'=>"离职员工客资分配成功,{$totals}个"]);
        } else {
            return json(['code'=>'500', 'msg'=>'离职员工客资分配失败']);
        }
    }
}