<?php

namespace app\h5\controller\customer;

use app\common\model\Budget;
use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\Notice;
use app\common\model\OperateLog;
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
        $this->statusList = array_values(Intention::getIntentions());
        // print_r($this->statusList);
        // $this->auth = UserAuth::getUserLogicAuth($this->user['id']);

        $this->model = new MemberVisit();
        $this->memberModel = new Member();
    }

    /**
     * 回访列表
     */
    public function detailList() {
        $param = $this->request->param();
        $param['limit'] = 100;
        $param['page'] = isset($param['page']) ? $param['page'] + 1 : 1;
        $config = [
            'page' => $param['page']
        ];

        ### 获取用户基本信息
        $allocate = MemberAllocate::get($param['allocate_id']);
        if (empty($allocate)) {
            $result = [
                'code'  => '400',
                'msg'   => '获取用户基本信息失败',
            ];
            return json($result);
        }
        $users = User::getUsers(false);

        $map = [];
        $map[] = ['member_id','=',$allocate['member_id']];
        $field = "status,create_time,next_visit_time,user_id,content,is_into_store";
        ### 根据分配表的数据查询回访记录
        $list = $this->model->where($map)
                    ->field($field)
                    ->order('create_time desc')
                    ->paginate($param['limit'], false, $config);
        // echo $this->model->getLastSql();

        if(empty($list))
        {
            $result = [
                'code'  => '200',
                'msg'   => '回访记录为空',
                'data'  => []
            ];
            return json($result);
        }

        foreach ($list as &$value) {
            $value['level'] = '重要客户';
            // $value['s'] = $value['status'];
            // $value['s1'] = $this->statusList;

            $value['status'] = $this->statusList[$value['status']]['title'];
            $value['realname'] = $users[$value['user_id']]['realname'];
            $value['next_visit_time'] = date("Y-m-d",$value['next_visit_time']);
        }

        $result = [
            'code'  => '200',
            'msg'   => '回访记录',
            'data'  => $list->getCollection()
        ];

        return json($result);
    }

    ### 获取回访状态列表
    public function create()
    {
        $map = [];
        $map[]  = ['type', '<>', 'wash'];

        $fields = 'id,title';
        $statusList = Intention::field($fields)->where($map)->order('is_valid desc,sort desc,id asc')->select();
        foreach ($statusList as &$row) {
            if ($row['id'] == 4) {
                $row['children'] = [
                    '空号',
                    '无任何需求',
                    '有需求，客户已定',
                    '无结婚需求'
                ];

            } else if ($row['id'] == 7) {
                $row['children'] = [
                    '电话无法接通',
                    '关机',
                    '客户直接挂断',
                    '停机'
                ];

            } else {
                $row['children'] = [];
            }
        }

        $result = [
            'code'  => '200',
            'msg'   => '获取数据成功',
            'data'  => [
                'statusList' => $statusList
            ]
        ];

        return json($result);
    }

    ## 添加回访记录
    public function doCreate()
    {
        $param = $this->request->param();

        if (!empty($param['next_visit_time'])) {
            $data['next_visit_time'] = strtotime($param['next_visit_time']);
        } else {
            $data['next_visit_time'] = 0;
        }

        if (empty($param['status'])){
            return json(['code'=>'400','msg'=>'状态']);
        }

        if (empty($param['content'])){
            return json(['code'=>'400','msg'=>'备注']);
        }

        $allocate = MemberAllocate::get($param['allocate_id']);
        $member = Member::get($allocate->member_id);
        $member->startTrans();
        $model = new MemberVisit();
        ### 保存回访信息
        if (!empty($param['next_visit_time'])) {
            $param['next_visit_time'] = strtotime($param['next_visit_time']);
        } else {
            $param['next_visit_time'] = 0;
        }
        $model->status = $param['status'];
        $model->member_allocate_id = $allocate->id;
        $model->member_id = $allocate->member_id;
        $model->member_no = $member->member_no;
        $visitNo = microtime() . rand(100000, 1000000);
        $model->visit_no = md5($visitNo);
        $model->clienter_no = $this->user['user_no'];
        $model->user_id = $this->user['id'];
        $result1 = $model->save($param);

        $member->active_status = $param['status'];
        $member->visit_amount = ['inc', 1];
        $result2 = $member->save();

        if ($result1 && $result2) {
            $member->commit();
            $data = [];
            $data['active_status'] = $param['status'];
            $data['color'] = $param['color'] ? $param['color'] : '';
            $res = $allocate->save($data);

            ### 添加下次回访提醒
            if ($param['next_visit_time'] > 0) {
                $Notice = new Notice();
                $from = 0;
                $to = $this->user['id'];
                $content = '预约回访提醒';
                $Notice->appendNotice('visit', $from, $to, $param['next_visit_time'], $content);
            }
            ### 记录log日志
            OperateLog::appendTo($model);
            return json(['code' => '200', 'msg' => '回访成功']);
        } else {
            $member->rollback();
            return json(['code' => '400', 'msg' => '回访失败']);
        }
    }
}
