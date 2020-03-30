<?php
namespace app\index\controller;

use think\facade\Request;

class Ritual extends Base
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('ritual')->where($map)->order('is_valid desc,sort asc,id asc')->paginate($get['limit'], false, $config);
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

    public function addRitual()
    {
        $this->view->engine->layout(false);
        return $this->fetch('edit_Ritual');
    }

    public function editRitual()
    {
        $get = Request::param();
        $Ritual = \app\common\model\Ritual::get($get['id']);
        $this->assign('data', $Ritual);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditRitual()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '更新仪式';
            $Model = \app\common\model\Ritual::get($post['id']);
        } else {
            $action = '添加仪式';
            $Model = new \app\common\model\Ritual();
        }

        // $Model::create($post);
        $result = $Model->save($post);
        if($result) {
            ### 更新缓存
            \app\common\model\Ritual::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteRitual()
    {
        $post = Request::post();
        $Model = \app\common\model\Ritual::get($post['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\Ritual::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}