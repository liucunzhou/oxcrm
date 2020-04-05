<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/4/22
 * Time: 8:46 PM
 */

namespace app\index\controller\dictionary;


use app\common\model\Brand;
use app\common\model\Region;
use app\index\controller\Backend;
use think\facade\Request;

class Supplier extends Backend
{

    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];

            if (isset($get['id']) && $get['id'] > 0) {
                $map[] = ['id', '=', $get['id']];
            }

            if (isset($get['title'])) {
                $map[] = ['title', 'like', "%{$get['title']}%"];
            }

            $list = model('Supplier')->where($map)->order('is_valid desc,sort desc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();

            $result = [
                'code'  => 0,
                'msg'   => '获取数据成功',
                'count' => $list->total(),
                'data'  => $data
            ];
            return json($result);
        } else {
            return $this->fetch();
        }
    }

    public function addSupplier()
    {
        ### 获取品牌列表
        $brands = Brand::getBrands();
        $this->assign('brands', $brands);

        $this->view->engine->layout(false);
        return $this->fetch('edit_supplier');
    }

    public function editSupplier()
    {
        ### 获取品牌列表
        $brands = Brand::getBrands();
        $this->assign('brands', $brands);

        ### 获取门店详情
        $get = Request::param();
        $data = \app\common\model\Supplier::get($get['id']);
        $this->assign('data', $data);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditSupplier()
    {
        $post = Request::post();
        if($post['id']) {
            $Model = \app\common\model\Supplier::get($post['id']);
        } else {
            $Model = new \app\common\model\Supplier();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            empty($post['id']) && $post['id'] = $Model->id;
            $suppliers = \app\common\model\Supplier::getList(true);
            $js = 'var suppliers = '.json_encode($suppliers, JSON_UNESCAPED_UNICODE);
            file_put_contents('./assets/json/suppliers.js', $js);

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> '添加成功']);
        } else {
            return json(['code'=>'500', 'msg'=> '添加失败']);
        }
    }

    public function deleteSupplier()
    {
        $post = Request::post();
        $Model = \app\common\model\Supplier::get($post['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\Supplier::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }

    public function getSupplierListByArea()
    {
        $post = Request::post();
        $SupplierList = \app\common\model\Supplier::getSupplierListByArea($post);

        return json($SupplierList);
    }
}