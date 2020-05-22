<?php

namespace app\common\validate;


use think\Validate;

class OrderBanquet extends Validate
{
    protected $rule = [
        'banquet_totals' => 'require|float',
        'table_amount' => 'require|number',
        'table_price' => 'require|float',
    ];

    protected $message = [
        'banquet_totals.require' => '婚宴总金额不能为空',
        'banquet_totals.float' => '婚宴总金额必须是数字',
        'table_amount.require' => '桌数不能为空',
        'table_amount.number' => '桌数必须是数字',
        'table_price.require' => '餐标不能为空',
        'table_price.float' => '餐标必须是数字',
    ];
}