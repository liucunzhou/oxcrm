<?php

namespace app\common\validate;


use think\Validate;

class OrderWedding extends Validate
{
    protected $rule = [
        // 'wedding_totals' => 'require|float',
    ];

    protected $message = [
        'wedding_totals.require' => '婚庆总金额不能为空',
        'wedding_totals.float' => '婚庆总金额必须是数字',
    ];
}