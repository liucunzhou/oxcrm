<?php

namespace app\wash\controller\customer;

use app\common\model\CallRecord;
use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\MemberApply;
use app\common\model\MemberHotelAllocate;
use app\common\model\MemberVisit;
use app\common\model\Mobile;
use app\common\model\MobileRelation;
use app\common\model\OperateLog;
use app\common\model\Region;
use app\common\model\Store;
use app\common\model\User;
use app\wash\controller\Backend;
use think\Request;

class Customer extends Backend
{
    protected $customerModel;
    protected $regionModel;
    protected $levels = [];
    protected $stores = [];
    protected $sources = [];
    protected $staffs = [];

    protected function initialize()
    {
        parent::initialize();

        ### 实例需要使用的model
        $this->model = new \app\common\model\MemberAllocate();
        $this->customerModel = new Member();

        ### 客资来源
        $this->sources = \app\common\model\Source::getSources();
        $this->assign('sources', $this->sources);

        // 门店列表
        $where = [];
        $this->stores = Store::where($where)->column('id,title,sort', 'id');
        $this->assign('stores', $this->stores);

        // 意向列表
        $allStatus = Intention::column('id,title,color');
        //echo "<pre>";
        // print_r($allStatus);
        // exit;
        $this->assign('allStatus', $allStatus);

        // 用户列表
        $users = User::getUsers();
        $this->assign('users', $users);

        $departmentId = $this->user['department_id'];
        $this->staffs = User::getUsersInfoByDepartmentId($departmentId, false);
        $this->assign('staffs', $this->staffs);

        // 获取城市列表
        $currentCityList = [
            '802' => '上海市',
            '1965' => '广州市',
            '934' => '杭州市',
            '861' => '苏州市'
        ];
        $currentCityIds = array_keys($currentCityList);
        $where = [];
        $where[] = ['id', 'in', $currentCityIds];
        $cityList = Region::where($where)->select();
        $this->assign('cityList', $cityList);
    }


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $params = $this->request->param();
        $where = [];
        if (isset($params['status']) && $params['status'] >=0) {
            $status = $params['status'];
            if (isset($params['status']) && $params['status'] >= 0) {
                $whereStatus = [];
                $whereStatus[] = ['active_status', '=', $params['status']];
            }
        } else  {
            $status = -1;
            $whereStatus = [];
        }

        if (isset($params['staff_id']) && !empty($params['staff_id']) && is_array($params['staff_id'])) {
            // 兼容下面url函数，在转会之前params['staff_id']是一个数组，无法转换成url
            $params['staff_id'] = implode(',', $params['staff_id']);
        }

        // 检索员工列表
        if (isset($params['staff_id']) && !empty($params['staff_id'])) {
            $where[] = ['user_id', 'in', explode(',', $params['staff_id'])];
        } else {
            $where[] = ['user_id', '=', $this->user['id']];
        }

        // 检索来源
        if (isset($params['source_id']) && !empty($params['source_id'])) {
            $where[] = ['source_id', '=', $params['source_id']];
        }

        // 检索分配方式
        if (isset($params['assign_type']) && !empty($params['assign_type'])) {
            $where[] = ['assign_type', '=', $params['assign_type']];
        }

        // 检索城市
        if (isset($params['city_id']) && !empty($params['city_id'])) {
            $where[] = ['city_id', '=', $params['city_id']];
        }

        // 检索回访数
        if (isset($params['visit_amount']) && $params['visit_amount'] >= 0) {
            $where[] = ['visit_amount', '=', $params['visit_amount']];
        }

        if (isset($params['mobile']) && strlen($params['mobile']) == 11) {
            $mobiles = MobileRelation::getMobiles($params['mobile']);
            if (!empty($mobiles)) {
                $where[] = ['mobile', 'in', $mobiles];
            } else {
                $where[] = ['mobile', 'like', "%{$params['mobile']}%"];
            }
        } else if (isset($params['mobile']) && !empty($params['mobile']) && strlen($params['mobile']) < 11) {
            $where[] = ['mobile', 'like', "%{$params['mobile']}%"];
        } else if (isset($get['mobile']) && strlen($params['mobile']) > 11) {
            $where[] = ['mobile', '=', $params['mobile']];
        }

