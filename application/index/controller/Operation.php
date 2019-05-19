<?php
/**
 * Created by PhpStorm.
 * User: xiaozhu
 * Date: 2019/5/7
 * Time: 13:34
 */

namespace app\index\controller;


class Operation extends Base
{
    public function index()
    {
        return $this->fetch();
    }
}