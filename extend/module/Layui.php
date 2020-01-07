<?php
/**
 * Created by PhpStorm.
 * User: xiaozhu
 * Date: 2019/7/10
 * Time: 19:11
 */

namespace module;


use app\common\model\Auth;
use app\common\model\User;
use app\common\model\UserAuth;

class Layui
{

    public static function checkAuth($route)
    {
        $display = 'layui-hide';
        $nodes = Auth::getList();
        $user = session("user");
        if($user['nickname'] != 'admin') {
            $userAuth = UserAuth::getUserAuthSet($user['id']);
            $userAuthSet = explode(',', $userAuth['auth_set']);
            foreach ($userAuthSet as $val){
                if (strtolower($nodes[$val]['route']) == strtolower($route)) {
                    $display = '';
                    break;
                }
            }
        } else {
            $display = '';
        }

        return $display;
    }

    public static function button($tag)
    {
        $show = false;
        $nodes = Auth::getList();
        $user = session("user");
        if($user['nickname'] != 'admin') {
            $userAuth = UserAuth::getUserAuthSet($user['id']);
            $userAuthSet = explode(',', $userAuth['auth_set']);
            foreach ($userAuthSet as $val){
                $route = explode("/",$nodes[$val]['route']);
                if (strtolower($route[1]) == strtolower($tag['event'])) {
                    $show = true;
                    break;
                }
            }
        } else {
            $show = true;
        }

        if(!$show) return '';

        $event  = empty($tag['event']) ? 'event' : $tag['event'];
        $text   = empty($tag['text']) ? ' 未知事件' : $tag['text'];
        $class  = empty($tag['class']) ? '' : $tag['class'];

        if(isset($tag['type']) && $tag['type'] == 'edit') {
            $parse = "<button class=\"layui-btn layui-btn-xs {$class}\" lay-event=\"{$event}\">{$text}</button>";
        } else if(isset($tag['type']) && $tag['type'] == 'delete') {
            $parse = "<button class=\"layui-btn layui-btn-xs layui-btn-danger {$class}\" lay-event=\"{$event}\">{$text}</button>";
        } else {
            $parse = "<button class=\"layui-btn layui-btn-sm {$class}\" lay-event=\"{$event}\">{$text}</button>";
        }

        return $parse;
    }


    public static function search($tag)
    {
        $event  = empty($tag['event']) ? 'event' : $tag['event'];
        $text   = empty($tag['text']) ? ' 未知事件' : $tag['text'];
        $class  = empty($tag['class']) ? '' : $tag['class'];
        $parse = "<button class=\"layui-btn layui-btn-sm {$class}\" lay-event=\"{$event}\">{$text}</button>";

        return $parse;
    }
}