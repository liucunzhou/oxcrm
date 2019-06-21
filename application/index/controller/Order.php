<?php
namespace app\index\controller;


use think\facade\Request;

class Order extends Base
{
    public function index()
    {
        $Order = new \app\index\model\OrderEntire();
        $list = $Order->order('create_time desc')->paginate(15);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function wedding()
    {
        $Order = new \app\index\model\OrderEntire();
        $list = $Order->order('create_time desc')->paginate(15);
        $this->assign('list', $list);
        return $this->fetch('index');
    }

    public function banquet()
    {

        $Order = new \app\index\model\OrderEntire();
        $list = $Order->order('create_time desc')->paginate(15);
        $this->assign('list', $list);
        return $this->fetch('index');
    }

    public function addOrder()
    {
        return $this->fetch('edit_order');
    }

    public function editOrder()
    {
        return $this->fetch();
    }

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