<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/7/28
 * Time: 8:01 PM
 */

namespace app\index\controller;

use think\Controller;
use think\facade\Request;

class Region extends Controller
{

    public function getProvinceList()
    {
        $post = Request::param();
        $data = \app\index\model\Region::getCityList($post['id']);

        return json($data);
    }

    public function getCityList()
    {
        $post = Request::param();
        $data = \app\index\model\Region::getCityList($post['id']);

        return json($data);
    }

    public function getAreaList()
    {
        $post = Request::param();
        $data = \app\index\model\Region::getAreaList($post['id']);

        return json($data);
    }
}