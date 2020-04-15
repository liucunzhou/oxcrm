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

    protected function initialize()
    {
        parent::initialize();
        // 获取系统来源,酒店列表,意向状态
        $this->sources = Source::getSources();
        $this->hotels = Store::getStoreList();
        $this->statusList = Intention::getIntentions();
        // $this->auth = UserAuth::getUserLogicAuth($this->user['id']);

        $this->model = new MemberVisit();
        $this->Membermodel = new Member();
    }


    /**
     * 回访详情
     */
    public function detail() {
        $request = $this->request->param();
        $users = User::getUsers();

        $map = [];
        $map[] = ['member_id', '=', $request['member_id']];
        $list = $this->model->where($map)->order('create_time desc')->select();
        foreach ($list as $key=>&$value) {
            $userId = $value->user_id;
            $status = $value->status;
            $value['level'] = '重要客户';
            $value['user_id'] = $users[$userId]['realname'];
            $value['status'] = $this->statusList[$status]['title'];
        }

        $result = [
            'code'  => '200',
            'msg'   => '回访记录',
            'data'  => $list
        ];
        return json($result);
    }
}
