<?php

namespace app\h5\controller\customer;

use app\common\model\Budget;
use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\MemberApply;
use app\common\model\Mobile;
use app\common\model\Source;
use app\common\model\Store;
use app\common\model\User;
use app\common\model\UserAuth;
use app\h5\controller\Base;
use think\facade\Request;
use app\common\model\Region;
use app\common\model\MemberVisit;
use app\common\model\MobileRelation;
use think\Validate;

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

        // $this->auth = UserAuth::getUserLogicAuth($this->user['id']);
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

        $list = $this->model->where($map)->order('id desc')->paginate($get['limit'], false, $config);
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

        return json($result);
    }

    /**
     * 今日跟进
     * [today description]
     * @return [type] [description]
     */
    public function today()
    {
        $param = $this->request->param();
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 3;
        $param['page'] = isset($param['page']) ? $param['page'] + 1 : 1;
        $config = [
            'page' => $param['page']
        ];

        $map[] = ['user_id', '=', $this->user['id']];
        $map[] = ['active_status', 'not in', [3, 4]];
        $start = strtotime($param['next_visit_time']);
        $tomorrow = strtotime('tomorrow');
        $today = $tomorrow - 86400;
        if (!isset($param['next_visit_time']) || empty($param['next_visit_time']) || $today == $start) {
            $map[] = ['next_visit_time', '>', 0];
            $map[] = ['next_visit_time', 'between', [0, $tomorrow]];
        } else {
            $end = $start + 86400;
            $map[] = ['next_visit_time', 'between', [$start, $end]];
        }

        $field = 'id,realname,mobile,active_status,member_id,create_time';
        $list = $this->model->where($map)->field($field)->order('next_visit_time desc')->paginate($param['limit'], false, $config);
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
        $param = $this->request->param();
        $allocate = MemberAllocate::get($param['id']);
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
                'customer' => $customer,
                'level' => $this->config['levels'],
            ]
        ];
        return json($result);
    }

    # 新增客资
    public function doCreate()
    {
        $param = $this->request->param();
        $res = $this->validate($param, 'Customer');
        if (true !== $res) {
            // 验证失败 输出错误信息
            return json([
                'code' => '400',
                'msg' => $res,
            ]);
        }

        $model = new Member();
        $model->member_no = date('YmdHis') . rand(100, 999);
        ### 验证手机号唯一性
        if (empty($param['mobile1'])) {
            $originMember = $model::checkFromMobileSet($param['mobile'], false);
            if ($originMember) {
                return json([
                    'code' => '400',
                    'msg' => $param['mobile'] . '手机号已经存在',
                ]);
            }
        } else {
            $originMember1 = $model::checkFromMobileSet($param['mobile'], false);
            $originMember2 = $model::checkFromMobileSet($param['mobile1'], false);
            if ($originMember1 || $originMember2) {
                return json([
                    'code' => '400',
                    'msg' => $param['mobile'] . '手机号已经存在',
                ]);
            }
        }

        ### 同步来源名称
        if (isset($param['source_id']) && $param['source_id'] > 0) {
            $param['source_text'] = $this->sources[$param['source_id']]['title'];
            $model->source_text = $this->sources[$param['source_id']]['title'];
        }

        ### 基本信息入库
        $model->is_sea = 1;
        if (in_array($this->user['role_id'], [5, 6, 8, 26])) {
            // 代表来源登陆手机端，会进入派单组公海
            $param['add_source'] = 1;
        }

        $model->operate_id = $this->user['id'];
        $result1 = $model->allowField(true)->save($param);

        if ($result1) {
            $memberId = $model->id;
            ### 新添加客资要加入到分配列表中
            $param['operate_id'] = $this->user['id'];
            $param['allocate_type'] = 3;
            MemberAllocate::insertAllocateData($this->user['id'], $memberId, $param);

            $mobileModel = new Mobile();
            $mobileModel->insert(['mobile' => $param['mobile'], 'member_id' => $memberId]);
            ### 将手机号1添加到手机号库
            if (!empty($param['mobile1'])) {
                $mobileModel->insert(['mobile' => $param['mobile1'], 'member_id' => $memberId]);
            }
            $result = ['code' => '200', 'msg' => '添加客资成功'];
        } else {
            $result = ['code' => '500', 'msg' => '添加客资失败, 请重试'];
        }

        return json($result);
    }

    #  realname/budget,budget_end,banquet_size,banquet_size_end,zone,remark,level
    public function doEdit()
    {
        $param = $this->request->param();
        $memberAllocate = new MemberAllocate();
        $allocate = $memberAllocate->where('id', '=', $param['id'])->find();
        unset($param['id']);
        $result = $allocate->allowField(true)->save($param);

        $memberModel = new Member();
        $member = $memberModel->where('id', '=', $allocate->member_id)->find();
        $member->allowField(true)->save($param);

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

    public function unique()
    {
        $param = $this->request->param();
        $list = Mobile::where('mobile', like, "%{$param['mobile']}%")->select();
        if($list->isEmpty()) {
            $result = [
                'code'  => '200',
                'msg'   => '该手机号不存在，可以添加'
            ];
        } else {
            $result = [
                'code'  => '400',
                'msg'   => '该手机号已存在'
            ];
        }

        return json($result);
    }

    /**
     * 我的客资
     * Method mine
     * @return \think\response\Json
     */
    public function mine()
    {
        $param = $this->request->param();
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 3;
        $param['page'] = isset($param['page']) ? $param['page'] + 1 : 1;
        $config = [
            'page' => $param['page']
        ];

        $map = [];
        ###  管理者还是销售
        if ($this->role['auth_type'] > 0) {
            ### 员工列表
            if (isset($param['user_id']) && !empty($param['user_id'])) {
                // $user_id = explode(',',$param['user_id']);
                if ($param['user_id'] == 'all') {
                    $map[] = ['user_id', 'in', $this->staffs];
                } else if (is_numeric($param['user_id'])) {
                    $map[] = ['user_id', '=', $this->user['id']];
                } else {
                    $map[] = ['user_id', 'in', $param['user_id']];
                }

            } else {
                $map[] = ['user_id', '=', $this->user['id']];
            }

        } else {
            $map[] = ['user_id', '=', $this->user['id']];
        }

        ### 当前选择跟进渠道
        if (isset($param['active_status']) && is_numeric($param['active_status'])) {
            $map[] = ['active_status', '=', $param['active_status']];
        } else if (empty($param['active_status'])) {
            $map[] = ['active_status', '=', 0];
        }

        ### 获取方式
        if (isset($param['allocate_type']) && is_numeric($param['allocate_type'])) {
            $map[] = ['allocate_type', '=', $param['allocate_type']];
        }

        ### 时间区间
        if (isset($param['range']) && $param['range']!='all') {
            $range = format_date_range($param['range']);
            $map[] = ['create_time', 'between', $range];
        }
        $model = $this->model->where($map);

        ### 手机号筛选
        if ($param['mobile']!='' && strlen($param['mobile']) == 11) {
            $model->where('member_id', 'in', function ($query) use ($param) {
                $query->table('tk_mobile')->where('mobile', '=', $param['mobile'])->field('member_id');
            });
        } else if ($param['mobile']!='' && strlen($param['mobile']) < 11) {
            $model->where('member_id', 'in', function ($query) use ($param) {
                $query->table('tk_mobile')->where('mobile', 'like', "%{$param['mobile']}%")->field('member_id');
            });
        }

        $order = 'create_time desc,member_create_time desc';
        $field = "id,user_id,member_id,realname,mobile,mobile1,active_status,budget,banquet_size,banquet_size_end,zone,source_text,wedding_date,color";
        $list = $model->field($field)->order($order)->paginate($param['limit'], false, $config);
        // echo $model->getLastSql();
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
        $param = $this->request->param();
        $map = [];
        ###  管理者还是销售
        if ($this->role['auth_type'] > 0) {
            ### 员工列表
            if (isset($param['user_id']) && !empty($param['user_id'])) {
                if ($param['user_id'] == 'all') {
                    $map[] = ['user_id', 'in', $this->staffs];
                } else if (is_numeric($param['user_id'])) {
                    $map[] = ['user_id', '=', $this->user['id']];
                } else {
                    $map[] = ['user_id', 'in', $param['user_id']];
                }

            } else {
                $map[] = ['user_id', '=', $this->user['id']];
            }

        } else {
            $map[] = ['user_id', '=', $this->user['id']];
        }

        ### 获取方式
        if (isset($param['allocate_type']) && is_numeric($param['allocate_type'])) {
            $map[] = ['allocate_type', '=', $param['allocate_type']];
        }

        ### 时间区间
        if (isset($param['range']) && !empty($param['range'])) {
            $range = format_date_range($param['range']);
            $map[] = ['create_time', 'between', $range];
        }

        $model = $this->model->where($map);
        ### 手机号筛选
        if (isset($param['mobile']) && strlen($param['mobile']) == 11) {
            $model->where('member_id', 'in', function ($query) use ($param) {
                $query->table('tk_mobile')->where('mobile', '=', $param['mobile'])->field('member_id');
            });
        } else if (isset($param['mobile']) && strlen($param['mobile']) < 11) {
            $model->where('member_id', 'in', function ($query) use ($param) {
                $query->table('tk_mobile')->where('mobile', 'like', "%{$param['mobile']}%")->field('member_id');
            });
        }

        $list = $model->field('active_status,count(*) as count')->where($map)->group('active_status')->select();
        if (!empty($list)) {
            $list = $list->toArray();
            $list = array_column($list, 'count', 'active_status');

            $where = [];
            $where[] = ['type', '<>', 'wash'];
            $statusList = Intention::getIntentions();
            // $statusList = array_keys($statusList);

            $data = [];
            foreach ($statusList as $row) {
                $amount = isset($list[$row['id']]) ? $list[$row['id']] : 0;
                $data[] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'count' => $amount
                ];
            }

            $all = [
                0 => [
                    'id' => 'all',
                    'title' => '所有客资',
                    'count' => array_sum($list)
                ]
            ];
            $data = array_merge($all, $data);

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
        $param = $this->request->param();
        $param['limit'] = isset($param['limit']) ? $param['limit'] : 5;
        $param['page'] = isset($param['page']) ? $param['page'] + 1 : 1;
        $config = [
            'page' => $param['page']
        ];
        $member = new Member();
        // $map[] = ['is_sea', '=', '1'];
        if ($this->user['city_id'] > 0) {
            $map[] = ['city_id', '=', $this->user['city_id']];
        }
        ###  默认隐藏失效、无效客资
        $fields = "id,realname,mobile,active_status,banquet_size,banquet_size_end,budget,budget_end,hotel_text,zone,create_time";
        $member = $member->field($fields)->where($map);

        ### 手机号筛选
        if (isset($param['keywords']) && strlen($param['keywords']) == 11) {
            $mobile = $param['keywords'];
            $member = $member->where('id', 'in', function ($query) use ($mobile) {
                $query->table('tk_mobile')->where('mobile', 'like', "%{$mobile}%")->field('member_id');
            });

        } else if (isset($param['keywords']) && strlen($param['keywords']) < 11) {
            $mobile = $param['keywords'];
            $member = $member->where('id', 'in', function ($query) use ($mobile) {
                $query->table('tk_mobile')->where('mobile', 'like', "%{$mobile}%")->field('member_id');
            });
        } else {
            $map[] = ['active_status', 'not in', [3, 4]];
            $member = $member->where($map);
        }
        $list = $member->order('create_time desc')->paginate($param['limit'], false, $config);

        foreach ($list as &$value) {
            $value['mobile'] = substr_replace($value['mobile'], '***', 3, 3);
            $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";

        }
        $result = [
            'code' => '200',
            'msg' => '获取数据成功',
            'data' => [
                'sealist' => $list->getCollection()
            ]
        ];

        return json($result);
    }

    # 客资申请
    public function doApply()
    {
        $ids = $this->request->param('ids');
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return json([
                'code' => '500',
                'msg' => '申请的客资不能为空'
            ]);
        }

        $count = count($ids);
        $success = 0;
        foreach ($ids as $id) {
            $allocate = MemberAllocate::getAllocate($this->user['id'], $id);
            if (!empty($allocate)) continue;

            $model = new MemberApply();
            $map = [];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['member_id', '=', $id];
            $map[] = ['apply_status', '=', 0];
            $apply = $model->where($map)->find();
            if (!empty($apply)) continue;

            $data['user_id'] = $this->user['id'];
            $data['member_id'] = $id;
            $data['apply_status'] = 0;
            $data['create_time'] = time();
            $res = $model->insert($data);
            if ($res) $success = $success + 1;
        }

        // $fail = $count - $success;
        if ($success > 0) {
            $result = [
                'code' => '200',
                'msg' => "申请成功"
            ];
        } else {
            $result = [
                'code' => '200',
                'msg' => "已经有此客资"
            ];
        }

        return json($result);
    }

    public function searchAllocate()
    {
        $post = Request::param();
        $mobile = $post['mobile'];
        $member = new Member();
        $list = $member->where('id', 'in', function ($query) use ($mobile) {
            $query->table('tk_mobile')->where('mobile', 'like', "%{$mobile}%")->field('member_id');
        })->select();

        if (!$list->isEmpty()) {
            $list = $list->toArray();
            $data = $list[0];
            $data['allocate_type'] = 1;
            MemberAllocate::searchAllocateData($this->user['id'], $data['id'], $data);

            $response = [
                'code' => 200,
                'msg' => '获取成功',
                'data' => $data['id']
            ];
        } else {
            $response = [
                'code' => 400,
                'msg' => '获取客资失败',
                'data' => []
            ];
        }

        return xjson($response);
    }
}
