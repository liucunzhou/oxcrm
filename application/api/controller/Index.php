<?php
namespace app\api\controller;

use think\Controller;

class Index extends Controller{

    public function index()
    {
        $actions = [
            ['title'=>'我要招聘'],
            ['title'=>'假期辅导'],
            ['title'=>'英语辅导'],
            ['title'=>'兴趣辅导'],
            ['title'=>'新店开业'],
            ['title'=>'朋友K歌'],
            ['title'=>'亲朋小聚'],
            ['title'=>'旅游骑行']
        ];

        foreach ($actions as $key=>&$action){
            $action['action'] = $key + 1;
        }

        $result = [
            'code'  => '200',
            'msg'   => '获取数据成功',
            'data'  => [
                'actionList' => $actions
            ]
        ];

        return json($result);
    }
}