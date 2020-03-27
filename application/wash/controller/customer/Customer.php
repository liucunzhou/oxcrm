<?php

namespace app\wash\controller\customer;

use app\common\model\CallRecord;
use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\MemberVisit;
use app\common\model\Region;
use app\common\model\Store;
use app\wash\controller\Backend;
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
            ]
        ];
        $this->assign('levels', $this->levels);

        $where = [];
        $this->stores = Store::where($where)->column('id,title,sort', 'id');
        $this->assign('stores', $this->stores);
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
        $intention = new Intention();
        $where['type'] = 'wash';
        $tabs = $intention->where($where)->order('sort desc')->column('id,title,type', 'id');
        $all = [
            'id'    => 0,
            'title' => '所有客资',
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
                $map['active_status'] = $status;
                $total = $this->model->where($map)->count();
            } else {
                $total = $this->model->count(); 
            }
            $row['total'] = $total;
        }
        $this->assign('tabs', $tabs);


        $list = $this->model->order('id desc')->paginate(15);
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
        $this->assign('member', $member);

        // 获取省市列表
        $provinceList = Region::getProvinceList();
        $this->assign('provinceList', $provinceList);
        // 获取当前省份的城市列表
        if(!empty($allocate->province_id)) {
            $cityList = Region::getCityList($allocate->province_id);
        } else {
            $cityList = [];
        }
        $this->assign('cityList', $cityList);


        // 获取已选酒店
        $memberHotelSelected = new \app\common\model\MemberHotelSelected();
        $where = [];
        $where['allocate_id'] = $allocate->id;
        $selected = $memberHotelSelected->where($where)->order('create_time desc')->select();
        $this->assign('selected', $selected);

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
                $callocate = MemberAllocate::where($where)->find();
                $visitGroup[$userId] = [
                    'user_id' => $row->user_id,
                    'visit_times' => $callocate->visit_amount,
                    'create_time' => $allocate->create_time,
                    'active_status' => $allocate->active_status,
                    'next_visit_time' => $row->next_visit_time,
                    'last_visit_time' => $row->create_time
                ];
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
