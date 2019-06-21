<?php
namespace app\index\controller;

class Count extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function hour()
    {
        return $this->fetch();
    }

    public function compare()
    {
        return $this->fetch();
    }
}