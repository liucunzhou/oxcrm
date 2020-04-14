<?php

namespace app\wash\controller\customer;

use app\common\model\MemberAllocate;
use app\common\model\MemberHotelAllocate;
use app\common\model\OperateLog;
use app\common\model\User;
use app\wash\controller\Backend;
use think\Request;

class Allocate extends Backend
{
    protected $customerModel;

    protected function initialize()
    {
        parent::initialize();

        $this->model = new \app\common\model\MemberAllocate();
        $this->customerModel = new \app\common\model\Member();
    }

    /**
     * 分配给清洗组
     */
    public function wash()
    {
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if (!in_array($value['role_id'], [2, 7])) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);

        return $this->fetch();
    }

    /**
     * 分配给推荐组
     */
    public function recommend()
    {
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if (!in_array($value['role_id'], [3, 4])) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);
        return $this->fetch();
    }

    public function merchant()
    {
        $users = User::getUsers(false);
        foreach ($users as $key => $value) {
            if ($value['role_id'] != 9) {
                unset($users[$key]);
            }
        }
        $this->assign('users', $users);
        return $this->fetch();
    }

    public function doAssign()
    {
        $post = $this->request->param();
        if (empty($post['ids'])) {
            return json([
                'code' => '500',
                'msg' => '请先选择要分配的客资'
            ]);
        }

        $ids = explode(',', $post['ids']);
        $totals = count($ids);
        $success = 0;
        foreach ($ids as $id) {
            if (empty($id)) continue;
            $result = $this->executeAllocateToStaff($id, $post['staff']);
            if ($result) $success = $success + 1;
        }

        $fail = $totals - $success;
        return json([
            'code' => '200',
            'msg' => "分配到洗单组:成功{$success}个,失败{$fail}个"
        ]);
    }

    private function executeAllocateToStaff($id, $staff, $status = 0)
    {
        $allocate = MemberAllocate::get($id);
        if (!$allocate) false;

        ### 检查该用户是否已经分配过
        $isAllocated = MemberAllocate::getAllocate($staff, $allocate->member_id);
        if ($isAllocated) return false;

        $data = $allocate->getData();
        unset($data['id']);
        $data['operate_id'] = $this->user['id'];
        $data['user_id'] = $staff;
        $data['update_time'] = 0;
        $data['create_time'] = time();
        ### 分配后重新回访
        $data['active_status'] = $status;
        $data['active_assign_status'] = 0;
        $data['possible_assign_status'] = 0;
        $data['allocate_type'] = 0;
        $MemberAllocate = new MemberAllocate();
        $result1 = $MemberAllocate->insert($data);

        // 更新分配状态
        $data = [];
        if ($allocate->active_status == 5) {
            $data['active_assign_status'] = 1;
        }
        if ($allocate->active_status == 6) {
            $data['possible_assign_status'] = 1;
        }
        $data['is_into_store'] = 0;
        $data['assign_status'] = 1;
        $allocate->save($data);

        if ($result1) {
            $user = User::get($staff);
            if (!empty($user['dingding'])) {
                $users[] = $user['dingding'];
                $DingModel = new \app\api\model\DingTalk();
                $message = $DingModel->linkMessage("新增客资", "新增客资消息", "http://h5.hongsizg.com/pages/customer/mine?status=0&is_into_store=0&page_title=%E6%9C%AA%E8%B7%9F%E8%BF%9B%E5%AE%A2%E8%B5%84&time=" . time());
                $DingModel->sendJobMessage($users, $message);
            }

            ### 记录log日志
            OperateLog::appendTo($allocate);
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }
}
