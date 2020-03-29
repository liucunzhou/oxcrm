<?php

namespace app\wash\controller\customer;

use app\common\model\CallRecord;
use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\MemberHotelAllocate;
use app\common\model\MemberVisit;
use app\common\model\Region;
use app\common\model\Store;
use app\common\model\User;
use app\wash\controller\Backend;
use think\process\exception\Timeout;
use think\Request;

class Customer extends Backend
{
    protected $customerModel;
    protected $regionModel;
    protected $levels = [];
    protected $stores = [];

    protected function initialize()
    {
        parent::initialize();

        $this->model = new \app\common\model\MemberAllocate();
        $this->customerModel = new Member();

        $this->levels = [
            999 => [
                'title' => '非常重要',
                'btn'   => 'btn-danger'
            ],
            998 => [
                'title' => '重要',
                'btn'   => 'btn-warning'
            ],
            997 => [
                'title' => '一般',
                'btn'   => 'btn-primary'
            ],
            0   => [
                'title' => '无',
                'btn'   => 'btn-info'
            ]
        ];
        $this->assign('levels', $this->levels);

        $where = [];
        $this->stores = Store::where($where)->column('id,title,sort', 'id');
        $this->assign('stores', $this->stores);

        $users = User::getUsers();
        $this->assign('users', $users);

        $allStatus = Intention::column('id,title');
        $this->assign('allStatus', $allStatus);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $params = $this->request->param();
        if(isset($params['status'])) {
            $status = $params['status'];
        } else {
            $status = 0;
        }

        // 获取洗单组的意向列表
        $where = [];
        $where[] = ['type', 'in', ['wash', 'other']];
        $fields = 'id,title,type';
        $tabs = \app\common\model\Intention::where($where)->field($fields)->order('sort,id')->column($fields, 'id');
        $unvisit = [
            'id'    => 0,
            'title' => '未跟进',
            'type'  => 'wash'
        ];
        array_unshift($tabs, $unvisit);

        $all = [
            'id'    => -1,
            'title' => '全部客资',
            'type'  => 'wash'
        ];
        array_unshift($tabs, $all);

        foreach($tabs as $key=>&$row) {
            // 检测当前
            if ($key == $status) {
                $row['active'] = 1;
            } else {
                $row['active'] = 2;
            }

            $params['status'] = $key;
            $row['url'] = url('customer.customer/index', $params);

            $map = [];
            // 检测所有
            if ($key != 0) {
                $map['active_status'] = $row['id'];
                $total = $this->model->where($map)->count();
            } else {
                $total = $this->model->count(); 
            }
            $row['total'] = $total;
        }
        $this->assign('tabs', $tabs);


        $list = $this->model->order('id desc')->paginate(15, $config);
        foreach ($list as $key=>&$row) {
            $member = \app\api\model\Member::get($row->member_id);
            $row->visit_amount = $member->visit_amount;
        }
        $this->assign('list', $list);

        return $this->fetch();
    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
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

        // 获取省市列表
        $provinceList = Region::getProvinceList();
        $this->assign('provinceList', $provinceList);

        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
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
        $member->mobile = substr_replace($member->mobile, '***', 3, 3);
        $this->assign('member', $member);

        // 计算信息类型
        if (empty($member->news_types)) {
            $member->news_types = $member->news_type;
        }
        $selectNewsTypes = empty($member->news_types) ? [] : explode(',', $member->news_types);
        $this->assign("selectNewsTypes", $selectNewsTypes);

        // 获取省市列表
        // $provinceList = Region::getProvinceList();
        // $this->assign('provinceList', $provinceList);
        // 获取当前省份的城市列表
        $fields = 'id,pid,shortname,name,level,pinyin,code,zip_code';
        $currentCityList = [
            '802'   => '上海市',
            '1965'  => '广州市',
            '934'   => '杭州市',
            '861'   => '苏州市'
        ];
        $currentCityIds = array_keys($currentCityList);
        $where = [];
        $where[]    = ['id', 'in', $currentCityIds];
        $cityList = Region::where($where)->field($fields)->select();
        $this->assign('cityList', $cityList);
        // 获取当前城市的区县列表
        $where = [];
        $where['pid'] = $allocate->city_id;
        $areaList = Region::where($where)->field($fields)->select();
        $this->assign('areaList', $areaList);
        // 获取已选区县列表
        if(!empty($allocate->zone)) {
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
        foreach ($visits as $key=>$row) {
            // 获取该员工该客资的分配信息
            $userId = $row->user_id;
            if(!isset($visitGroup[$userId])) {
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
        $where['fwdDstNum'] = '+86'.$member->mobile;
        $callRecord = new CallRecord();
        $records = $callRecord->where($where)->order('fwdStartTime desc')->select();
        $this->assign('records', $records);
        return $this->fetch();
    }

    public function doUpdate()
    {
        $params = $this->request->param();
        // $where = [];
        // $where['id'] = $params['']
        $member = \app\api\model\Member::get($params['member_id']);
        $allocate = MemberAllocate::get($params['allocate_id']);

        $params['update_time'] = time();
        // print_r($params['news_types']);
        $params['news_types'] = empty($params['news_types']) ? '' : implode(',', $params['news_types']);
        $result = $member->allowField(true)->save($params);;
        $allocate->allowField(true)->save($params);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
