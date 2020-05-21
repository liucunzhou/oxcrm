<?php

namespace app\common\validate;


use think\Validate;

class OrderIncome extends Validate
{
    protected $rule = [
        'income_date' => 'require|date',
        'income_payment' => 'require|number',
        'income_type' => 'require|number',
        'income_item_price' => 'require|number',
    ];

    protected $message = [
        'income_date.require' => '收款日期不能为空',
        'income_date.date' => '收款日期格式不正确',
        'income_type.require' => '请选择收款性质',
        'income_type.number' => '请选择收款性质',
        'income_payment.require' => '请选择收款方式',
        'income_payment.number' => '请选择收款方式',
        'income_item_price.require' => '收款金额不能为空',
        'income_item_price.number' => '收款金额必须是数字',
    ];
}