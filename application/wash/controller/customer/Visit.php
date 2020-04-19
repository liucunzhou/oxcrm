<?php

namespace app\wash\controller\customer;

use app\common\model\Allocate;
use app\common\model\Member;
use app\common\model\MemberAllocate;
use app\common\model\MemberVisit;
use app\common\model\Notice;
use app\common\model\OperateLog;
use app\wash\controller\Backend;
use think\Request;

class Visit extends Backend
{
    protected function initialize()
    {
        parent::initialize();

        $this->model = new \app\common\model\MemberVisit();
        // 获取status
        $where = [];
        $where[] = ['type', 'in', ['wash', 'other']];
        $statusList = \app\common\model\Intention::where($where)->order('sort,id')->select();
        $this->assign('statusList', $statusList);

        $fasts = [
            4   => [
                '空号',
                '无任何需求',
                '有需求，客户已定',
                '无结婚需求'
            ],
            7   => [
                '电话无法接通',
                '关机',
                '客户直接挂断',
                '停机'
            ]
        ];
        $this->assign('fasts', $fasts);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $params = $this->request->param();
        $this->assign('params', $params);
        //
        return $this->fetch();
    }

    public function doCreate()
    {
        $params = $this->request->param();
        $allocate = MemberAllocate::get($params['allocate_id']);
        $member = Member::get($allocate->member_id);
        $member->startTrans();
        $model = new MemberVisit();
        ### 保存回访信息
        if (!empty($params['next_visit_time'])) {
            $params['next_visit_time'] = strtotime($params['next_visit_time']);
        } else {
            $params['next_visit_time'] = 0;
        }
        $model->status = $params['status'];
        $model->member_allocate_id = $allocate->id;
        $model->member_id = $allocate->member_id;
        $model->member_no = $member->member_no;
        $visitNo = microtime() . rand(100000, 1000000);
        $model->visit_no = md5($visitNo);
        $model->clienter_no = $this->user['user_no'];
        $model->user_id = $this->user['id'];
        $result1 = $model->save($params);

        $member->active_status = $params['status'];
        $member->visit_amount = ['inc', 1];
        $result2 = $member->save();

        if ($result1 && $result2) {
            $member->commit();
            $data = [];
            $data['active_status'] = $params['status'];
            $data['color'] = $params['color'] ? $params['color'] : '';
            $allocate->save($data);

            ### 添加下次回访提醒
            if ($params['next_visit_time'] > 0) {
                $Notice = new Notice();
                $from = 0;
                $to = $this->user['id'];
                $content = '预约回访提醒';
                $Notice->appendNotice('visit', $from, $to, $params['next_visit_time'], $content);
            }
            ### 记录log日志
            OperateLog::appendTo($model);
            return json(['code' => '200', 'msg' => '回访成功', 'redirect'=>$params['redirect']]);
        } else {
            $member->rollback();
            return json(['code' => '500', 'msg' => '回访失败']);
        }
    }

    public function logs()
    {
        $get = $this->request->param();
        ### 获取用户基本信息
        $customer = Member::get($get['member_id']);
        if (!$this->auth['is_show_entire_mobile']) {
            $customer['mobile'] = substr_replace($customer['mobile'], '****', 3, 4);
            if (!empty($customer['mobile1'])) $customer['mobile1'] = substr_replace($customer['mobile1'], '****', 3, 4);
        }
        if($customer['operate_id'] > 0) {
            $users = \app\common\model\User::getUsers();
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

}
