<?php

namespace app\h5\controller\customer;

use app\common\model\Budget;
use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\Source;
use app\common\model\Store;
use app\common\model\User;
use app\h5\controller\Base;
use think\facade\Request;
use app\common\model\Region;
use app\common\model\MemberVisit;
use app\common\model\MobileRelation;

class Customer extends Base
{
    protected $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];
    protected $washStatus = [];
    protected $hotels = [];
    protected $sources = [];
    protected $status = [];
    protected $auth = [];
    protected $budgets = [];
    protected $scales = [];
    protected $model = null;

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->sources = Source::getSources();
        $this->hotels = Store::getStoreList();
        $this->status = Intention::getIntentions();
        // $this->auth = UserAuth::getUserLogicAuth($this->user['id']);


        $this->model = new MemberAllocate();
        $this->Membermodel = new Member();
    }


    /**
     * 客资公海
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $get = $this->request->param();
        $get['limit'] = isset($get['limit']) ? $get['limit'] : 3;
        $get['page'] = isset($get['page']) ? $get['page'] + 1 : 1;
        $config = [
            'page' => $get['page']
        ];

        $map = [];

        if ($this->role['auth_type'] == 0) {
            // 普通员工 多选
            $map[] = ['user_id', '=', $this->user['id']];
        } else {
            $staffs = User::getUsersByDepartmentId($this->user['department_id'], false);
            if (!isset($get['staff_id']) && count($get['staff_id']) > 0) {
                // 搜索状态下
                $map[] = ['user_id', 'in', $get['staff_id']];
            } else {
                // 管理者
                $map[] = ['user_id', 'in', $staffs];
            }
        }

        $list = $this->model->where($map)->order('create_time desc,member_create_time desc')->paginate($get['limit'], false, $config);
        if (!empty($list)) {
            $users = User::getUsers();
            $data = $list->getCollection()->toArray();

            foreach ($data as &$value) {
                $value['operator'] = $users[$value['operate_id']]['realname'];
                $value['user_realname'] = $users[$value['user_id']]['realname'];
                $value['mobile'] = substr_replace($value['mobile'], '***', 3, 3);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                $value['color'] = $value['active_status'] ? $this->status[$value['active_status']]['color'] : '#FF0000';
                $value['allocate_time'] = $value['create_time'];
                if ($value['member_create_time'] > 0) {
                    $value['member_create_time'] = date('Y-m-d H:i', $value['member_create_time']);
                } else {
                    $value['member_create_time'] = $value['create_time'];
                }

                if ($value['member_id'] > 0) {
                    $memberObj = Member::get($value['member_id']);
                    $value['visit_amount'] = $memberObj->visit_amount;
                }
            }

            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data,
            ];
        } else {

            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'data' => []
            ];
        }

        return xjson($result);
    }

    /**
     * 今日跟进
     * [today description]
     * @return [type] [description]
     */
    public function today()
    {
        $request = $this->request->param();
        $request['limit'] = isset($request['limit']) ? $request['limit'] : 3;
        $request['page'] = isset($request['page']) ? $request['page'] + 1 : 1;
        $config = [
            'page' => $request['page']
        ];

        $map[] = ['active_status', 'not in', [2, 3, 4]];
        $tomorrow = strtotime('tomorrow');
        if (!isset($request['next_visit_time']) || empty($request['next_visit_time'])) {
            $map[] = ['next_visit_time', '>', 0];
            $map[] = ['next_visit_time', 'between', [0, $tomorrow]];
        } else {
            $request['next_visit_time'] = strtotime($request['next_visit_time']);
            $map[] = ['next_visit_time', 'between', [$request['next_visit_time'], $tomorrow]];
        }

        $field = 'id,realname,mobile,active_status,member_id,create_time';
        $list = $this->model->where($map)->field($field)->order('next_visit_time desc')->paginate($request['limit'], false, $config);
        if (!empty($list)) {
            foreach ($list as &$value) {
                $value['color'] = $value['active_status'] ? $this->status[$value['active_status']]['color'] : '#FF0000';
                $value['mobile'] = substr_replace($value['mobile'], '***', 3, 3);;
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
            }
            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $list->getCollection(),
            ];

        } else {

            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'count' => 0,
                'data' => []
            ];
        }
        return json($result);
    }

    /**
     * 客资详情
     * Method member
     * @return \think\response\Json
     */
    public function member()
    {
        $get = Request::param();
        $allocate = MemberAllocate::get($get['id']);
        ### 获取用户基本信息
        $field = "id,realname,mobile,mobile1,active_status,budget,banquet_size,banquet_size_end,zone,source_text,wedding_date,hotel_text,remark";
        $customer = Member::field($field)->get($allocate->member_id);
        $customer['next_visit_time'] = date('Y-m-s h:i:s', $allocate->next_visit_time);
        $customer['color'] = $customer['active_status'] ? $this->status[$customer['active_status']]['color'] : '#FF0000';
        $customer['active_status'] = $customer['active_status'] ? $this->status[$customer['active_status']]['title'] : "未跟进";
        if (!($this->auth['is_show_entire_mobile'] || !empty($allocate))) {
            $customer['mobile'] = substr_replace($customer['mobile'], '****', 3, 4);
            $customer['mobile1'] = substr_replace($customer['mobile1'], '****', 3, 4);
        }

        $result = [
            'code' => 200,
            'msg' => '客资详情',
            'data' => $customer
        ];
        return json($result);
    }

    /**
     * 我的客资
     * Method mine
     * @return \think\response\Json
     */
    public function mine()
    {
        $get = Request::param();
        $get['limit'] = isset($get['limit']) ? $get['limit'] : 3;
        $get['page'] = isset($get['page']) ? $get['page'] + 1 : 1;
        $config = [
            'page' => $get['page']
        ];
        $map[] = ['user_id', '=', $get['user_id']];
        ##用户  权限   查询
        //$map = Search::customerMine($this->user, $get);

        $field = "id,member_id,realname,mobile,mobile1,active_status,budget,banquet_size,banquet_size_end,zone,source_text,wedding_date,color";
        $list = $this->model->where($map)->field($field)->order('create_time desc,member_create_time desc')->paginate($get['limit'], false, $config);
        if (!empty($list)) {
            foreach ($list as &$value) {
                $value['color'] = $value['active_status'] ? $this->status[$value['active_status']]['color'] : '#FF0000';
                $value['mobile'] = substr_replace($value['mobile'], '***', 3, 3);;
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
            }
            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $list->getCollection(),
            ];

        } else {

            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'count' => 0,
                'data' => []
            ];
        }
        return json($result);

    }
}
