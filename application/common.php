<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
### 清除两端的空格
if (!function_exists('clear_both_blank')) {
    function clear_both_blank($data)
    {
        $data = trim($data);
        $data = preg_replace("/\s(?=\s)/", "", $data);
        $data = preg_replace("/^[(\xc2\xa0)|\s]+/", "", $data);
        $data = preg_replace("/[\n\r\t]/", ' ', $data);
        return $data;
    }
}

### csv转码
if(!function_exists('csv_convert_encoding')) {
    function csv_convert_encoding($data)
    {
        $encoding = mb_detect_encoding($data);
        var_dump($encoding);
        $data = mb_convert_encoding($data, 'UTF-8', ['Unicode', 'ASCII', 'GB2312', 'GBK', 'UTF-8', 'ISO-8859-1']);

        return $data;
    }
}