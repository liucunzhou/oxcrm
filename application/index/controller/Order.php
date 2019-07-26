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
            $config = [
                'page' => $get['page']
            ];

            $map[] = ['news_type', '=', '2'];
            $list = model('OrderEntire')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            $sources = \app\index\model\Source::getSources();
            $users = \app\index\model\User::getUsers();
            $halls = BanquetHall::getBanquetHalls();
            foreach ($data as $key=>&$value) {
                $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
                $value['sales_id'] = isset($users[$value['sales_id']]) ? $users[$value['sales_id']]['realname'] : '——';
                $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '——';
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

    /**
     * 婚庆订单
     * @return mixed
     */
    public function wedding()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];

            $map[] = ['news_type', '=', '1'];
            $list = model('OrderEntire')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            $sources = \app\index\model\Source::getSources();
            $users = \app\index\model\User::getUsers();
            $halls = BanquetHall::getBanquetHalls();
            foreach ($data as $key=>&$value) {
                $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
                $value['sales_id'] = isset($users[$value['sales_id']]) ? $users[$value['sales_id']]['realname'] : '——';
                $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '——';
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

    /**
     * 婚宴订单
     * @return mixed
     */
    public function banquet()
    {

        if(Request::isAjax()) {
            $get = Request::param();
            $map = [];
            $config = [
                'page' => $get['page']
            ];

            $map[] = ['news_type', '=', '0'];
            $list = model('OrderEntire')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            $sources = \app\index\model\Source::getSources();
            $users = \app\index\model\User::getUsers();
            $halls = BanquetHall::getBanquetHalls();
            foreach ($data as $key=>&$value) {
                $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
                $value['sales_id'] = isset($users[$value['sales_id']]) ? $users[$value['sales_id']]['realname'] : '——';
                $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '——';
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


    public function createOrder()
    {
        $get = Request::param();
        if(empty($get['id'])) return false;

        $allocate = MemberAllocate::get($get['id']);
        $member = Member::get($allocate['member_id']);

        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);
        $this->assign('allocate', $allocate);
        $this->assign('member', $member);
        return $this->fetch('edit_order');
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

        $user = session('user');
        // $Model::create($post);
        $Model->user_id = $user['id'];
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