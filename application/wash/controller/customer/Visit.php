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

        $member->visit_amount = ['inc', 1];
        $result2 = $member->save();

        if ($result1 && $result2) {
            $member->commit();
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

}
