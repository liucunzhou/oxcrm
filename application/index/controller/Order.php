<?php
namespace app\index\controller;


use app\common\model\Allocate;
use app\common\model\BanquetHall;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\OperateLog;
use app\common\model\OrderApply;
use app\common\model\OrderEntire;
use app\common\model\Search;
use app\common\model\Tab;
use app\common\model\User;
use think\facade\Request;

class Order extends Base
{

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        if(!Request::isAjax()) {
            $staffes = User::getUsersInfoByDepartmentId($this->user['department_id']);
            $this->assign('staffes', $staffes);
            $sources = \app\common\model\Source::getSources();
            $this->assign('sources', $sources);
        }

    }

    public function index()
    {
        if(Request::isAjax()) {
            $get = Request::param();
            $config = [
                'page' => $get['page']
            ];

            $get['news_type'] = 2;
            $map = Search::order($this->user, $get);

            $list = model('OrderEntire')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            $sources = \app\common\model\Source::getSources();
            $users = \app\common\model\User::getUsers();
            $halls = BanquetHall::getBanquetHalls();
            foreach ($data as $key=>&$value) {
                $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
                $value['sales_id'] = isset($users[$value['sales_id']]) ? $users[$value['sales_id']]['realname'] : '——';
                $value['manager_id'] = isset($users[$value['manager_id']]) ? $users[$value['manager_id']]['realname'] : '——';
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

            $get['news_type'] = 1;
            $map = Search::order($this->user, $get);
            $list = model('OrderEntire')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            $sources = \app\common\model\Source::getSources();
            $users = \app\common\model\User::getUsers();
            $halls = BanquetHall::getBanquetHalls();
            foreach ($data as $key=>&$value) {
                $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
                $value['sales_id'] = isset($users[$value['sales_id']]) ? $users[$value['sales_id']]['realname'] : '——';
                $value['manager_id'] = isset($users[$value['manager_id']]) ? $users[$value['manager_id']]['realname'] : '——';
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

            $get['news_type'] = 0;
            $map = Search::order($this->user, $get);
            $list = model('OrderEntire')->where($map)->order('id desc')->paginate($get['limit'], false, $config);
            $data = $list->getCollection();
            $sources = \app\common\model\Source::getSources();
            $users = \app\common\model\User::getUsers();
            $halls = BanquetHall::getBanquetHalls();
            foreach ($data as $key=>&$value) {
                $value['source_id'] = isset($sources[$value['source_id']]) ? $sources[$value['source_id']]['title'] : '——';
                $value['sales_id'] = isset($users[$value['sales_id']]) ? $users[$value['sales_id']]['realname'] : '——';
                $value['manager_id'] = isset($users[$value['manager_id']]) ? $users[$value['manager_id']]['realname'] : '——';
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
            return $this->fetch();
        }
    }


    public function createOrder()
    {
        $get = Request::param();
        if(empty($get['id'])) return false;
        $user = User::getUser($get['user_id']);
        $allocate = MemberAllocate::getAllocate($user['id'], $get['member_id']);
        $this->assign('allocate', $allocate);

        $member = Member::get($get['member_id']);
        $this->assign('member', $member);

        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);
        return $this->fetch('edit_order');
    }

    public function editOrder()
    {
        $get = Request::param();
        if(empty($get['id'])) return false;
        $order = OrderEntire::get($get['id']);
        $this->assign('data', $order);

        $allocate = MemberAllocate::getAllocate($this->user['id'], $order['member_id']);
        $this->assign('allocate', $allocate);

        $member = Member::get($order['member_id']);
        $this->assign('member', $member);

        $halls = BanquetHall::getBanquetHalls();
        $this->assign('halls', $halls);

        $member = Member::get($order['member_id']);
        $this->assign('member', $member);
        return $this->fetch('edit_order');
    }

    #### 一站式完成订单
    public function doEditOrderEntire()
    {
        $post = Request::post();
        if($post['id']) {
            $action = '编辑订单信息';
            $Model = \app\common\model\OrderEntire::get($post['id']);
        } else {
            $action = '添加订单信息';
            $Model = new \app\common\model\OrderEntire();
        }

        $user = session('user');
        // $Model::create($post);
        $Model->user_id = $user['id'];
        $Model->sales_id = $this->user['id'];
        $Model->manager_id = User::getTopManager($this->user);
        $result = $Model->save($post);
        if($result) {
            if(empty($post['id'])) {
                ### 发送申请
                /**
                `id` int(11) not null auto_increment,
                `operate_id` int(11) not null default '0',
                `user_id` int(11) not null default '0',
                `order_id` int(11) not null default '0',
                `apply_status` int(11) not null default '0',
                `create_time` int(11) not null default '0',
                `update_time` int(11) not null default '0',
                `delete_time` int(11) not null default '0',
                 */
                $data = [];
                $data['user_id'] = $this->user['id'];
                $data['order_id'] = $Model->id;
                $data['apply_status'] = 0;
                $data['create_time'] = time();
                $OrderApply = new OrderApply();
                /**
                $where = [];
                $where[] = ['user_id', '=', $this->user['id']];
                $where[] = ['order_id', '=', $Model->id];
                 * **/
                $OrderApply->insert($data);
            }

            OperateLog::appendTo($Model);
            ### 更新用户信息缓存
            return json(['code'=>'200', 'msg'=> $action.'成功', 'redirect'=>$post['referer']]);
        } else {
            return json(['code'=>'500', 'msg'=> $action.'失败']);
        }
    }

    ### 订单申请
    public function orderApply()
    {
        $get = Request::param();
        if (Request::isAjax()) {
            $newsTypes = ['婚宴信息', '婚庆信息', '婚宴转婚庆'];
            $sources = \app\common\model\Source::getSources();
            $config = [
                'page' => $get['page']
            ];

            $map[] = ['user_id', '=', $this->user['id']];
            if (isset($get['status'])) {
                $map[] = ['apply_status', '=', $get['status']];
            }
            $list = model('OrderApply')->where($map)->with('OrderEntire')->paginate($get['limit'], false, $config);
            if (!empty($list)) {
                $data = $list->getCollection()->toArray();
                foreach ($data as &$value) {
                    if (empty($value['order_entire'])) continue;
                    $order = $value['order_entire'];
                    unset($order['id']);
                    $value = array_merge($value, $order);
                    $value['news_type'] = $newsTypes[$value['news_type']];
                    $value['source_id'] = $sources[$value['source_id']]['title'];
                }
            }

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data
            ];
            return json($result);

        } else {
            $tabs = Tab::orderApply($get);
            $this->assign('tabs', $tabs);
            $this->assign('get', $get);
            return $this->fetch();
        }
    }
}