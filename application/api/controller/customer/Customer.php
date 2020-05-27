<?php
namespace app\api\controller\customer;

use think\Controller;

class Customer extends Controller
{
    private $listFields = [];
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 今日跟进
     */
    public function today()
    {
        return json();
    }

    /**
     * 我的客资
     */
    public function mine()
    {
        // 字段
        return json();
    }
}