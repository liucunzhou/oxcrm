<?php
namespace app\index\controller\dictionary;

use app\index\controller\Backend;
use think\facade\Request;

// 桌数
class Scale extends Backend
{
    public function index()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $fields = 'id,title,sort,create_time';
            $list = model('scale')->field($fields)->where($map)->order('is_valid desc,sort desc,id asc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();

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

    public function addScale()
    {
        $this->view->engine->layout(false);
        return $this->fetch('edit_scale');
    }

    public function editScale()
    {
        $get = Request::param();
        $brand = \app\common\model\Scale::get($get['id']);
        $this->assign('data', $brand);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditScale()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑桌数';
            if(isset($post['create_time'])) unset($post['create_time']);
            if(isset($post['modify_time'])) unset($post['modify_time']);
            $Model = \app\common\model\Scale::get($post['id']);
        } else {
            $action = '添加桌数';
            $Model = new \app\common\model\Scale();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            ### 更新缓存
            \app\common\model\Scale::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteScale()
    {
        $get = Request::param();
        $Model = \app\common\model\Scale::get($get['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\Scale::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}