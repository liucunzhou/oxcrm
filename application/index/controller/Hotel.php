<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/4/22
 * Time: 8:45 PM
 */

namespace app\index\controller;

use think\facade\Request;

class Hotel extends Base
{
    public function index()
    {
        // $map[] = ['delete_time', '=', '0'];
        $map = [];
        $list = model('hotel')->where($map)->order('is_valid desc,sort desc,id asc')->paginate(10);
        // echo model('brand')->getLastSql();
        $this->assign('list', $list);

        return $this->fetch();
    }

    public function addHotel()
    {
        $this->view->engine->layout(false);
        return $this->fetch('edit_brand');
    }

    public function editHotel()
    {
        $get = Request::param();
        $brand = \app\index\model\Brand::get($get['id']);
        $this->assign('data', $brand);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditHotel()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '添加酒店';
            $Model = \app\index\model\Hotel::get($post['id']);
        } else {
            $action = '编辑酒店';
            $Model = new \app\index\model\Hotel();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            ### 更新缓存
            \app\index\model\Hotel::updateCache();
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function delete()
    {
        $get = Request::param();
        $result = \app\index\model\Hotel::get($get['id'])->delete();

        if($result) {
            // 更新缓存
            \app\index\model\Hotel::updateCache();
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}