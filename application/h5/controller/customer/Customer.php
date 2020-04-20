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
        $request = $this->request->param();
        $request['limit'] = isset($request['limit']) ? $request['limit'] : 3;
        $request['page'] = isset($request['page']) ? $request['page'] + 1 : 1;
        $config = [
            'page' => $request['page']
        ];

        $map = [];
        $maps = [];
        ###  管理者还是销售
        if($this->role['auth_type'] > 0) {
            ### 员工列表
            if( isset($request['user_id']) && $request['user_id'] != 'all' && is_numeric($request['active_status'])){
//                $user_id = explode(',',$request['user_id']);
                $map[] = ['user_id','=',$request['user_id']];
            } elseif (  isset($request['user_id']) && $request['user_id'] != 'all') {
                $map[] = ['user_id','in',$request['user_id']];
            }
        } else {
            $map[] = ['user_id', '=', $this->user['user_id']];
        }

        ### 手机号筛选


        ### 当前选择跟进渠道
        if(isset($request['active_status']) && is_numeric($request['active_status'])){
            $map[] = ['active_status','=',$request['active_status']];
        }

        ### 获取方式
        if( isset($request['allocate_type']) && is_numeric($request['allocate_type']) ){
            $map[] = ['allocate_type','=',$request['allocate_type']];
        }
        ### 时间区间
        /*if( isset($request['range']) && $request['range'] == 'start_date' ){
            $map[] = [];
        }*/

        ##用户  权限   查询
        //$map = Search::customerMine($this->user, $get);
        $model = $this->model->where($map);

        $field = "id,member_id,realname,mobile,mobile1,active_status,budget,banquet_size,banquet_size_end,zone,source_text,wedding_date,color";
        $list = $model->field($field)->order('create_time desc,member_create_time desc')->paginate($request['limit'], false, $config);

        ###  清除条件中的跟进状态条件
        foreach ($map as $key=>$value){
            if( $value['0'] == 'active_status' ){
                unset($map[$key]);
            }
        }
        $lists = $this->model->field('id,active_status')->where($map)->select();
        if (!empty($list)) {
            foreach ($list as &$value) {
                $value['color'] = $value['active_status'] ? $this->status[$value['active_status']]['color'] : '#FF0000';
                $value['mobile'] = substr_replace($value['mobile'], '***', 3, 3);;
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
            }

            ###  出来列表页跟进状态
            $active_status = array_column($this->status,'id');
            $count = array_count_values(array_column($lists->toArray(),"active_status"));
            $wordCount = [];
            foreach($active_status as $k => $v)
            {
                $sl = isset($count[$v])?$count[$v]:0;
                $wordCount[] = [
                    'id'=>$v,
                    'title'=>$this->status[$v]['title'],
                    'count'=>$sl,
                ];
            }
            $count = [
                 '0'=>  [
                    'id'    =>  'all',
                    'title' =>  '所有客资',
                    'count' =>count($lists)
                    ]
            ];

            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'counts'=> $count + $wordCount,
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
     * 客资公海
     * Method seas
     * @return mixed|\think\response\Json
     */
    public function seas()
    {
        $request = $this->request->param();
        $config = [
            'page' => $request['page']
        ];
        $map[] = ['is_sea', '=', '1'];
        if (isset($request['source']) && $request['source'] > 0) {
            $map[] = ['source_id', '=', $request['source']];
        }

        if (isset($request['staff']) && $request['staff'] > 0) {
            $map[] = ['operate_id', '=', $request['staff']];
        }

        if (isset($request['city_id']) && $request['city_id'] > 0) {
            $map[] = ['city_id', '=', $request['city_id']];
        }

        ###  默认隐藏失效、无效客资
        $map[] = ['active_status', 'not in', [3, 4]];
        if (isset($request['date_range']) && !empty($request['date_range'])) {
            $range = explode('~', $request['date_range']);
            $range[0] = trim($range[0]);
            $range[1] = trim($range[1]);
            $start = strtotime($range[0]);
            $end = strtotime($range[1]);
            $map[] = ['create_time', 'between', [$start, $end]];
        }

        ### 省市划分
        if ($this->user['city_id'] > 0) {
            $map[] = ['city_id', '=', $this->user['city_id']];
        }

        if (isset($request['keywords']) && strlen($request['keywords']) == 11) {

            $map = [];
            $mobiles = MobileRelation::getMobiles($request['keywords']);
            if (!empty($mobiles)) {
                $map[] = ['mobile', 'in', $mobiles];
            } else {
                $map[] = ['mobile', '=', $request['keywords']];
            }
            $where1[] = ['mobile', 'like', "%{$request['keywords']}%"];
            $where2[] = ['mobile1', 'like', "%{$request['keywords']}%"];
            $list = model('Member')->where($map)->whereOr($where1)->whereOr($where2)->order('create_time desc')->paginate($request['limit'], false, $config);

        } else if (isset($request['keywords']) && !empty($request['keywords']) && strlen($request['keywords']) < 11) {

            $map = [];
            $map[] = ['mobile', 'like', "%{$request['keywords']}%"];
            $list = model('Member')->where($map)->order('create_time desc')->paginate($request['limit'], false, $config);

        } else if (isset($request['keywords']) && strlen($request['keywords']) > 11) {

            $map = [];
            $map[] = ['mobile', 'like', "%{$request['keywords']}%"];
            $list = model('Member')->where($map)->order('create_time desc')->paginate($request['limit'], false, $config);

        } else {

            $list = model('Member')->where($map)->where('id', 'not in', function ($query) {
                $map = [];
                $map[] = ['user_id', '=', $this->user['id']];
                $query->table('tk_member_allocate')->where($map)->field('member_id');
            })->order('create_time desc')->paginate($request['limit'], false, $config);

        }
        $data = $list->getCollection()->toArray();
        $users = User::getUsers();
        foreach ($data as &$value) {
            $value['operator'] = $users[$value['operate_id']]['realname'];
            $value['mobile'] = substr_replace($value['mobile'], '***', 3, 3);
            $value['news_type'] = $this->newsTypes[$value['news_type']];
            $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";

            if ($this->auth['is_show_alias'] == '1') {
                $value['source_text'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
            } else {
                $value['source_text'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
            }
        }
        $result = [
            'code' => 0,
            'msg' => '获取数据成功',
            'pages' => $list->lastPage(),
            'count' => $list->total(),
            'map' => $map,
            'data' => $data
        ];

        return json($result);
    }
}
