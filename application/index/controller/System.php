<?php
namespace app\index\controller;

class System extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

}
