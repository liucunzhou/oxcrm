<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/7/8
 * Time: 11:15 PM
 */

namespace app\index\controller;


use app\index\model\Department;
use app\index\model\User;
use think\facade\Session;

class Im extends Base
{
    public function init()
    {
        $cuser = Session::get("user");
        $avatar = "http://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83eqrMQPajaIPOIhMRrOOov21CdGrGgibBqUT7CI8r8QQqSnz0Y2qQYTwRGrbYnQ5rauEmSwCZfyYSsw/132";
        $groups = [];
        $departments = Department::getDepartments();
        $groupAvatar = 'https://cdn.laoyaojing.net/dongxiaofang/45099876de73e38440cdc0284b932ced.png';
        foreach ($departments as $department) {
            $groups[] = [
                'id' => $department['id'],
                'groupname' => $department['title'],
                'avatar' => $groupAvatar
            ];
        }

        $users = User::getUsers();
        foreach ($users as $user){
            if($user['id'] == $cuser['id']) continue;

        }

        $result = [
            'code'  => 0,
            'msg'   => 'init success',
            'data'  => [
                'mine'  => [
                    "id"    => $cuser['id'],
                    "username" => $cuser['realname'],
                    "status" => "online",
                    "sign"  => "帅哥一枚",
                    "avatar" => $avatar
                ],
                'group'  => $groups
            ]
        ];

        return json($result);
    }

    /**
     * 个人信息
     */
    public function mine()
    {

    }

    public function friends()
    {

    }

    /**
     * 群组信息
     */
    public function groups()
    {


    }
}