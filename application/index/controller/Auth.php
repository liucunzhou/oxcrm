<?php
namespace app\index\controller;

use app\index\model\AuthGroup;
use think\facade\Request;

class Auth extends Base
{
    public function index()
    {
        $modules = \app\index\model\Auth::getModules();
        $this->assign('modules', $modules);

        $items = \app\index\model\Auth::getList();
        $this->assign('items', $items);

        return $this->fetch();
    }

    public function addAuth()
    {
        $modules = \app\index\model\Auth::getModules();
        $this->assign('modules', $modules);

        $this->view->engine->layout(false);
        return $this->fetch('edit_auth');
    }

    public function editAuth()
    {
        $modules = \app\index\model\Auth::getModules();
        $this->assign('modules', $modules);

        $get = Request::param();
        $brand = \app\index\model\Auth::get($get['id']);
        $this->assign('data', $brand);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditAuth()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑权限';
            $Model = \app\index\model\Auth::get($post['id']);
        } else {
            $action = '添加权限';
            $Model = new \app\index\model\Auth();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            empty($post['id']) && $post['id'] = $Model->id;
            ### 更新缓存
            \app\index\model\Auth::updateCache($post['id']);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function delete()
    {
        $get = Request::param();
        $result = \app\index\model\Auth::get($get['id'])->delete();

        if($result) {
            // 更新缓存
            \app\index\model\Auth::updateCache($get['id']);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }


    public function group()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('AuthGroup')->where($map)->order('is_valid desc,sort desc,id asc')->paginate($get['limit'], false, $config);
            $result = [
                'code'  => 0,
                'msg'   => '获取数据成功',
                'count' => $list->total(),
                'data'  => $list->getCollection()
            ];
            return json($result);
        } else {
            $this->view->engine->layout(false);
            return $this->fetch();
        }
        return $this->fetch();
    }

    public function addGroup()
    {

        $this->view->engine->layout(false);
        return $this->fetch('edit_group');
    }

    public function editGroup()
    {
        $get = Request::param();
        $brand = \app\index\model\AuthGroup::get($get['id']);
        $this->assign('data', $brand);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditGroup()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑权限分组';
            $Model = \app\index\model\AuthGroup::get($post['id']);
        } else {
            $action = '添加权限分组';
            $Model = new \app\index\model\AuthGroup();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            empty($post['id']) && $post['id'] = $Model->getLastInsID();
            ### 更新缓存
            \app\index\model\AuthGroup::updateCache($post['id']);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function assignAuth()
    {
        $get = Request::param();
        $data = \app\index\model\AuthGroup::getAuthGroup($get['id']);
        $this->assign('data', $data);

        $authSelected = explode(',', $data['auth_set']);
        $this->assign('authSelected', $authSelected);

        $modules = \app\index\model\Auth::getModules();
        $this->assign('modules', $modules);

        $items = \app\index\model\Auth::getList();
        $this->assign('items', $items);
        return $this->fetch();
    }

    public function doAssignAuth()
    {
        $post = Request::post();
        if(empty($post['id'])) {
            return json([
                'code'  => '400',
                'msg'   => 'id不能为空'
            ]);
        }

        if(empty($post['ids'])) {
            return json([
                'code'  => '400',
                'msg'   => '要分配的权限不能为空'
            ]);
        }

        $result = AuthGroup::get($post['id'])->save(['auth_set'=>$post['ids']]);

        if($result) {
            AuthGroup::updateCache($post['id']);
            return json([
                'code'  => '200',
                'msg'   => '权限分配成功'
            ]);
        } else {
            return json([
                'code'  => '500',
                'msg'   => '权限分配失败'
            ]);
        }
    }

    public function deleteGroup()
    {
        $get = Request::param();
        $result = \app\index\model\AuthGroup::get($get['id'])->delete();

        if($result) {
            // 更新缓存
            \app\index\model\AuthGroup::updateCache($get['id']);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}