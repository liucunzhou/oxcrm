<?php
namespace app\index\controller;


class News extends Base
{
    public function index()
    {
        $list = model('News')->order('create_time desc')->paginate(10);
        $this->assign('list', $list);
        return $this->fetch();
    }

}