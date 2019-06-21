<?php
namespace app\index\controller;

use app\http\Swoole;
use app\index\model\AuthGroup;
use app\index\model\Department;
use app\index\model\UserAuth;
use think\facade\Request;

class User extends Base
{
    public function index()
    {
        // echo "<pre>";
        $User = new \app\index\model\User();
        $list = $User->with('userAuth')->order('sort desc')->paginate(15);
        $this->assign('list', $list);
        // print_r($list);

        ### 获取角色列表
        $AuthGroup = new AuthGroup();
        $roles = $AuthGroup::getRoles();
        $this->assign('roles', $roles);
        $this->assign('AuthGroup', $AuthGroup);

        ### 获取部门列表
        $departments = Department::getDepartments();
        $this->assign('departments', $departments);

        return $this->fetch();
    }

    public function editUser()
    {
        $get = Request::param();
        $data = \app\index\model\User::get($get['id']);
        $this->assign('data', $data);

        return $this->fetch();
    }

    public function doEditUser()
    {

        $post = Request::post();
        if($post['id']) {
            $action = '编辑用户';
            $Model = \app\index\model\User::get($post['id']);
        } else {
            $action = '添加用户';
            $Model = new \app\index\model\User();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            empty($post['id']) && $post['id'] = $Model->id;
            ### 更新用户信息缓存
            $Model::updateCache($post['id']);
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
        $departments = \app\index\model\Department::getDepartments();
        $this->assign('departments', $departments);

        ### 获取所有角色
        $roles = AuthGroup::getRoles();
        $this->assign('roles', $roles);

        ### 按品牌进行分组的门店
        $brands = \app\index\model\Store::getStoresGroupByBrand();
        $this->assign('brands', $brands);

        ### 按平台进行分组的门店
        $platforms = \app\index\model\Source::getSouresGroupByPlatform();
        $this->assign('platforms', $platforms);

        return $this->fetch();
    }

    public function doEditAuth()
    {
        $post = Request::post();

        $Model = new \app\index\model\UserAuth();

        $where[] = ['user_id','=',$post['user_id']];
        $newObj = $Model->where($where)->find();
        !empty($newObj) && $Model = $newObj;

        $Model->user_id = $post['user_id'];
        $Model->department_id = $post['department_id'];
        !empty($post['role']) && $Model->role_ids = implode(',', $post['role']);
        !empty($post['store']) && $Model->store_ids = implode(',', $post['store']);
        !empty($post['source']) && $Model->source_ids = implode(',', $post['source']);
        $result = $Model->save();

        if($result) {
            ### 更新用户信息缓存
            $Model::updateCache($post['user_id']);
            return json(['code'=>'200', 'msg'=> '用户权限成功', 'redirect'=>$post['referer']]);
        } else {
            return json(['code'=>'500', 'msg'=> '用户权限失败']);
        }
    }

    public function delete()
    {
        $get = Request::param();
        $result = \app\index\model\User::get($get['id'])->delete();

        if($result) {
            // 更新缓存
            \app\index\model\User::updateCache();
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }

    public function info()
    {

        return $this->fetch();
    }

    public function repassword()
    {
        return $this->fetch();
    }

    public function doRepassword()
    {

    }

    public function sendMessage()
    {
        $Swoole = new Swoole();
        $Swoole->sendMessage();

    }
}