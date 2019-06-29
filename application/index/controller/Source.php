<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/4/22
 * Time: 8:47 PM
 */

namespace app\index\controller;


use think\facade\Request;

class Source extends Base
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            ### 获取平台列表
            $platforms = \app\index\model\Source::getPlatforms();
            $map[] = ['parent_id', '>', 0];
            $config = [
                'page' => $get['page']
            ];
            $list = model('source')->where($map)->order('parent_id asc,sort desc,id asc')->paginate($get['limit'], false, $config);
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
    }

    public function addSource()
    {
        $platforms = \app\index\model\Source::getPlatforms();
        $this->assign('platforms', $platforms);
        // print_r($platforms);

        $this->view->engine->layout(false);
        return $this->fetch('edit_source');
    }

    public function editSource()
    {
        $platforms = \app\index\model\Source::getPlatforms();
        $this->assign('platforms', $platforms);
        // print_r($platforms);

        $get = Request::param();
        $data = \app\index\model\Source::get($get['id']);
        $this->assign('data', $data);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditSource()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '添加来源';
            $Model = \app\index\model\Source::get($post['id']);
        } else {
            $action = '编辑来源';
            $Model = new \app\index\model\Source();
        }

        // 缺少字段验证
        $result = $Model->save($post);

        if($result) {
            \app\index\model\Source::updateCache();
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function delete()
    {
        $get = Request::param();
        $result = \app\index\model\Source::get($get['id'])->delete();

        if($result) {
            // 更新缓存
            \app\index\model\Source::updateCache();
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}