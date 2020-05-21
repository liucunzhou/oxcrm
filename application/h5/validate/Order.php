<?php

namespace app\h5\validate;


use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'mobile' => 'require|max:11|/^1[1-9]{1}[0-9]{9}$/',
        'mobile1' => '/^1[3-8]{1}[0-9]{9}$/',
        'source_id' => 'require|number',
        'city_id' => 'require|number',
    ];

    protected $message = [
        'mobile.require' => '请输入手机号码',
        'mobile.max' => '手机号码最多不能超过11个字符',
        'mobile' => '手机号码格式不正确',
        'mobile1' => '其他手机号格式不正确',
        'source_id.require' => '请选择来源',
        'source_id.number' => '来源编号需要时数字',
        'city_id.require' => '请选择城市',
        'city_id.number' => '城市编号需要时数字'
    ];
}