<?php
namespace app\index\controller\order;

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

    // 婚车信息
    public function car()
    {
        return $this->fetch();
    }

    // 喜糖信息
    public function sugar()
    {
        return $this->fetch();
    }

    // 酒水信息
    public function wine()
    {
        return $this->fetch();
    }

    // 灯光信息
    public function light()
    {
        return $this->fetch();
    }

    // 点心信息
    public function dessert()
    {
        return $this->fetch();
    }

    // led信息
    public function led()
    {
        return $this->fetch();
    }

    // 3D信息
    public function d3()
    {
        return $this->fetch();
    }
}