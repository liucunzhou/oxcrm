<?php

namespace app\common\validate;


use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'company_id' => 'require|gt:0',
        'mobile' => '/^1[1-9]{1}[0-9]{9}$/',
        'news_type' => 'require|number',
        'cooperation_mode' => 'number',
        'sign_date' => 'date',
        'bridegroom' => 'date',
        'bridegroom_mobile' => 'date',

    ];

    protected $message = [
        'company_id.require' => '请选择公司',
        'company_id.gt' => '请选择公司',
        'news_type.require' => '请选择订单类型',
        'news_type.number' => '请选择订单类型',
    ];
}