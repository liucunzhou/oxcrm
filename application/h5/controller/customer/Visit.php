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
use app\common\model\Search;
use think\response\Json;

class Visit extends Base
{
    protected $newsTypes = ['婚宴信息', '婚庆信息', '一站式'];
    protected $washStatus = [];
    protected $hotels = [];
    protected $sources = [];
    protected $statusList = [];
    protected $auth = [];
    protected $budgets = [];
    protected $scales = [];
    protected $model = null;
    protected $memberModel = null;

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->sources = Source::getSources();
        $this->hotels = Store::getStoreList();
        $this->statusList = Intention::getIntentions();
        // $this->auth = UserAuth::getUserLogicAuth($this->user['id']);

        $this->model = new MemberVisit();
        $this->memberModel = new Member();
    }


    /**
     * 回访详情
     */
    public function detail() {
        $request = $this->request->param();

        ### 获取用户基本信息
        $allocate = MemberAllocate::get($request['allocate_id']);

        if (empty($allocate)) {
            $result = [
                'code'  => '200',
                'msg'   => '回访记录',
                'data'  => ''
            ];
            return json($result);
        }

        $map = [];
        $map[] = ['member_id','=',$allocate['member_id']];
        $field = "";
        ### 根据分配表的数据查询回访记录
        $list = $this->model->where($map)->field($field)->order('create_time desc')->select();
        $data = [];
        foreach ($list as $key=>$value) {
            $row = [];
            $row['status'] = $this->statusList[$value['status']]['title'];
            $row['user'] = '';
            $data[] = $row;
        }

        $result = [
            'code'  => '200',
            'msg'   => '回访记录',
            'data'  => $data
        ];

        return json($result);
    }
}
