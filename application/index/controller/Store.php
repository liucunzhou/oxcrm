<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/4/22
 * Time: 8:46 PM
 */

namespace app\index\controller;


use app\index\model\Brand;
use think\facade\Request;

class Store extends Base
{
    public function index()
    {
        if(Request::isAjax()) {
            $brands = Brand::getBrands();
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('store')->where($map)->order('brand_id,is_valid desc,sort desc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            foreach ($data as &$value) {
                $value['brand_id'] = $brands[$value['brand_id']]['title'];
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

    public function addStore()
    {
        ### 获取品牌列表
        $brands = Brand::getBrands();
        $this->assign('brands', $brands);

        $this->view->engine->layout(false);
        return $this->fetch('edit_store');
    }

    public function editStore()
    {
        ### 获取品牌列表
        $brands = Brand::getBrands();
        $this->assign('brands', $brands);

        ### 获取门店详情
        $get = Request::param();
        $data = \app\index\model\Store::get($get['id']);
        $this->assign('data', $data);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditStore()
    {
        $post = Request::post();
        if($post['id']) {
            $Model = \app\index\model\Store::get($post['id']);
        } else {
            $Model = new \app\index\model\Store();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            empty($post['id']) && $post['id'] = $Model->id;
            \app\index\model\Store::updateCache($post['id']);
            return json(['code'=>'200', 'msg'=> '添加成功']);
        } else {
            return json(['code'=>'500', 'msg'=> '添加失败']);
        }
    }

    public function delete()
    {
        $get = Request::param();
        $result = \app\index\model\Store::get($get['id'])->delete();

        if($result) {
            // 更新缓存
            \app\index\model\Store::updateCache($get['id']);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}