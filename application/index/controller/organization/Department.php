<?php
namespace app\index\controller\organization;

use app\index\controller\Backend;
use think\facade\Request;

class Department extends Backend
{
    public function index()
    {
        $get = Request::param();
        $config = [
            'page' => $get['page']
        ];
        $get['limit'] = $get['limit'] ? $get['limit'] : 100;

        $map = [];
        $map[] = ['parent_id', '=', 1];
        $result = model('department')->where($map)->order('path,sort')->select();
        foreach ($result as &$row) {
            $users = \app\common\model\User::getUsersByDepartmentId($row->id);
            $row['staff_amount'] = count($users);
        }
        $this->assign('list', $result);

        return $this->fetch();
    }

    public function subdepartments()
    {
        $param = $this->request->param();

        $map = [];
        $map[] = ['parent_id', '=', $param['id']];
        $result = model('department')->where($map)->order('path,sort')->select();
        foreach ($result as &$row) {
            $users = \app\common\model\User::getUsersByDepartmentId($row->id);
            $row['staff_amount'] = count($users);
        }
        $this->assign('list', $result);

        $department = \app\common\model\Department::getDepartment($param['id']);
        $breadcrumb = [];
        $path = explode('-', $department->path);
        foreach ($path as $row) {
            if($row > 0) {
                $breadcrumb[] = \app\common\model\Department::getDepartment($row);
            }
        }
        $this->assign('breadcrumb', $breadcrumb);

        $users = \app\common\model\User::getUsersInfoByDepartmentId($param['id'], false);
        $this->assign('users', $users);
        return $this->fetch();
    }

    public function addDepartment()
    {
        $param = $this->request->param();
        $this->assign('param', $param);

        $departments = \app\common\model\Department::getDepartments();
        $this->assign('departments', $departments);

        $this->view->engine->layout(false);
        return $this->fetch('create');
    }

    public function editDepartment()
    {
        $departments = \app\common\model\Department::getDepartments();
        $this->assign('departments', $departments);

        $get = Request::param();
        $data = \app\common\model\Department::get($get['id']);
        $this->assign('data', $data);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditDepartment()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑部门';
            $Model = \app\common\model\Department::get($post['id']);
        } else {
            $action = '添加部门';
            $Model = new \app\common\model\Department();
        }

        $result = $Model->save($post);
        if($result) {

            empty($post['id']) && $post['id'] = $Model->id;
            if($post['parent_id']) {
                $parent = \app\common\model\Department::get($post['parent_id']);
                $path = $parent['path'] . $post['id'].'-';
                $depth = substr_count($path, '-');
            } else {
                $path = '0'.'-'.$post['id'].'-';
                $depth = 1;
            }
            $Model->save(['path' => $path, 'depth' => $depth]);
            // 更新所有孩子节点
            // $children = $Model::getTree($post['id']);


            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            // \app\common\model\Source::updateCache();
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteDepartment()
    {
        $get = Request::param();
        $Model = \app\common\model\Department::get($get['id']);

        $where = [];
        $where[] = ['parent_id', '=', $get['id']];
        $children = \app\common\model\Department::where($where)->find();
        if (empty($children)) {
            $result = $Model->delete();
        } else {
            return json(['code'=>'500', 'msg'=>'请先删除子部门']);
        }

        if($result) {
            // 更新缓存
            // \app\common\model\Brand::updateCache();
            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}