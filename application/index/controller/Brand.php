<?php
namespace app\index\controller;

use think\facade\Request;

class Brand extends Base
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('brand')->where($map)->order('is_valid desc,sort desc,id asc')->paginate($get['limit'], false, $config);
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

    public function addBrand()
    {
        $this->view->engine->layout(false);
        return $this->fetch('edit_brand');
    }

    public function editBrand()
    {
        $get = Request::param();
        $brand = \app\index\model\Brand::get($get['id']);
        $this->assign('data', $brand);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditBrand()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '更新品牌';
            $Model = \app\index\model\Brand::get($post['id']);
        } else {
            $action = '添加品牌';
            $Model = new \app\index\model\Brand();
        }

        // $Model::create($post);
        $result = $Model->save($post);
        if($result) {
            ### 更新缓存
            \app\index\model\Brand::updateCache();

            ### 添加操作日志
            \app\index\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteBrand()
    {
        $post = Request::post();
        $Model = \app\index\model\Brand::get($post['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\index\model\Brand::updateCache();

            ### 添加操作日志
            \app\index\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}