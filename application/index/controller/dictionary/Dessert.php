<?php
namespace app\index\controller\dictionary;

use app\index\controller\Backend;
use think\facade\Request;

class Dessert extends Backend
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('Dessert')->where($map)->order('is_valid desc,sort desc,id asc')->paginate($get['limit'], false, $config);
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

    public function addDessert()
    {
        $this->view->engine->layout(false);
        return $this->fetch('edit');
    }

    public function editDessert()
    {
        $get = Request::param();
        $Dessert = \app\common\model\Dessert::get($get['id']);
        $this->assign('data', $Dessert);

        $this->view->engine->layout(false);
        return $this->fetch('edit');
    }

    public function doEditDessert()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '更新信息';
            $Model = \app\common\model\Dessert::get($post['id']);
        } else {
            $action = '添加信息';
            $Model = new \app\common\model\Dessert();
        }

        // $Model::create($post);
        $result = $Model->save($post);
        if($result) {
            ### 更新缓存
            \app\common\model\Dessert::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteDessert()
    {
        $post = Request::post();
        $Model = \app\common\model\Dessert::get($post['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\Dessert::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}