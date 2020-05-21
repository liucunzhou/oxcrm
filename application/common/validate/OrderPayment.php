<?php

namespace app\common\validate;


use think\Validate;

class OrderPayment extends Validate
{
    protected $rule = [
        'pay_type' => 'require|number',
        'pay_item_price' => 'require|number',
        'pay_to_company' => 'require',
        'pay_to_bank' => 'require',
        'pay_to_account' => 'require',

    ];

    protected $message = [
        'pay_type.require' => '请选择付款性质',
        'pay_type.number' => '请选择付款性质',
        'pay_item_price.require' => '付款金额不能为空',
        'pay_item_price.number' => '付款金额必须是数字',
        'pay_to_company.require' => '付款酒店不能为空',
        'pay_to_bank.require' => '开户行不能为空',
        'pay_to_account.require' => '收款账号不能为空',
    ];
}