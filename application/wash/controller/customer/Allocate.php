<?php

namespace app\wash\controller\customer;

use app\common\model\MemberAllocate;
use app\common\model\MemberHotelAllocate;
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
}
