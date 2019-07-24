<?php
namespace app\index\controller;


use app\index\model\BanquetHall;
use app\index\model\Member;
use app\index\model\MemberAllocate;
use think\facade\Request;

class Order extends Base
{
    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];
            $list = model('OrderEntire')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
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

    /**
     * 婚庆订单
     * @return mixed
     */
    public function wedding()
    {
        $Order = new \app\index\model\OrderEntire();
        $list = $Order->order('create_time desc')->paginate(15);
        $this->assign('list', $list);
        return $this->fetch('index');
    }

    /**
     * 婚宴订单
     * @return mixed
     */
    public function banquet()
    {
        $Order = new \app\index\model\OrderEntire();
        $list = $Order->order('create_time desc')->paginate(15);
        $this->assign('list', $list);
        return $this->fetch('index');
    }

    /**
     * 创建订单
     * @return mixed
     */
    public function createOrder()
    {
        $get = Request::param();
        if(empty($get['id'])) return false;

        $allocate = MemberAllocate::get($get['id']);
        $member = Member::get($allocate['member_id']);
        $newsTypesView = ['edit_wedding_order', 'edit_banquet_order', 'edit_full_order'];
        if (isset($newsTypesView[$member['news_type']])) {
            $view = $newsTypesView[$member['news_type']];
        } else {
            $view = $newsTypesView[2];
        }

        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        return $this->fetch($view);
    }

    public function editOrder()
    {

    }

    #### 一站式完成订单
    public function doEditOrderEntire()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑订单信息';
            $Model = \app\index\model\OrderEntire::get($post['id']);
        } else {
            $action = '添加订单信息';
            $Model = new \app\index\model\OrderEntire();
        }

        // $Model::create($post);
        $result = $Model->save($post);
        if($result) {
            empty($post['id']) && $post['id'] = $Model->id;
            ### 更新用户信息缓存
            return json(['code'=>'200', 'msg'=> $action.'成功', 'redirect'=>$post['referer']]);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }
}