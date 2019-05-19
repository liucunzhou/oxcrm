<?php
namespace app\index\controller;

class Count extends BaseController
{
    public function index()
    {
        return $this->fetch();
    }
}