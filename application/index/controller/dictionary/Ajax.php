<?php
namespace app\index\controller\dictionary;

use app\wash\controller\Backend;

class Ajax extends Backend
{
    // 婚庆表单
    public function wedding()
    {
        return $this->fetch();
    }

    // 婚宴表单
    public function banquet()
    {
        return $this->fetch();
    }

    // 酒店消费项目
    public function hotelItems()
    {
        return $this->fetch();
    }
}