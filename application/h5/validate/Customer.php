<?php
namespace app\h5\validate;


use think\Validate;

class Customer extends Validate
{
    protected $rule = [
//        'mobile'    =>  'require|max:11'
        'mobile'    => 'require|max:11|/^1[1-9]{1}[0-9]{9}$/',
        'mobile1'    => '/^1[3-8]{1}[0-9]{9}$/',
    ];

    protected $message = [
        'mobile.require'    =>  '请输入手机号码',
        'mobile.max'        =>  '手机号码最多不能超过11个字符',
        'mobile'            =>  '手机号码格式不正确',
        'mobile1'           =>  '其他手机号格式不正确',
    ];
}