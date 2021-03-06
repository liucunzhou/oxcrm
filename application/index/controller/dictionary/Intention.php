<?php
namespace app\index\controller\dictionary;


use app\common\model\AuthGroup;
use app\index\controller\Backend;
use think\facade\Request;

class Intention extends Backend
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('intention')->where($map)->order('sort asc,id asc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            foreach ($data as &$value) {
                $value['is_valid'] = $value['is_valid'] ? '在线' : '下线';
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

    public function addIntention()
    {
        $this->view->engine->layout(false);
        return $this->fetch('edit_intention');
    }

    public function editIntention()
    {
        $get = Request::param();
        $data = \app\common\model\Intention::get($get['id']);
        $this->assign('data', $data);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditIntention()
    {
        $post = Request::post();
        if($post['id']) {
            $Model = \app\common\model\Intention::get($post['id']);
        } else {
            $Model = new \app\common\model\Intention();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            $Model::getIntentions(true);
            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> '添加成功']);
        } else {
            return json(['code'=>'500', 'msg'=> '添加失败']);
        }
    }

    public function deleteIntention()
    {
        $get = Request::param();
        $Model = \app\common\model\Intention::get($get['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\Intention::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}