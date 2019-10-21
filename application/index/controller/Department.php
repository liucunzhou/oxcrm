<?php
namespace app\index\controller;

use think\facade\Request;

class Department extends Base
{
    /**
     * 编辑、授权、删除
     * @return mixed|\think\response\Json
     */
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('department')->where($map)->order('path asc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            foreach ($data as $value) {
                if($value['depth'] > 1) {
                    $value['title'] = str_repeat('─',$value['depth'] - 1).$value['title'];
                }
            }

            $result = [
                'code'  => 0,
                'msg'   => '获取数据成功',
                'count' => $list->total(),
                'data'  => $data
            ];
            return json($result);
        } else {
            return $this->fetch();
        }
    }

    public function addDepartment()
    {
        $departments = \app\common\model\Department::getDepartments();
        $this->assign('departments', $departments);

        $this->view->engine->layout(false);
        return $this->fetch('edit_department');
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
        $result = $Model->delete();

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