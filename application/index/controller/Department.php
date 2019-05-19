<?php
namespace app\index\controller;

use think\facade\Request;

class Department extends Base
{
    public function index()
    {
        // $map[] = ['delete_time', '=', '0'];
        // $map[] = ['parent_id', '=', 0];
        $parents = model("department")->order('path asc')->select()->toArray();
        // print_r($parents);
        $this->assign('list', $parents);
//        foreach ($parents as $key=>&$department) {
//            $where = [];
//            $where[] = ['parent_id', '=', $department['id']];
//            $department['nodes'] = model('department')->where($where)->order('path,sort desc')->select()->toArray();
//        }

       //  print_r($parents);

        return $this->fetch();
    }

    public function addDepartment()
    {
        $departments = \app\index\model\Department::getDepartments();
        $this->assign('departments', $departments);

        $this->view->engine->layout(false);
        return $this->fetch('edit_department');
    }

    public function editDepartment()
    {
        $departments = \app\index\model\Department::getDepartments();
        $this->assign('departments', $departments);

        $get = Request::param();
        $data = \app\index\model\Department::get($get['id']);
        $this->assign('data', $data);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditDepartment()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑部门';
            $Model = \app\index\model\Department::get($post['id']);
        } else {
            $action = '添加部门';
            $Model = new \app\index\model\Department();
        }

        $result = $Model->save($post);
        if($result) {

            empty($post['id']) && $post['id'] = $Model->id;
            if($post['parent_id']) {
                $parent = \app\index\model\Department::get($post['parent_id']);
                $path = $parent['path'] . '-' . $post['id'];
                $depth = substr_count($path, '-');
            } else {
                $path = '0'.'-'.$post['id'];
                $depth = 1;
            }
            $Model->save(['path' => $path, 'depth' => $depth]);

            // \app\index\model\Source::updateCache();
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function delete()
    {
        $get = Request::param();
        $result = \app\index\model\Brand::get($get['id'])->delete();

        if($result) {
            // 更新缓存
            // \app\index\model\Brand::updateCache();
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}