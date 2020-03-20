<?php

namespace app\wash\controller\customer;

use app\wash\controller\Backend;
use think\Request;

class Append extends Backend
{
    protected $customerModel;

    protected function initialize()
    {
        parent::initialize();

        $this->model = new \app\common\model\MemberAllocate();
        $this->customerModel = new \app\common\model\Member();
    }

    /**
     * 追加手机号
     */
    public function mobile()
    {

        return $this->fetch();
    }

    public function doAppendMobile()
    {

    }

    /**
     * 追加渠道
     */
    public function source()
    {
        return $this->fetch();
    }

    public function doAppendSource()
    {

    }
}
