<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2018/10/13
 * Time: 下午11:09
 */

namespace app\index\controller;


use think\facade\App;

class Tool extends Base
{

    public function createList()
    {

    }

    /**
     * 创建表单
     */
    public function createForm()
    {

        $data = $this->fetch();
        $modulePath = App::getModulePath();
        echo $path = $modulePath.'view/exam/edit_category.html';
        echo file_put_contents($path, $data);
    }
}