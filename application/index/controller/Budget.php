<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/4/22
 * Time: 8:45 PM
 */

namespace app\index\controller;

use think\facade\Request;

// 预算
class Budget extends Base
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
            $list = model('budget')->field($fields)->where($map)->order('is_valid desc,sort desc,id asc')->paginate($get['limit'], false, $config);
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

    public function addBudget()
    {
        return $this->fetch('edit_budget');
    }

    public function editBudget()
    {
        $get = Request::param();
        $brand = \app\common\model\Budget::get($get['id']);
        $this->assign('data', $brand);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditBudget()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑预算';
            if(isset($post['create_time'])) unset($post['create_time']);
            if(isset($post['modify_time'])) unset($post['modify_time']);
            $Model = \app\common\model\Budget::get($post['id']);
        } else {
            $action = '添加预算';
            $Model = new \app\common\model\Budget();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            ### 更新缓存
            \app\common\model\Budget::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteBudget()
    {
        $get = Request::param();
        $Model = \app\common\model\Budget::get($get['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\Budget::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}