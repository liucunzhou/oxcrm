<?php

namespace app\api\controller;

use think\Controller;

class Map extends Controller
{
    public function index()
    {
        $post = input("post.");
        $location = $post['lat'].','.$post['lng'];
        $key = 'QJABZ-PEUWW-VZ5RJ-OLZYH-YHJSE-SYFYV';
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?location='.$location.'&key='.$key.'&get_poi=1';
        $res = file_get_contents($url);

        header("Content-type:application/json;charset=utf-8");
        echo $res;
    }
}