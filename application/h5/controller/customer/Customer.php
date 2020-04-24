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
    protected $staffs = [];

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->sources = Source::getSources();
        $this->hotels = Store::getStoreList();
        $this->status = Intention::getIntentions();

        $this->model = new MemberAllocate();
        $this->Membermodel = new Member();

        if (isset($this->role['auth_type']) && $this->role['auth_type'] > 0) {
            $this->staffs = User::getUsersByDepartmentId($this->user['department_id']);
        }
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

    # 客资详情
    public function member()
    {
        $request = $this->request->param();
        $allocate = MemberAllocate::get($request['id']);
        ### 获取用户基本信息
        $field = "id,realname,mobile,mobile1,active_status,budget,budget_end,banquet_size,banquet_size_end,zone,source_text,wedding_date,hotel_text,remark";
        $customer = Member::field($field)->get($allocate->member_id);
        $customer['next_visit_time'] = $allocate->next_visit_time ? date('Y-m-s h:i:s', $allocate->next_visit_time) : '';
        $customer['color'] = $customer['active_status'] ? $this->status[$customer['active_status']]['color'] : '#FF0000';
        $customer['active_status'] = $customer['active_status'] ? $this->status[$customer['active_status']]['title'] : "未跟进";
        if (!($this->auth['is_show_entire_mobile'] || !empty($allocate))) {
            $customer['mobile'] = substr_replace($customer['mobile'], '****', 3, 4);
            $customer['mobile1'] = substr_replace($customer['mobile1'], '****', 3, 4);
        }

        $result = [
            'code' => 200,
            'msg' => '客资详情',
            'data' => [
                'customer'  =>  $customer,
                'level'     =>  $this->config['levels'],
            ]
        ];
        return json($result);
    }
    #  realname/budget,budget_end,banquet_size,banquet_size_end,zone,remark,level
    public function doEdit()
    {
        $request = $this->request->param();
        $allocate = MemberAllocate::get($request['id']);
        $member = \app\api\model\Member::get($allocate->member_id);

        $request['update_time'] = time();
        $request['news_types'] = empty($request['news_types']) ? '' : implode(',', $request['news_types']);
        $member->allowField(true)->save($request);;
        $result = $allocate->allowField(true)->save($request);
        if ($result) {
            return json([
                'code' => '200',
                'msg' => '修改客资成功'
            ]);
        } else {
            return json([
                'code' => '500',
                'msg' => '修改客资失败'
            ]);
        }

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
        ###  管理者还是销售
        if($this->role['auth_type'] > 0) {
            ### 员工列表
            if( isset($request['user_id']) && !empty($result['user_id'])) {
                // $user_id = explode(',',$request['user_id']);
                if ($request['user_id'] == 'all') {
                    $map[] = ['user_id', 'in', $this->staffs];
                } else if (is_numeric($request['user_id'])) {
                    $map[] = ['user_id', '=', $this->user['id']];
                } else {
                    $map[] = ['user_id', 'in', $request['user_id']];
                }

            }  else {
                $map[] = ['user_id', '=', $this->user['id']];
            }

        } else {
            $map[] = ['user_id', '=', $this->user['id']];
        }

        ### 当前选择跟进渠道
        if(isset($request['active_status']) && is_numeric($request['active_status'])){
            $map[] = ['active_status','=',$request['active_status']];
        }

        ### 获取方式
        if( isset($request['allocate_type']) && is_numeric($request['allocate_type']) ){
            $map[] = ['allocate_type','=',$request['allocate_type']];
        }

        ### 时间区间
        if( isset($request['range']) && !empty($request['range'])){
            $range = format_date_range($request['range']);
            $map[] = ['create_time', 'between', $range];
        }

        $model = $this->model->where($map);
        ### 手机号筛选
        if( isset($request['mobile']) && strlen($request['mobile']) == 11 ){
           $model->where('member_id', 'in', function ($query) use ($request) {
                $query->table('tk_mobile')->where('mobile', '=', $request['mobile'])->field('member_id');
           });
        } else if (isset($request['mobile']) && strlen($request['mobile']) < 11){
            $model->where('member_id', 'in', function ($query) use ($request) {
                $query->table('tk_mobile')->where('mobile', 'like', "%{$request['mobile']}%")->field('member_id');
            });
        }

        $order = 'create_time desc,member_create_time desc';
        $field = "id,user_id,member_id,realname,mobile,mobile1,active_status,budget,banquet_size,banquet_size_end,zone,source_text,wedding_date,color";
        $list = $model->field($field)->order($order)->paginate($request['limit'], false, $config);

        if (!empty($list)) {
            foreach ($list as &$value) {
                $value['color'] = $value['active_status'] ? $this->status[$value['active_status']]['color'] : '#FF0000';
                $value['mobile'] = substr_replace($value['mobile'], '***', 3, 3);;
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
            }
            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'data' => $list->getCollection(),
            ];

        } else {

            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'data' => []
            ];
        }
        return json($result);
    }

    # 我的客资顶部导航带数据
    public function count()
    {
        $request = $this->request->param();
        $map = [];
        ###  管理者还是销售
        if($this->role['auth_type'] > 0) {
            ### 员工列表
            if( isset($request['user_id']) && !empty($result['user_id'])) {
                // $user_id = explode(',',$request['user_id']);
                if ($request['user_id'] == 'all') {
                    $map[] = ['user_id', 'in', $this->staffs];
                } else if (is_numeric($request['user_id'])) {
                    $map[] = ['user_id', '=', $this->user['id']];
                } else {
                    $map[] = ['user_id', 'in', $request['user_id']];
                }

            }  else {
                $map[] = ['user_id', '=', $this->user['id']];
            }

        } else {
            $map[] = ['user_id', '=', $this->user['id']];
        }


        ### 获取方式
        if( isset($request['allocate_type']) && is_numeric($request['allocate_type']) ){
            $map[] = ['allocate_type','=',$request['allocate_type']];
        }

        ### 时间区间
        if( isset($request['range']) && !empty($request['range'])){
            $range = format_date_range($request['range']);
            $map[] = ['create_time', 'between', $range];
        }

        $model = $this->model->where($map);
        ### 手机号筛选
        if( isset($request['mobile']) && strlen($request['mobile']) == 11 ){
            $model->where('member_id', 'in', function ($query) use ($request) {
                $query->table('tk_mobile')->where('mobile', '=', $request['mobile'])->field('member_id');
            });
        } else if (isset($request['mobile']) && strlen($request['mobile']) < 11){
            $model->where('member_id', 'in', function ($query) use ($request) {
                $query->table('tk_mobile')->where('mobile', 'like', "%{$request['mobile']}%")->field('member_id');
            });
        }

        $list = $model->field('active_status,count(*) as count')->where($map)->group('active_status')->select();
        if (!empty($list)) {
            $list = $list->toArray();
            $list = array_column($list,'count', 'active_status');

            $where = [];
            $where[] = ['type', '<>', 'wash'];
            $field = "id,title,color";
            $statusList = Intention::where($where)->field($field)->order('is_valid desc,sort desc,id asc')->select();

            $data = [];
            foreach ($statusList as $row) {
                $amount = isset($list[$row->id]) ? $list[$row->id] : 0;
                $data[] = [
                    'id'        => $row->id,
                    'title'     => $row->title,
                    'count'    => $amount
                ];
            }

            $all = [
                0 => [
                    'id'    => 'all',
                    'title' => '所有客资',
                    'count' => array_sum($list)
                ]
            ];
            $data = $all + $data;

            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'data' => $data
            ];

        } else {

            $result = [
                'code' => 200,
                'msg' => '获取数据成功',
                'data' => []
            ];
        }
        return json($result);
    }

    # 公海
    public function sea()
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
