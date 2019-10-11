<?php
namespace app\api\controller;

use app\common\model\Allocate;
use app\common\model\Budget;
use app\common\model\DuplicateLog;
use app\common\model\Intention;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\MemberVisit;
use app\common\model\Notice;
use app\common\model\OperateLog;
use app\common\model\Recommender;
use app\common\model\Source;
use app\common\model\Store;
use app\common\model\UserAuth;
use app\common\model\Scale;
use think\facade\Request;

class Visit extends Base
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
    }

    public function visitCustomer()
    {
        ### 获取用户基本信息
        $customer = Member::get($this->params['member_id']);
        $customer['source_text'] = $this->sources[$customer['source_id']] ? $this->sources[$customer['source_id']]['title'] : $customer['source_text'];

        ### 获取回访日志,检测是否拥有查看所有回访的权限
        $auth = UserAuth::getUserLogicAuth($this->user['id']);
        $visits = MemberVisit::getMemberVisitList($this->user, $auth, $customer);

        return xjson([
            'code'   => '200',
            'msg'   => '获取回访信息成功',
            'result' => [
                'customer'  => $customer,
                'visits'    => $visits,
                'budgets'   => $this->budgets,
                'scales'    => $this->scales,
                'hotels'    => $this->hotels,
            ]
        ]);
    }


    public function logs()
    {
        ### 获取用户基本信息
        $customer = Member::get($this->params['member_id']);
        if (!$this->auth['is_show_entire_mobile']) {
            $customer['mobile'] = substr_replace($customer['mobile'], '****', 3, 4);
            $customer['mobile1'] = substr_replace($customer['mobile'], '****', 3, 4);
        }

        ### 获取回访日志,检测是否拥有查看所有回访的权限
        $auth = UserAuth::getUserLogicAuth($this->user['id']);
        $visits = MemberVisit::getMemberVisitList($this->user, $auth, $customer);

        return xjson([
           'code'   => '200',
            'msg'   => '获取回访信息成功',
            'result' => [
                'customer' => $customer,
                'visits'    => $visits
            ]
        ]);
    }

    public function doVisitCustomer()
    {
        $post = Request::post();
        $Member = Member::get($post['member_id']);
        $originAllocate = MemberAllocate::getAllocate($this->user['id'], $post['member_id']);
        if (empty($originAllocate)) {
            return xjson(['code' => '500', 'msg' => '您不能回访这条客资']);
        }

        ### 保存回访信息
        $Model = model("MemberVisit");
        if(!empty($post['next_visit_time'])) {
            $post['next_visit_time'] = strtotime($post['next_visit_time']);
        } else {
            $post['next_visit_time'] = 0;
        }

        $Model->status = $post['active_status'];
        $Model->member_no = $Member->member_no;
        $visitNo = microtime().rand(100000,1000000);
        $Model->visit_no = md5($visitNo);
        $Model->clienter_no = $this->user['user_no'];
        $Model->user_id = $this->user['id'];
        $result1 = $Model->save($post);

        ### 该客资的回放次数+1
        if(in_array($this->user['role_id'],[3,4,10,11])) {
            // 推荐组＆派单组修改信息的时候自动同步到到客资基本信息
            if (!empty($post['realname'])) $Member->realname = $post['realname'];
            if (!empty($post['budget'])) $Member->budget = $post['budget'];
            if (!empty($post['banquet_size'])) $Member->banquet_size = $post['banquet_size'];
            if (!empty($post['zone'])) $Member->zone = $post['zone'];
            if (isset($post['news_type'])) $Member->news_type = $post['news_type'];
            if (isset($post['active_status'])) {
                $Member->status = $post['active_status'];
                $Member->active_status = $post['active_status'];
            }
            if (isset($post['hotel_id']) && $post['hotel_id'] > 0) {
                $Member->hotel_text = $this->hotels[$post['hotel_id']]['title'];
            }
        }
        $Member->visit_amount = ['inc', 1];
        $result2 = $Member->save($post);

        MemberAllocate::updateAllocateData($this->user['id'], $post['member_id'], $post);
        if ($result1 && $result2) {
            ### 添加下次回访提醒
            if($post['next_visit_time'] > 0) {
                $Notice = new Notice();
                $from = 0;
                $to = $this->user['id'];
                $content = '预约回访提醒';
                $Notice->appendNotice('visit', $from, $to, $post['next_visit_time'], $content);
            }

            OperateLog::appendTo($Model);
            return xjson(['code' => '200', 'msg' => '回访成功']);
        } else {
            return xjson(['code' => '500', 'msg' => '回访失败']);
        }
    }

}