        // 时间区间
        if (isset($params['range']) && stripos($params['range'], '~') > 0) {
            $arr = explode('~', $params['range']);
            if (count($arr) == 2) {
                $start = strtotime(trim($arr[0]));
                $end = strtotime(trim($arr[1]));
                $range = ['create_time', 'between', [$start, $end]];
                $where[] = $range;
            }
        } else {
            $range = [];
        }

        // 获取清洗组意向列表
        $tabs = Intention::getWash();
        foreach ($tabs as $key => &$row) {
            // 检测当前
            if ($row['id'] == $status) {
                $row['active'] = 1;
            } else {
                $row['active'] = 2;
            }
            $params['status'] = $row['id'];
            $row['url'] = url('/wash/customer.customer/index', $params, false);
            // 检测所有
            if ($key != 0) {
                $map = [];
                // 获取自己拥有的客资列表
                $map[] = ['active_status', '=', $row['id']];;
                $total = $this->model->where($where)->where($map)->count();

                if($row['id']==0 && $total > 0) {
                    $row['bg'] = 'bg-unvisited';
                } else {
                    $row['bg'] = '';
                }
            } else {
                // 获取自己拥有的客资列表
                $total = $this->model->where($where)->count();
                $row['bg'] = '';
            }
            $row['total'] = $total;
        }
        $this->assign('tabs', $tabs);
        $config = [
            'type' => 'bootstrap',
            'var_page' => 'page',
            'page' => $params['page'],
            'query' => request()->param()
        ];
        if (!isset($params['limit'])) $params['limit'] = 50;
        $list = $this->model->where($where)->where($whereStatus)->order('update_time asc,create_time desc')->paginate(50, false, $config);

        foreach ($list as $key => &$row) {
            $member = \app\api\model\Member::get($row->member_id);
            $row->visit_amount = $member->visit_amount;
            $row->remark = $member->remark;
        }
        $this->assign('list', $list);

