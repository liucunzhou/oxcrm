<?php
namespace app\index\controller;

use app\http\Swoole;
use app\index\model\AuthGroup;
use app\index\model\Department;
use app\index\model\UserAuth;
use think\facade\Request;
use think\Session;

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
            $list = model('user')->where($map)->order('is_valid desc,sort desc,id asc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            foreach ($data as &$value) {
                $value['department_id'] = $departments[$value['department_id']]['title'];
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
            return $this->fetch();
        }
    }

    public function addUser()
    {
        return $this->fetch('edit_user');
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

        if($post['password']) {
            $post['password'] = md5($post['password']);
        } else {
            unset($post['password']);
        }

        $result = $Model->save($post);

        if($result) {
            empty($post['id']) && $post['id'] = $Model->id;
            ### 更新用户信息缓存
            $Model::updateCache($post['id']);

            ### 添加操作日志
            \app\index\model\OperateLog::appendTo($Model);
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
        !empty($post['role']) && $Model->role_ids = $post['role'];
        !empty($post['store']) && $Model->store_ids = implode(',', $post['store']);
        !empty($post['source']) && $Model->source_ids = implode(',', $post['source']);
        $Model->show_visit_log = $post['show_visit_log'];
        $Model->is_show_entire_mobile = $post['is_show_entire_mobile'];
        $result = $Model->save();

        if($result) {
            ### 更新用户信息缓存
            $user = \app\index\model\User::get($post['user_id']);
            $user->department_id = $post['department_id'];
            $user->role_id = $post['role'];
            $user->save();
            ### 更新该用户的权限缓存
            $Model::updateCache($post['user_id']);
            ### 添加操作日志
            \app\index\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> '用户权限成功', 'redirect'=>$post['referer']]);
        } else {
            return json(['code'=>'500', 'msg'=> '用户权限失败']);
        }
    }

    public function deleteUser()
    {
        $get = Request::param();
        $Model = \app\index\model\User::get($get['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\index\model\User::updateCache();

            ### 添加操作日志
            \app\index\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }

    public function info()
    {

        $user = \think\facade\Session::get("user");
        $auth = UserAuth::getUserLogicAuth($user['id']);
        $roles = AuthGroup::getRoles();
        $this->assign('role', $roles[$auth['role_ids']]);
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
        $user = \app\index\model\User::get($user['id']);
        $post['password'] = md5($post['password']);
        if ($user['password'] != $post['password']) {
            return json(['code'=>'500', 'msg'=>'初始密码不正确']);
        }
        $user->password = md5($post['newpassword']);
        $result = $user->save();

        if($result) {
            return json(['code'=>'200', 'msg'=>'修改密码成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'修改密码失败']);
        }
    }

    public function sendMessage()
    {
        $Swoole = new Swoole();
        $Swoole->sendMessage();

    }
}