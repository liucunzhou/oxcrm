<?php
namespace app\api\controller;

use think\Controller;
use think\facade\Request;

class Region extends Controller
{
    public function getProvinceList()
    {
        $post = Request::param();
        $data = \app\common\model\Region::getCityList($post['id']);
        return xjson($data);
    }

    public function getCityList()
    {
        $post = Request::param();
        $data = \app\common\model\Region::getCityList($post['id']);
        return xjson($data);
    }

    public function getAreaList()
    {
        $post = Request::param();
        $data = \app\common\model\Region::getAreaList($post['id']);
        $data = array_values($data);
        $result = [
            'code'  => 0,
            'msg'   => '获取地区列表成功',
            'data'  => $data
        ];
        return xjson($result);
    }
}