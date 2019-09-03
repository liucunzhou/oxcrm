<?php
namespace app\api\controller;

use app\common\model\BanquetHall;
use app\common\model\OperateLog;
use app\common\model\OrderApply;
use app\common\model\Search;
use app\common\model\User;
use app\common\model\Source;
use think\facade\Request;

class Order extends Base
{
    public function entire()
    {

        $get = Request::param();
        $config = [
            'page' => $get['page']
        ];

        $get['news_type'] = 2;
        $map = Search::order($this->user, $get);
        $list = model('OrderEntire')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
        $data = $list->getCollection();
        $sources = Source::getSources();
        $users = User::getUsers();
        $halls = BanquetHall::getBanquetHalls();
        foreach ($data as $key => &$value) {
            $value['sign_date'] = substr($value['sign_date'], 0, 10);
            $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
            $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
            $value['sales_id'] = isset($users[$value['sales_id']]) ? $users[$value['sales_id']]['realname'] : '——';
            $value['manager_id'] = isset($users[$value['manager_id']]) ? $users[$value['manager_id']]['realname'] : '——';
            $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '——';
        }

        $result = [
            'code' => 0,
            'msg' => '获取数据成功',
            'count' => $list->total(),
            'data' => $data
        ];
        return xjson($result);

    }

    /**
     * 婚庆订单
     * @return mixed
     */
    public function wedding()
    {

        $get = Request::param();
        $map = [];
        $config = [
            'page' => $get['page']
        ];

        $get['news_type'] = 1;
        $map = Search::order($this->user, $get);
        $list = model('OrderEntire')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
        $data = $list->getCollection();

        $sources = Source::getSources();
        $users = User::getUsers();
        $halls = BanquetHall::getBanquetHalls();
        foreach ($data as $key => &$value) {
            $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
            $value['sales_id'] = isset($users[$value['sales_id']]) ? $users[$value['sales_id']]['realname'] : '——';
            $value['manager_id'] = isset($users[$value['manager_id']]) ? $users[$value['manager_id']]['realname'] : '——';
            $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '——';
        }

        $result = [
            'code' => 0,
            'msg' => '获取数据成功',
            'count' => $list->total(),
            'data' => $data
        ];
        return xjson($result);
    }

    /**
     * 婚宴订单
     * @return mixed
     */
    public function banquet()
    {
        $get = Request::param();
        $map = [];
        $config = [
            'page' => $get['page']
        ];

        $get['news_type'] = 0;
        $map = Search::order($this->user, $get);
        $list = model('OrderEntire')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
        $data = $list->getCollection();
        $sources = Source::getSources();
        $users = User::getUsers();
        $halls = BanquetHall::getBanquetHalls();
        foreach ($data as $key => &$value) {
            $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
            $value['sales_id'] = isset($users[$value['sales_id']]) ? $users[$value['sales_id']]['realname'] : '——';
            $value['manager_id'] = isset($users[$value['manager_id']]) ? $users[$value['manager_id']]['realname'] : '——';
            $value['banquet_hall_id'] = isset($halls[$value['banquet_hall_id']]) ? $halls[$value['banquet_hall_id']]['title'] : '——';
        }

        $result = [
            'code' => 0,
            'msg' => '获取数据成功',
            'count' => $list->total(),
            'data' => $data
        ];

        return xjson($result);
    }

    public function createOrder(){
        $post = Request::param();
        $action = '添加订单信息';
        $Model = new \app\common\model\OrderEntire();

        $Model->sales_id = $this->user['id'];
        $Model->manager_id = User::getTopManager($this->user);
        $result = $Model->save($post);
        if($result) {
            $data = [];
            $data['user_id'] = $this->user['id'];
            $data['order_id'] = $Model->id;
            $data['apply_status'] = 0;
            $data['create_time'] = time();
            $OrderApply = new OrderApply();
            $OrderApply->insert($data);
            OperateLog::appendTo($Model);
            ### 更新用户信息缓存
            return xjson(['code'=>'200', 'msg'=> $action.'成功', 'redirect'=>$post['referer']]);
        } else {
            return xjson(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    public function getOrder()
    {
        $map = [];
        $map[] = ['id', '=', $this->params['id']];
        $order = model('OrderEntire')->where($map)->find()->toArray();

        $result = [
            'code'  => 0,
            'msg'   => '获取订单信息成功',
            'data'  => $order
        ];

        return xjson($result);
    }
}