        return $this->fetch();
    }

    public function today()
    {
        $params = $this->request->param();
        $staffIds = array_column($this->staffs, 'id');

        $where = [];
        if (isset($params['status']) && !empty($params['status'])) {
            $status = $params['status'];
            if (isset($params['status']) && $params['status'] >= 0) {
                $where[] = ['active_status', '=', $params['status']];
            }
        } else {
            $status = 0;
        }

        if (isset($params['staff_id']) && !empty($params['staff_id']) && is_array($params['staff_id'])) {
            // 兼容下面url函数，在转会之前params['staff_id']是一个数组，无法转换成url
            $params['staff_id'] = implode(',', $params['staff_id']);
        }

        // 检索员工列表
        if (isset($params['staff_id']) && !empty($params['staff_id'])) {
            $where[] = ['user_id', 'in', explode(',', $params['staff_id'])];
        } else {
            $where[] = ['user_id', '=', $this->user['id']];
        }

        // 检索来源
        if (isset($params['source_id']) && !empty($params['source_id'])) {
            $where[] = ['source_id', '=', $params['source_id']];
        }

        // 检索分配方式
        if (isset($params['assign_type']) && !empty($params['assign_type'])) {
            $where[] = ['assign_type', '=', $params['assign_type']];
        }

        // 检索城市
        if (isset($params['city_id']) && !empty($params['city_id'])) {
            $where[] = ['city_id', '=', $params['city_id']];
        }

        if (isset($params['mobile']) && strlen($params['mobile']) == 11) {
            $mobiles = MobileRelation::getMobiles($params['mobile']);
            if (!empty($mobiles)) {
                $where[] = ['mobile', 'in', $mobiles];
            } else {
                $where[] = ['mobile', 'like', "%{$params['mobile']}%"];
            }
        } else if (isset($params['mobile']) && !empty($params['mobile']) && strlen($params['mobile']) < 11) {
            $where[] = ['mobile', 'like', "%{$params['mobile']}%"];
        } else if (isset($get['mobile']) && strlen($params['mobile']) > 11) {
            $where[] = ['mobile', '=', $params['mobile']];
        }

        $start = strtotime('yesterday') + 86400;
        $end = strtotime('tomorrow');
        $where[] = ['create_time', 'between', [$start, $end]];

        // 获取清洗组意向列表
        $tabs = Intention::getWash();
        foreach ($tabs as $key => &$row) {
            // 检测当前
            if ($row['id'] == $status) {
                $row['active'] = 1;
            } else {
                $row['active'] = 2;
            }
            $params['status'] = $row['id'];
            $row['url'] = url('/wash/customer.customer/today', $params, false);
            // 检测所有
            if ($key != 0) {
                // 获取自己拥有的客资列表
                $where[] = ['active_status', '=', $row['id']];
                // !empty($range) && $map[] = $range;
                $total = $this->model->where($where)->count();
            } else {
                // 获取自己拥有的客资列表
                $total = $this->model->where($where)->count();
            }
            $row['total'] = $total;
        }
        $this->assign('tabs', $tabs);
        $config = [
            'type' => 'bootstrap',
            'var_page' => 'page',
            'page' => $params['page'],
            'query' => request()->param()
        ];
        if (!isset($params['limit'])) $params['limit'] = 50;
        $list = $this->model->where($where)->order('update_time asc,create_time desc')->paginate(50, false, $config);
        foreach ($list as $key => &$row) {
            $member = \app\api\model\Member::get($row->member_id);
            $row->visit_amount = $member->visit_amount;
            $row->remark = $member->remark;
        }
        $this->assign('list', $list);

        return $this->fetch();
    }

    public function sea()
    {
        $get = $this->request->param();
        if ($this->request->isAjax()) {
            $config = [
                'page' => $get['page']
            ];
            $map[] = ['is_sea', '=', '1'];
            if (isset($get['source']) && $get['source'] > 0) {
                $map[] = ['source_id', '=', $get['source']];
            }

            if (isset($get['staff']) && $get['staff'] > 0) {
                $map[] = ['operate_id', '=', $get['staff']];
            }

            if (isset($get['city_id']) && $get['city_id'] > 0) {
                $map[] = ['city_id', '=', $get['city_id']];
            }

            ###  默认隐藏失效、无效客资
            $map[] = ['active_status', 'not in', [3, 4]];
            if (isset($get['date_range']) && !empty($get['date_range'])) {
                $range = explode('~', $get['date_range']);
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

        } else {

            if (strlen($get['keywords']) == 11) {
                $this->assign('showGetEntireBtn', 1);
            } else {
                $this->assign('showGetEntireBtn', 2);
            }

            return $this->fetch();
        }
    }

    public function add()
    {
        ### 获取推荐人列表
        $recommenders = \app\common\model\Recommender::column('id,title', 'id');
        $this->assign('recommenders', $recommenders);

        ### 酒店列表
        $hotels = Store::getStoreList();
        $this->assign("hotels", $hotels);


        $cities = Region::getCityList(0);
        $this->assign('cities', $cities);

        $areas = [];
        if ($this->user['city_id']) {
            $areas = Region::getAreaList($this->user['city_id']);
        }
        $this->assign('areas', $areas);

        $data['area_id'] = [];
        $this->assign("data", $data);

        $action = 'add';
        $this->assign('action', $action);

        return $this->fetch();
    }

    public function doAdd()
    {
        $post = $this->request->param();

        if (empty($post['mobile'])) {
            return json([
                'code' => '400',
                'msg' => '手机号不能为空',
            ]);
        }

        if (empty($post['realname'])) {
            return json([
                'code' => '400',
                'msg' => '客户姓名不能为空',
            ]);
        }

        if (empty($post['source_id'])) {
            return xjson([
                'code' => '400',
                'msg' => '来源不能为空',
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
        if (!empty($post['mobile1']) && $len != 11) {
            return xjson([
                'code' => '401',
                'msg' => '请输入正确的其他手机号',
            ]);
        }

        $action = '添加客资';
        $Model = new \app\common\model\Member();
        $Model->member_no = date('YmdHis') . rand(100, 999);
        ### 验证手机号唯一性
        if (empty($post['mobile1'])) {
            $originMember = $Model::checkFromMobileSet($post['mobile'], false);
            if ($originMember) {
                return json([
                    'code' => '400',
                    'msg' => $post['mobile'] . '手机号已经存在',
                ]);
            }
        } else {
            $originMember1 = $Model::checkFromMobileSet($post['mobile'], false);
            $originMember2 = $Model::checkFromMobileSet($post['mobile1'], false);
            if ($originMember1 || $originMember2) {
                return json([
                    'code' => '400',
                    'msg' => $post['mobile'] . '手机号已经存在',
                ]);
            }
        }

        $Model->operate_id = $this->user['id'];
        if (in_array($this->user['role_id'], [5, 6, 8, 26])) {
            $post['add_source'] = 1;
        }

        ### 同步来源名称
        $sourceText = $this->sources[$post['source_id']]['title'];
        $Model->source_text = $sourceText;
        $post['source_text'] = $sourceText;

        ### 基本信息入库
        $Model->is_sea = 1;
        $result1 = $Model->save($post);
        ### 新添加客资要加入到分配列表中

        $post['allocate_type'] = 3;
        $post['operate_id'] = $this->user['id'];
        MemberAllocate::insertAllocateData($this->user['id'], $Model->id, $post);

        if ($result1) {
            ### 将手机号添加到手机号库
            $memberId = $Model->id;
            $mobileModel = new Mobile();
            $mobileModel->insert(['mobile' => $post['mobile'], 'member_id' => $memberId]);

            ### 将手机号1添加到手机号库
            if (!empty($post['mobile1'])) {
                $mobileModel->insert(['mobile' => $post['mobile1'], 'member_id' => $memberId]);
            }

            ### 添加操作记录
            OperateLog::appendTo($Model);
            if (isset($Allocate)) OperateLog::appendTo($Allocate);
            return json(['code' => '200', 'msg' => $action . '成功']);
        } else {
            return json(['code' => '500', 'msg' => $action . '失败, 请重试']);
        }
    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        // 获取分配信息
        $map = [];
        $map['id'] = $id;
        $allocate = $this->model->where($map)->find();
        $this->assign('allocate', $allocate);

        // 获取客户的基本信息
        $map = [];
        $map['id'] = $allocate->member_id;
        $member = $this->customerModel->where($map)->find();
        $this->assign('member', $member);

        // 计算信息类型
        if (empty($member->news_types)) {
            $member->news_types = $member->news_type;
        }
        $selectNewsTypes = empty($member->news_types) ? [] : explode(',', $member->news_types);
        $this->assign("selectNewsTypes", $selectNewsTypes);

        // 获取省市列表
        $provinceList = Region::getProvinceList();
        $this->assign('provinceList', $provinceList);

        // 获取当前城市的区县列表
        $fields = 'id,pid,shortname,name,level,pinyin,code,zip_code';
        $where = [];
        $where['pid'] = $allocate->city_id;
        $areaList = Region::where($where)->field($fields)->select();
        $this->assign('areaList', $areaList);
        // 获取已选区县列表
        if (!empty($allocate->zone)) {
            $zoneSelected = explode(',', $allocate->zone);
        } else {
            $zoneSelected = [];
        }
        $this->assign('zoneSelected', $zoneSelected);

        // 获取已选酒店
        $memberHotelSelected = new \app\common\model\MemberHotelSelected();
        $where = [];
        $where['allocate_id'] = $allocate->id;
        $selected = $memberHotelSelected->where($where)->order('create_time desc')->select();
        $this->assign('selected', $selected);

        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        // 获取分配信息
        $map = [];
        $map['id'] = $id;
        $allocate = $this->model->where($map)->find();
        $this->assign('allocate', $allocate);

        // 获取客户的基本信息
        $map = [];
        $map['id'] = $allocate->member_id;
        $member = $this->customerModel->where($map)->find();
        $mobile = $member->mobile;
        $member->mobile = substr_replace($member->mobile, '***', 3, 3);
        $this->assign('member', $member);

        // 计算信息类型
        if (empty($member->news_types)) {
            $member->news_types = $member->news_type;
        }
        $selectNewsTypes = empty($member->news_types) ? [$member->news_type] : explode(',', $member->news_types);
        $this->assign("selectNewsTypes", $selectNewsTypes);

        $mobiles = MobileRelation::getMobiles($mobile);
        $this->assign('mobiles', $mobiles);

        // 获取省市列表
        // $provinceList = Region::getProvinceList();
        // $this->assign('provinceList', $provinceList);
        // 获取当前省份的城市列表
        // 获取当前城市的区县列表
        $fields = 'id,pid,shortname,name,level,pinyin,code,zip_code';
        $where = [];
        $where['pid'] = $allocate->city_id;
        $areaList = Region::where($where)->field($fields)->select();
        $this->assign('areaList', $areaList);
        // 获取已选区县列表
        if (!empty($allocate->zone)) {
            $zoneSelected = explode(',', $allocate->zone);
        } else {
            $zoneSelected = [];
        }
        $this->assign('zoneSelected', $zoneSelected);

        // 获取已选酒店
        $memberHotelSelected = new \app\common\model\MemberHotelSelected();
        $where = [];
        $where['allocate_id'] = $allocate->id;
        $selected = $memberHotelSelected->where($where)->order('create_time desc')->select();
        $this->assign('selected', $selected);

        // 获取所有已分配的人员
        $where = [];
        $where['member_id'] = $allocate->member_id;
        $allocatedStaff = MemberHotelAllocate::where($where)->select();
        $this->assign('allocatedStaff', $allocatedStaff);

        // 获取回访记录
        $memberVisit = new MemberVisit();
        $where = [];
        $where['member_id'] = $member->id;
        $visits = $memberVisit->where($where)->order('create_time desc')->select();
        $visitGroup = [];
        foreach ($visits as $key => $row) {
            // 获取该员工该客资的分配信息
            $userId = $row->user_id;
            if (!isset($visitGroup[$userId])) {
                $where = [];
                $where['user_id'] = $row->user_id;
                $where['mobile'] = $member->mobile;
                $visitGroup[$userId] = [
                    'user_id' => $row->user_id,
                    'visit_times' => 1,
                    'create_time' => $allocate->create_time,
                    'active_status' => $allocate->active_status,
                    'next_visit_time' => $row->next_visit_time,
                    'last_visit_time' => $row->create_time
                ];
            } else {
                $visitGroup[$userId]['visit_times'] += 1;
            }
        }
        $this->assign('visitGroup', $visitGroup);
        $this->assign('visits', $visits);

        // 获取当前手机的接听记录
        $where = [];
        $where['fwdDstNum'] = '+86' . $mobile;
        $callRecord = new CallRecord();
        $records = $callRecord->where($where)->order('fwdStartTime desc')->select();
        $this->assign('records', $records);

        return $this->fetch();
    }

    public function doUpdate()
    {
        $params = $this->request->param();
        $member = \app\api\model\Member::get($params['member_id']);
        $allocate = MemberAllocate::get($params['allocate_id']);

        $params['update_time'] = time();
        $params['news_types'] = empty($params['news_types']) ? '' : implode(',', $params['news_types']);
        $member->allowField(true)->save($params);;
        $result = $allocate->allowField(true)->save($params);
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

    public function doApply()
    {
        $ids = $this->request->param("ids");
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
            if ($apply) continue;

            $data['user_id'] = $this->user['id'];
            $data['member_id'] = $id;
            $data['apply_status'] = 0;
            $data['create_time'] = time();
            $res = $Model->insert($data);
            if ($res) $success = $success + 1;
        }

        $fail = $count - $success;
        return json([
            'code' => '200',
            'msg' => "申请成功{$success}条,失败{$fail}条"
        ]);
    }

    public function checkMobile()
    {
        $post = $this->request->param();
        if (!isset($post['mobile'])) {
            return json([
                'code' => '500',
                'msg' => '请输入手机号'
            ]);
        }

        ### 手机号验证
        $len = strlen($post['mobile']);
        if ($len != 11) {
            return json([
                'code' => '501',
                'msg' => '请输入正确的手机号'
            ]);
        }

        ###  根据手机号获取用户信息
        $member = \app\api\model\Member::getByMobile($post['mobile']);
        if (!empty($member)) {

            return json([
                'code' => '501',
                'msg' => '号码已存在'
            ]);
        } else {

            return json([
                'code' => '200',
                'msg' => '恭喜，该号码验证通过'
            ]);
        }
    }
}
