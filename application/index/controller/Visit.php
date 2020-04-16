<?php

namespace app\index\controller;

use app\common\model\DuplicateLog;
use app\common\model\Member;
use app\common\model\MemberVisit;
use app\common\model\Mobile;
use app\common\model\Notice;
use app\common\model\OperateLog;
use app\common\model\Recommender;
use app\common\model\Region;
use app\common\model\User;
use app\common\model\UserAuth;
use app\common\model\Source;
use app\common\model\Store;
use app\common\model\Intention;
use app\common\model\MemberAllocate;
use app\common\model\Budget;
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
        $this->budgets = Budget::getBudgetList();
        $this->scales = Scale::getScaleList();

        if (!Request::isAjax()) {
            $staffes = User::getUsersInfoByDepartmentId($this->user['department_id']);
            $this->assign('staffes', $staffes);
            $this->assign('sources', $this->sources);
        }
    }

    public function visitCustomer()
    {
        ### 获取分配信息
        $get = Request::param();
        $allocate = MemberAllocate::getAllocate($this->user['id'], ['member_id']);

        ### 获取用户基本信息
        $customer = Member::get($get['member_id']);
        if (!empty($allocate)) {
            $this->assign('allocate', $allocate);
        } else {
            if (!($this->auth['is_show_entire_mobile'] || !empty($allocate))) {
                $customer['mobile'] = substr_replace($customer['mobile'], '****', 3, 4);
                $customer['mobile1'] = substr_replace($customer['mobile1'], '****', 3, 4);
            }
        }
        if($customer['operate_id'] > 0) {
            $users = User::getUsers();
            $customer['operate_id'] = $users[$customer['operate_id']]['realname'];
        }
        $this->assign('customer', $customer);

        ### 获取用户区域列表
        if ($customer['city_id']) {
            $areas = Region::getAreaList($customer['city_id']);
        } else {
            $areas = [];
        }
        $this->assign('areas', $areas);

        $repeat = '';
        if (!empty($customer['repeat_log'])) {
            if ($this->auth['is_show_alias']) {
                ### 显示别名，也就是大类
                $repeat = $customer['repeat_platform_log'];
            } else {
                ### 显示来源
                $repeat = $customer['repeat_log'];
            }
        }
        $this->assign('repeat', $repeat);

        ### 获取回访日志,检测是否拥有查看所有回访的权限
        $visits = MemberVisit::getMemberVisitList($this->user, $this->auth, $customer);
        $this->assign('visits', $visits);

        ### 获取城市列表
        $cities = Region::getCityList(0);
        $this->assign('cities', $cities);
        if (in_array($this->user['role_id'], [3, 4, 10, 11])) {
            $this->assign('source_type', "select");
        } else {
            $this->assign('source_type', "input");
        }

        // 获取意向状态、来源、酒店、权限
        $this->assign('intentions', $this->status);
        $this->assign('sources', $this->sources);
        $this->assign("hotels", $this->hotels);
        $this->assign('newsTypes', $this->newsTypes);

        if ($this->user['role_id'] == 9) {
            $view = 'merchant_visit_customer';
        } else {
            $view = 'visit_customer';
        }

        return $this->fetch($view);
    }


    public function doVisitCustomer()
    {
        $post = Request::post();
        $Member = Member::get($post['member_id']);
        $originAllocate = MemberAllocate::getAllocate($this->user['id'], $post['member_id']);
        if ($this->user['role_id'] != 10 && $this->user['role_id'] != 11) {
            if (empty($originAllocate)) {
                return json(['code' => '500', 'msg' => '您不能回访这个客资']);
            }
        }

        $Model = model("MemberVisit");
        ### 保存回访信息
        if (!empty($post['next_visit_time'])) {
            $post['next_visit_time'] = strtotime($post['next_visit_time']);
        } else {
            $post['next_visit_time'] = 0;
        }
        $Model->status = $post['active_status'];
        $Model->member_no = $Member->member_no;
        $visitNo = microtime() . rand(100000, 1000000);
        $Model->visit_no = md5($visitNo);
        $Model->clienter_no = $this->user['user_no'];
        $Model->user_id = $this->user['id'];
        $result1 = $Model->save($post);

        ### 该客资的回放次数+1
        if (in_array($this->user['role_id'], [3, 4, 10, 11])) {
            // 推荐组＆派单组修改信息的时候自动同步到到客资基本信息
            if (isset($post['realname'])) $Member->realname = $post['realname'];
            if (isset($post['budget'])) $Member->budget = $post['budget'];
            if (isset($post['banquet_size'])) $Member->banquet_size = $post['banquet_size'];
            if (isset($post['news_type'])) $Member->news_type = $post['news_type'];
            if (isset($post['source_id'])) $Member->source_id = $post['source_id'];
            if (isset($post['source_id'])) $Model->source_text = $this->sources[$post['source_id']]['title'];
            if (isset($post['zone'])) $Member->zone = $post['zone'];
            if (isset($post['hotel_text'])) $Member->hotel_text = $post['hotel_text'];
            if (isset($post['active_status'])) {
                // $Member->status = $post['active_status'];
                $Member->active_status = $post['active_status'];
            }
        }
        $Member->visit_amount = ['inc', 1];
        $result2 = $Member->save($post);

        if ($result1 && $result2) {
            ### 同步分配信息
            MemberAllocate::updateAllocateData($this->user['id'], $post['member_id'], $post);

            ### 添加下次回访提醒
            if ($post['next_visit_time'] > 0) {
                $Notice = new Notice();
                $from = 0;
                $to = $this->user['id'];
                $content = '预约回访提醒';
                $Notice->appendNotice('visit', $from, $to, $post['next_visit_time'], $content);
            }

            ### 记录log日志
            OperateLog::appendTo($Model);
            return json(['code' => '200', 'msg' => '回访成功']);
        } else {
            return json(['code' => '200', 'msg' => '回访失败']);
        }
    }

    /**
     * 回访客资
     * @return mixed
     */
    public function visitLogs()
    {
        $get = Request::param();
        ### 获取用户基本信息
        $customer = Member::get($get['member_id']);
        if (!$this->auth['is_show_entire_mobile']) {
            $customer['mobile'] = substr_replace($customer['mobile'], '****', 3, 4);
            if (!empty($customer['mobile1'])) $customer['mobile1'] = substr_replace($customer['mobile1'], '****', 3, 4);
        }
        if($customer['operate_id'] > 0) {
            $users = User::getUsers();
            $customer['operate_id'] = $users[$customer['operate_id']]['realname'];
        }
        $this->assign('customer', $customer);

        ### 获取回访日志,检测是否拥有查看所有回访的权限
        $visits = MemberVisit::getMemberVisitList($this->user, $this->auth, $customer);
        $this->assign('visits', $visits);

        $repeat = '';
        if (!empty($customer['repeat_log'])) {
            if ($this->auth['is_show_alias']) {
                ### 显示别名，也就是大类
                $repeat = $customer['repeat_platform_log'];
            } else {
                ### 显示来源
                $repeat = $customer['repeat_log'];
            }
        }
        $this->assign('repeat', $repeat);

        // 获取意向状态、来源、酒店、权限
        $this->assign('intentions', $this->status);
        $this->assign('sources', $this->sources);
        $this->assign('hotels', $this->hotels);
        $this->assign('newsTypes', $this->newsTypes);
        return $this->fetch();
    }

    /**
     * 编辑客资视图
     * @return mixed
     */
    public function editCustomer()
    {
        $get = Request::param();
        ### 来源列表
        $sources = \app\common\model\Source::getSources();
        $this->assign('sources', $sources);

        ### 获取推荐人列表
        $recommenders = \app\common\model\Recommender::column('id,title', 'id');
        $this->assign('recommenders', $recommenders);

        ### 酒店列表
        $hotels = Store::getStoreList();
        $this->assign("hotels", $hotels);

        $data = \app\common\model\Member::get($get['member_id']);
        if (!empty($data['area_id'])) $data['area_id'] = explode(',', $data['area_id']);
        if(!strpos($data['wedding_date'], '~')) {
            $data['wedding_date'] .= '~'.$data['wedding_date'];
        }
        $this->assign('data', $data);

        $cities = Region::getCityList(0);
        $this->assign('cities', $cities);

        $areas = [];
        if (!empty($this->user['city_id'])) {
            $areas = Region::getAreaList($this->user['city_id']);
        }
        $this->assign('areas', $areas);

        $action = 'edit';
        $this->assign('action', $action);
        return $this->fetch();
    }

    ### 编辑客户信息
    public function doEditCustomer()
    {
        $post = Request::post();
        $Model = \app\common\model\Member::get($post['id']);
        $result1 = $Model->save($post);
        if ($result1) {
            ### 将手机号1添加到手机号库
            if(!empty($post['mobile1'])) {
                $mobileModel = new Mobile();
                $mobileModel->insert(['mobile'=>$post['mobile1'], 'member_id'=>$post['id']]);
            }
            MemberAllocate::updateAllocateData($this->user['id'], $Model->id, $post);
            ### 添加操作记录
            OperateLog::appendTo($Model);
            return json(['code' => '200', 'msg' =>  '修改客户资料成功']);
        } else {
            return json(['code' => '500', 'msg' => '修改客户资料失败, 请重试']);
        }
    }
}
