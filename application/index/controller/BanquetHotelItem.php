<?php
namespace app\index\controller;

use think\facade\Request;

class BanquetHotelItem extends Base
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('BanquetHotelItem')->where($map)->order('is_valid desc,sort desc,id asc')->paginate($get['limit'], false, $config);
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

    public function addBanquetHotelItem()
    {
        $this->view->engine->layout(false);
        return $this->fetch('edit_banquet_hotel_item');
    }

    public function editBanquetHotelItem()
    {
        $get = Request::param();
        $brand = \app\common\model\BanquetHotelItem::get($get['id']);
        $this->assign('data', $brand);

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function doEditBanquetHotelItem()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '更新酒店消费项目';
            $Model = \app\common\model\BanquetHotelItem::get($post['id']);
        } else {
            $action = '添加酒店消费项目';
            $Model = new \app\common\model\BanquetHotelItem();
        }

        // $Model::create($post);
        $result = $Model->save($post);
        if($result) {
            ### 更新缓存
            \app\common\model\BanquetHotelItem::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteBanquetHotelItem()
    {
        $post = Request::post();
        $Model = \app\common\model\BanquetHotelItem::get($post['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\BanquetHotelItem::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}