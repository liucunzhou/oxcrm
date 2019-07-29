<?php
namespace app\index\controller;

use app\index\model\Store;
use think\facade\Request;

class BanquetHall extends Base
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
            $list = model('BanquetHall')->field($fields)->where($map)->order('is_valid desc,sort desc,id asc')->paginate($get['limit'], false, $config);
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

    public function addBanquetHall()
    {
        return $this->fetch('edit_banquet_hall');
    }

    public function editBanquetHall()
    {

        $get = Request::param();
        $brand = \app\index\model\BanquetHall::get($get['id']);
        $this->assign('data', $brand);

        $hotels = Store::getStoreList();
        $this->assign("hotels", $hotels);
        return $this->fetch();
    }

    public function doEditBanquetHall()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑宴会厅';
            if(isset($post['create_time'])) unset($post['create_time']);
            if(isset($post['modify_time'])) unset($post['modify_time']);
            $Model = \app\index\model\BanquetHall::get($post['id']);
        } else {
            $action = '添加宴会厅';
            $Model = new \app\index\model\BanquetHall();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            ### 更新缓存
            \app\index\model\BanquetHall::updateCache();

            ### 添加操作日志
            \app\index\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteBanquetHall()
    {
        $get = Request::param();
        $Model = \app\index\model\BanquetHall::get($get['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\index\model\BanquetHall::updateCache();

            ### 添加操作日志
            \app\index\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}