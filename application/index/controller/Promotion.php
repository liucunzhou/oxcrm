<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/5/12
 * Time: 3:51 PM
 */

namespace app\index\controller;


class Promotion extends Base
{
    public function index()
    {
        $list = model('promotion')->order('cost_date desc,id asc')->paginate(10);
        $this->assign('list', $list);
        return $this->fetch();
    }
}