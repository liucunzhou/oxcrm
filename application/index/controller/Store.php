<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/4/22
 * Time: 8:46 PM
 */

namespace app\index\controller;


use app\common\model\Brand;
use app\common\model\Region;
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

            if (isset($get['title'])) {
                $map[] = ['title', 'like', "%{$get['title']}%"];
            }

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
        $data = \app\common\model\Store::get($get['id']);
        $this->assign('data', $data);

        $provinces = Region::getProvinceList();
        $this->assign('provinces', $provinces);

        $cities = [];
        if($data['province_id']) {
            $cities = Region::getCityList($data['province_id']);
        }
        $this->assign('cities', $cities);

        $areas = [];
        if($data['city_id']) {
            $areas = Region::getAreaList($data['city_id']);
        }
        $this->assign('areas', $areas);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditStore()
    {
        $post = Request::post();
        if($post['id']) {
            $Model = \app\common\model\Store::get($post['id']);
        } else {
            $Model = new \app\common\model\Store();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            empty($post['id']) && $post['id'] = $Model->id;
            \app\common\model\Store::updateCache($post['id']);

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> '添加成功']);
        } else {
            return json(['code'=>'500', 'msg'=> '添加失败']);
        }
    }

    public function deleteStore()
    {
        $post = Request::post();
        $Model = \app\common\model\Store::get($post['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\Store::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }

    public function getStoreListByArea()
    {
        $post = Request::post();
        $storeList = \app\common\model\Store::getStoreListByArea($post);

        return json($storeList);
    }
}