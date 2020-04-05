<?php
namespace app\index\controller\dictionary;

use app\common\model\Store;
use app\index\controller\Backend;
use think\facade\Request;

class Hall extends Backend
{
    public function index()
    {
        if (Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];

            if (isset($get['title'])) {
                $map[] = ['title', 'like', "%{$get['title']}%"];
            }

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
        $data = \app\common\model\BanquetHall::get($get['id']);
        $this->assign('data', $data);

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
            $Model = \app\common\model\BanquetHall::get($post['id']);
        } else {
            $action = '添加宴会厅';
            $Model = new \app\common\model\BanquetHall();
        }

        // $Model::create($post);
        $result = $Model->save($post);

        if($result) {
            ### 更新缓存
            \app\common\model\BanquetHall::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=> $action.'成功']);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function deleteBanquetHall()
    {
        $get = Request::param();
        $Model = \app\common\model\BanquetHall::get($get['id']);
        $result = $Model->delete();

        if($result) {
            // 更新缓存
            \app\common\model\BanquetHall::updateCache();

            ### 添加操作日志
            \app\common\model\OperateLog::appendTo($Model);
            return json(['code'=>'200', 'msg'=>'删除成功']);
        } else {
            return json(['code'=>'500', 'msg'=>'删除失败']);
        }
    }
}