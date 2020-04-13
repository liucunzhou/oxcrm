<?php
/**
 * Created by PhpStorm.
 * User: liucunzhou
 * Date: 2019/4/26
 * Time: 12:40 AM
 */

namespace module;


class Redis
{
    public static function redis() {

        $cache = new \Redis();
        $cache->connect('127.0.0.1', 6379, 5);
        $cache->auth('lcz19860109');

        return $cache;
    }
}