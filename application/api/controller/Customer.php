<?php
namespace app\api\controller;


use app\common\model\Allocate;
use app\common\model\Budget;
use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\MemberApply;
use app\common\model\MemberVisit;
use app\common\model\MobileRelation;
use app\common\model\OperateLog;
use app\common\model\Region;
use app\common\model\Scale;
use app\common\model\Search;
use app\common\model\Source;
use app\common\model\Store;
use app\common\model\User;
use app\common\model\UserAuth;
use think\facade\Request;

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

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->sources = Source::getSources();
        $this->hotels = Store::getStoreList();
        $this->status = Intention::getIntentions();
        $this->auth = UserAuth::getUserLogicAuth($this->user['id']);
        $this->budgets = Budget::getBudgetList();
        $this->scales = Scale::getScaleList();
    }

    public function sea()
    {
        $get = Request::param();
        $get['limit'] = $get['limit'] ? $get['limit'] : 30;
        $get['page'] = isset($get['page']) ? $get['page'] + 1 : 1;
        $config = [
            'page' => $get['page']
        ];

        if(isset($get['status']) && $get['status'] >= 0) {
            $map[] = ['active_status', '=', $get['status']];
        }

        $map[] = ['is_sea', '=', '1'];
        if (isset($get['source']) && $get['source'] > 0) {
            $map[] = ['source_id', '=', $get['source']];
        }

        if (isset($get['staff']) && $get['staff'] > 0) {
            $map[] = ['operate_id', '=', $get['staff']];
        }

        $map[] = ['active_status', 'not in', [3,4]];
        ### 省市划分
        if($this->user['city_id'] > 0) {
            $map[] = ['city_id', '=', $this->user['city_id']];
        }

        if (isset($get['date_range']) && !empty($get['date_range'])) {
            $range = explode('~', $get['date_range']);
            $range[0] = trim($range[0]);
            $range[1] = trim($range[1]);
            $start = strtotime($range[0]);
            $end = strtotime($range[1]);
            $map[] = ['create_time', 'between', [$start, $end]];
        }

        if (isset($get['create_time']) && $get['create_time'] == 'today') {
            $start = strtotime(date('Y-m-d'));
            $end = strtotime('tomorrow');
            $map[] = ['create_time', 'between', [$start, $end]];
        } else if(isset($get['create_time']) && !empty($get['create_time'])){
            $range = explode('~', $get['create_time']);
            $range[0] = trim($range[0]);
            $range[1] = trim($range[1]);
            $start = strtotime($range[0]);
            $end = strtotime($range[1]);
            $map[] = ['create_time', 'between', [$start, $end]];
        }

        if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
            $map = [];
            $mobiles = MobileRelation::getMobiles($get['keywords']);
            if (!empty($mobiles)) {
                $map[] = ['mobile', 'in', $mobiles];
            } else {
                $map[] = ['mobile', '=', $get['keywords']];
            }
            $where[] = ['mobile', 'like', "%{$get['keywords']}%"];
            $list = model('Member')->where($map)->whereOr($where)->order('create_time desc')->paginate($get['limit'], false, $config);
        } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
            $map = [];
            $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
            $list = model('Member')->where($map)->order('create_time desc')->paginate($get['limit'], false, $config);
        } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
            $map = [];
            $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
            $list = model('Member')->where($map)->order('create_time desc')->paginate($get['limit'], false, $config);
        } else {
            $list = model('Member')->where($map)->where('id', 'not in', function ($query) {
                $map = [];
                $map[] = ['user_id', '=', $this->user['id']];
                $query->table('tk_member_allocate')->where($map)->field('member_id');
            })->order('create_time desc')->paginate($get['limit'], false, $config);
        }

        $data = $list->getCollection()->toArray();
        $users = User::getUsers();
        foreach ($data as &$value) {
            $value['operator'] = $users[$value['operate_id']]['realname'];
            $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
            $value['news_type'] = $this->newsTypes[$value['news_type']];
            $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";

            if($this->auth['is_show_alias'] == '1') {
                $value['source_text'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
            } else {
                $value['source_text'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
            }
        }

        if(empty($data)) {
            $result = [
                'code' => -1,
                'msg' => '已经到底了',
                'pages' => $list->lastPage(),
                'count' => $list->total(),
                'data' => $data,
                'map' => $map
            ];
        } else {
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'pages' => $list->lastPage(),
                'count' => $list->total(),
                'data' => $data,
                'map' => $map
            ];
        }

        return xjson($result);
    }

    public function sea_without_mine()
    {
        $get = Request::param();
        $get['limit'] = $get['limit'] ? $get['limit'] : 100;
        $config = [
            'page' => $get['page']
        ];

        if(isset($get['status'])) {
            $map[] = ['active_status', '=', $get['status']];
        }

        $map[] = ['is_sea', '=', '1'];
        if (isset($get['source']) && $get['source'] > 0) {
            $map[] = ['source_id', '=', $get['source']];
        }

        if (isset($get['staff']) && $get['staff'] > 0) {
            $map[] = ['user_id', '=', $get['staff']];
        }

        $map[] = ['active_status', 'not in', [3,4]];
        ### 省市划分
        if($this->user['city_id'] > 0) {
            $map[] = ['city_id', '=', $this->user['city_id']];
        }

        if (isset($get['date_range']) && !empty($get['date_range'])) {
            $range = explode('~', $get['date_range']);
            $range[0] = trim($range[0]);
            $range[1] = trim($range[1]);
            $start = strtotime($range[0]);
            $end = strtotime($range[1]);
            $map[] = ['create_time', 'between', [$start, $end]];
        }

        if (isset($get['create_time']) && $get['create_time'] == 'today') {
            $start = strtotime(date('Y-m-d'));
            $end = strtotime('tomorrow');
            $map[] = ['create_time', 'between', [$start, $end]];
        } else if(isset($get['create_time'])){
            $range = explode('~', $get['create_time']);
            $range[0] = trim($range[0]);
            $range[1] = trim($range[1]);
            $start = strtotime($range[0]);
            $end = strtotime($range[1]);
            $map[] = ['create_time', 'between', [$start, $end]];
        }

        if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
            $map = [];
            $mobiles = MobileRelation::getMobiles($get['keywords']);
            if (!empty($mobiles)) {
                $map[] = ['mobile', 'in', $mobiles];
            } else {
                $map[] = ['mobile', '=', $get['keywords']];
            }
            $where1[] = ['mobile', 'like', "%{$get['keywords']}%"];
            $where2[] = ['mobile1', 'like', "%{$get['keywords']}%"];
            $list = model('Member')->where($map)->whereOr($where1)->whereOr($where2)->order('create_time desc')->paginate($get['limit'], false, $config);
        } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
            $map = [];
            $mobiles = MobileRelation::getLikeMobiles($get['keywords']);
            if(!empty($mobiles)) {
                $map[] = ['mobile', 'in', $mobiles];
            }
            $where[] = ['mobile', 'like', "%{$get['keywords']}%"];
            $where1[] = ['mobile1', 'like', "%{$get['keywords']}%"];
            $list = model('Member')->where($map)->whereOr($where)->whereOr($where1)->order('create_time desc')->paginate($get['limit'], false, $config);
        } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
            $map = [];
            $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
            $list = model('Member')->where($map)->order('create_time desc')->paginate($get['limit'], false, $config);
        } else {

            $list = model('Member')->where($map)->where('id', 'not in', function ($query) {
                $map = [];
                $map[] = ['user_id', '=', $this->user['id']];
                $query->table('tk_member_allocate')->where($map)->field('member_id');
            })->order('create_time desc')->paginate($get['limit'], false, $config);
        }

        $data = $list->getCollection()->toArray();
        if(isset($get['keywords']) && strlen($get['keywords']) == 11) {
            if(isset($data[0]) && !empty($data[0])) {
                $data[0]['active_status'] = 0;
                MemberAllocate::searchAllocateData($this->user['id'], $data[0]['id'], $data[0]);
            }
        }

        $users = User::getUsers();
        foreach ($data as &$value) {
            $value['operator'] = $users[$value['operate_id']]['realname'];
            $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
            $value['news_type'] = $this->newsTypes[$value['news_type']];
            $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";

            if($this->auth['is_show_alias'] == '1') {
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
            'data' => $data
        ];

        return xjson($result);
    }

    public function mine()
    {
        $get = Request::param();
        $get['limit'] = isset($get['limit']) ? $get['limit'] : 30;
        $get['page'] = isset($get['page']) ? $get['page'] + 1 : 1;
        $config = [
            'page' => $get['page']
        ];
        $map = Search::customerMine($this->user, $get);
        isset($get['keywords']) && $get['keywords'] = trim($get['keywords']);
        if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
            $mobiles = MobileRelation::getMobiles($get['keywords']);
            if(!empty($mobiles)) {
                $map[] = ['mobile', 'in', $mobiles];
            } else {
                $map[] = ['mobile', '=', $get['keywords']];
            }
        } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
            $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
        } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
            $map[] = ['mobile', '=', $get['keywords']];
        }

        $list = model('MemberAllocate')->where($map)->order('create_time desc,member_create_time desc')->paginate($get['limit'], false, $config);
        if(!empty($list)) {
            $users = User::getUsers();
            $data = $list->getCollection()->toArray();

            foreach ($data as &$value) {
                $value['operator'] = $users[$value['operate_id']]['realname'];
                $value['user_realname'] = $users[$value['user_id']]['realname'];
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                $value['allocate_time'] = $value['create_time'];
                if($value['member_create_time']>0) {
                    $value['member_create_time'] = date('Y-m-d H:i', $value['member_create_time']);
                } else {
                    $value['member_create_time'] = $value['create_time'];
                }

                if($value['member_id'] > 0) {
                    $memberObj = Member::get($value['member_id']);
                    $value['visit_amount'] = $memberObj->visit_amount;
                    if($this->auth['is_show_alias'] == '1') {
                        $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                    } else {
                        $value['source_text'] = $this->sources[$memberObj->source_id] ? $this->sources[$memberObj->source_id]['title'] : $memberObj->source_text;
                    }
                }
            }

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data,
                'map' => $map
            ];
        } else {

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'data' => []
            ];
        }

        return xjson($result);
    }

    public function apply()
    {
        $get = Request::param();
        $get['limit'] = $get['limit'] ? $get['limit'] : 60;
        $config = [
            'page' => $this->params['page']
        ];

        $map[] = ['user_id', '=', $this->user['id']];
        if (isset($this->params['status'])) {
            $map[] = ['apply_status', '=', $this->params['status']];
        }

        $list = model('MemberApply')->where($map)->with('member')->order('create_time desc')->paginate($this->params['limit'], false, $config);
        if (!empty($list)) {
            $data = $list->getCollection()->toArray();
            foreach ($data as &$value) {
                $member = $value['member'];
                if (empty($member)) continue;
                unset($member['id']);
                unset($value['member']);
                $value = array_merge($value, $member);
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['hotel_id'] = $this->hotels[$value['hotel_id']]['title'];
                if ($this->auth['is_show_alias'] == '1') {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                } else {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                }
            }

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data
            ];
        } else {
            $result = [
                'code' => 0,
                'msg' => '已经到底了',
            ];
        }
        return xjson($result);
    }

    public function doApply()
    {
        $ids = $this->params['ids'];
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

            $Model = new MemberApply();
            $map = [];
            $map[] = ['user_id', '=', $this->user['id']];
            $map[] = ['member_id', '=', $id];
            $map[] = ['apply_status', '=', 0];
            $apply = $Model->where($map)->find();
            if (!empty($apply)) continue;

            $data['user_id'] = $this->user['id'];
            $data['member_id'] = $id;
            $data['apply_status'] = 0;
            $data['create_time'] = time();
            $res = $Model->insert($data);
            if ($res) $success = $success + 1;
        }

        $fail = $count - $success;
        return xjson([
            'code' => '200',
            'msg' => "申请成功{$success}条,失败{$fail}条"
        ]);
    }

    public function getBaseData()
    {
        $cities = Region::getCityList(0);
        $cities = array_values($cities);
        if (!empty($this->user['city_id'])) {
            $city_id = $this->user['city_id'];
        } else {
            $city_id = $cities[0]['id'];
        }
        foreach ($cities as $key => $value) {
            if ($value['id'] == $city_id) {
                $city_index = $key;
                break;
            }
        }

        $staffes = User::getUsersInfoByDepartmentId($this->user['department_id'], false);
        $staff = [
            'id' => 'all',
            'realname' => '所有员工'
        ];
        array_unshift($staffes, $staff);

        $areas = Region::getAreaList($city_id);
        $areas = array_values($areas);
        $sources = array_values($this->sources);
        $source = ['id'=>0,'title'=>'所有来源'];
        array_unshift($sources, $source);

        $statuses = $this->status;
        $statuses = array_values($statuses);
        $status = ['id'=>-1,'title'=>'所有状态'];
        array_unshift($statuses, $status);
        $result = [
            'code' => 0,
            'msg' => '已经到底了',
            'data' => [
                'news_types' => $this->newsTypes,
                'sources' => $sources,
                'city_id' => $city_id,
                'cities' => $cities,
                'areas' => $areas,
                'city_index' => $city_index,
                'statuses' => $statuses,
                'staffes' => $staffes
            ]
        ];
        return xjson($result);
    }

    public function createCustomer()
    {
        $post = Request::param();
        if (empty($post['source_id'])) {
            return xjson([
                'code' => '400',
                'msg' => '请选择渠道',
            ]);
        }

        if (empty($post['mobile'])) {
            return xjson([
                'code' => '400',
                'msg' => '手机号不能为空',
            ]);
        }

        if (empty($post['realname'])) {
            return xjson([
                'code' => '400',
                'msg' => '客户姓名不能为空',
            ]);
        }


        $post['mobile'] = trim($post['mobile']);
        $len = strlen($post['mobile']);
        if ($len != 11) {
            return xjson([
                'code' => '400',
                'msg' => '请输入正确的手机号',
            ]);
        }

        $post['mobile1'] = trim($post['mobile1']);
        $len = strlen($post['mobile1']);
        if (!empty($post['mobile1']) && $len != 11) {
            return xjson([
                'code' => '400',
                'msg' => '请输入正确的其他手机号',
            ]);
        }

        $Model = new Member();
        $Model->member_no = date('YmdHis') . rand(100, 999);
        ### 验证手机号唯一性
        if(empty($post['mobile1'])) {
            $originMember = $Model::checkMobile($post['mobile']);
            if ($originMember) {
                return xjson([
                    'code' => '400',
                    'msg' => $post['mobile'] . '手机号已经存在',
                ]);
            }
        } else {
            $originMember1 = $Model::checkFromMobileSet($post['mobile'], false);
            $originMember2 = $Model::checkFromMobileSet($post['mobile1'], false);

            if ($originMember1 || $originMember2) {
                return xjson([
                    'code' => '400',
                    'msg' => $post['mobile'] . '手机号已经存在',
                ]);
            }
        }


        ### 同步来源名称
        if (isset($post['source_id']) && $post['source_id'] > 0) {
            $post['source_text'] = $this->sources[$post['source_id']]['title'];
            $Model->source_text = $this->sources[$post['source_id']]['title'];
        }
        ### 基本信息入库
        $Model->is_sea = 1;
        unset($post['token']);
        if(in_array($this->user['role_id'], [5,6,8,26])) {
            $post['add_source'] = 1; // 代表来源登陆手机端，会进入派单组公海
        }
        $Model->operate_id = $this->user['id'];
        $result1 = $Model->save($post);

        ### 新添加客资要加入到分配列表中
        $post['operate_id'] = $this->user['id'];
        MemberAllocate::insertAllocateData($this->user['id'], $Model->id, $post);
        if ($result1) {
            $memberId = $Model->getLastInsID();
            $mobileModel = new Mobile();
            $mobileModel->insert(['mobile'=>$post['mobile'],'member_id'=>$memberId]);

            ### 将手机号1添加到手机号库
            if(!empty($post['mobile1'])) {
                $mobileModel->insert(['mobile'=>$post['mobile1'], 'member_id'=>$memberId]);
            }

            ### 添加操作记录
            OperateLog::appendTo($Model);
            if (isset($Allocate)) OperateLog::appendTo($Allocate);
            $result = xjson(['code' => 0, 'msg' => '添加客资成功']);
        } else {
            $result = xjson(['code' => '500', 'msg' => '添加客资失败, 请重试']);
        }

        return $result;
    }

    public function editCustomer()
    {

        $post = Request::param();
        $Model = Member::get($post['id']);
        $result2 = MemberAllocate::updateAllocateData($this->user['id'], $Model->id, $post);
        if ($result2) {
            ### 添加操作记录
            OperateLog::appendTo($Model);
            if (isset($Allocate)) OperateLog::appendTo($Allocate);
            return xjson(['code' => 0, 'msg' => '编辑客户信息成功']);
        } else {
            return xjson(['code' => 500, 'msg' => '编辑客户信息失败, 请重试']);
        }
    }

    public function getCustomer()
    {
        $post = Request::param();
        $data = Member::get($post['id'])->toArray();
        return xjson(['code' => 200, 'msg' => '获取用户信息成功', 'data' => $data]);
    }

    /**
     * 今日跟进
     * @return mixed
     */
    public function today()
    {
        $get = Request::param();
        $get['limit'] = isset($get['limit']) ? $get['limit'] : 30;
        $get['page'] = isset($get['page']) ? $get['page'] + 1 : 1;
        $config = [
            'page' => $get['page']
        ];
        $map = Search::customerMine($this->user, $get);

        if (!isset($get['next_visit_time']) || empty($get['next_visit_time'])) {
            $tomorrow = strtotime('tomorrow');
            $map[] = ['next_visit_time', '>', 0];
            $map[] = ['next_visit_time', '<', $tomorrow];
        }

        $map[] = ['active_status', 'not in', [3,4]];

        if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
            $map = [];
            $mobiles = MobileRelation::getMobiles($get['keywords']);
            if (!empty($mobiles)) {
                $map[] = ['mobile', 'in', $mobiles];
            } else {
                $map[] = ['mobile', '=', $get['keywords']];
            }
            $list = model('MemberAllocate')::hasWhere('member', $map, "Member.*")->order('id desc')->paginate($get['limit'], false, $config);
        } else if (isset($get['keywords']) && !empty($get['keywords']) && strlen($get['keywords']) < 11) {
            $map = [];
            $map[] = ['mobile', 'like', "%{$get['keywords']}%"];
            $list = model('MemberAllocate')::hasWhere('member', $map, 'Member.*')->order('id desc')->with('member')->paginate($get['limit'], false, $config);
        } else if (isset($get['keywords']) && strlen($get['keywords']) > 11) {
            $map = [];
            $map[] = ['mobile', '=', $get['keywords']];
        } else {
            $list = model('MemberAllocate')->where($map)->order('next_visit_time desc')->paginate($get['limit'], false, $config);
        }

        if (!empty($list)) {
            $users = User::getUsers();
            $data = $list->getCollection()->toArray();
            if (isset($get['keywords']) && strlen($get['keywords']) == 11) {
                if (isset($data[0]) && !empty($data[0])) {
                    $data[0]['active_status'] = 0;
                    MemberAllocate::updateAllocateData($this->user['id'], $data[0]['id'], $data[0]);
                }
            }

            foreach ($data as &$value) {
                $value['next_visit_time'] = date('Y-m-d H:i', $value['next_visit_time']);
                $value['allocate_time'] = $value['create_time'];
                $value['user_realname'] = $users[$value['user_id']]['realname'];
                if (!empty($get['keywords'])) {
                    $value['member_id'] = $value['id'];
                }
                if (empty($value['member'])) {
                    $memberObj = Member::get($value['member_id']);
                    if ($memberObj) {
                        $member = $memberObj->getData();
                    } else {
                        $member = [];
                    }
                } else {
                    $member = $value['member'];
                    unset($value['member']);
                }

                if (empty($member)) continue;

                $member['member_id'] = $member['id'];
                unset($member['id']);
                $value = array_merge($value, $member);
                $value['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
                $value['news_type'] = $this->newsTypes[$value['news_type']];
                $value['hotel_id'] = $this->hotels[$value['hotel_id']]['title'];
                $value['wedding_date'] = substr($value['wedding_date'], 0, 10);
                $len = strlen($value['budget']);
                if ($len < 2) {
                    $value['budget'] = isset($this->budgets[$value['budget']]) ? $this->budgets[$value['budget']]['title'] : $value['budget'];
                }

                $len = strlen($value['banquet_size']);
                if ($len < 2) {
                    $value['banquet_size'] = isset($this->scales[$value['banquet_size']]) ? $this->scales[$value['banquet_size']]['title'] : $value['banquet_size'];
                }
                $value['intention_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                $value['active_status'] = $value['active_status'] ? $this->status[$value['active_status']]['title'] : "未跟进";
                if ($this->auth['is_show_alias'] == '1') {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['alias'] : $value['source_text'];
                } else {
                    $value['source_id'] = $this->sources[$value['source_id']] ? $this->sources[$value['source_id']]['title'] : $value['source_text'];
                }
                $value['create_time'] = date('Y-m-d H:i', $member['create_time']);
            }
            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => $list->total(),
                'data' => $data
            ];

        } else {

            $result = [
                'code' => 0,
                'msg' => '获取数据成功',
                'count' => 0,
                'data' => []
            ];
        }

        return xjson($result);
    }

    public function searchAllocate()
    {
        $post = Request::param();
        $member = Member::getByMobile($post['mobile']);
        if (empty($member)) {
            $mobiles = MobileRelation::getMobiles($post['mobile']);
            if (!empty($mobiles)) {
                $map[] = ['mobile', 'in', $mobiles];
            } else {
                $map[] = ['mobile', '=', $post['mobile']];
            }
            $where[] = ['mobile', 'like', "%{$post['mobile']}%"];
            $list = model('Member')->where($map)->whereOr($where)->order('create_time desc')->select();
            if(!empty($list)) {
                $data = $list->getData();
                $memberId = $data[0]['id'];
                $data[0]['allocate_type'] = 1;
                $result = MemberAllocate::searchAllocateData($this->user['id'], $memberId, $data[0]);
            } else {
                $result = 0;
            }
        } else {
            $data = $member->getData();
            $data['allocate_type'] = 1;
            $result = MemberAllocate::searchAllocateData($this->user['id'], $member->id, $data);
            $memberId = $member->id;
        }

        if ($result) {
            $response = [
                'code' => 0,
                'msg' => '获取成功',
                'count' => 0,
                'data' => $memberId
            ];
        } else {
            $response = [
                'code' => -1,
                'msg' => '获取客资失败',
                'count' => 0,
                'data' => []
            ];
        }

        return xjson($response);
    }
}