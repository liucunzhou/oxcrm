<?php
namespace app\index\validate;


use think\Validate;

class User extends Validate
{
    protected $rule = [
        'nickname'  => 'require|max:25',
        'password'  => 'notbetween:6,120'
    ];

    protected $message = [
        'nickname.require' => '用户名不能为空',
        'nickname.unique' => '用户名已存在',
        'nickname.max' => '用户名称最多不能超过25个字符',
        'password.between' => '密码长度不够'
    ];
}