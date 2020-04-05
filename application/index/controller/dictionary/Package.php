<?php
namespace app\index\controller\dictionary;

use app\index\controller\Backend;
use think\facade\Request;

class Package extends Backend
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('package')->where($map)->order('is_valid desc,sort asc,id asc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            foreach ($data as &$value){
                $value['is_valid'] = $value['is_valid'] ? '在线' : '下线';
            }
            $result = [
                'code'  => 0,
                'msg'   => '获取数据成功',
                'count' => $list->total(),
                'data'  => $data
            ];
            return json($result);
        } else {
            $this->view->engine->layout(false);
            return $this->fetch();
        }
    }

    public function addPackage()
    {
        $this->view->engine->layout(false);
        return $this->fetch('edit_package');
    }

    public function editPackage()
    {
        $get = Request::param();
        $Package = \app\common\model\Package::get($get['id']);
        $this->assign('data', $Package);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditPackage()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '更新套餐';
            $Model = \app\common\model\Package::get($post['id']);
        } else {
            $action = '添加套餐';
            $Model = new \app\common\model\Package();
        }

        // $Model::create($post);
        $result = $Model->save($post);
        if($result) {
            ### 更新缓存
            \app\common\model\Package::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deletePackage()
    {
        $post = Request::post();
        $Model = \app\common\model\Package::get($post['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\Package::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}