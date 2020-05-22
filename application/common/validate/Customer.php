<?php

namespace app\common\validate;


use think\Validate;

class Customer extends Validate
{
    protected $rule = [
        'news_type' => 'require|number',
        'source_id' => 'require|number',
        'mobile' => 'require|number',
        'mobile1' => 'number',
        'city_id' => 'require|number',
        'hotel_id' => 'number',
        'banquet_size' => 'number',
        'banquet_size_end' => 'number',
        'budget' => 'number',
        'budget_end' => 'number',
        'wedding_date' => 'date',
    ];

    protected $message = [
        'news_type.require' => '请选择订单类型',
        'news_type.number' => '订单类型必须为数字',
        'source_id.require' => '请选择来源',
        'source_id.number' => '来源格式不正确',
        'mobile.require' => '手机号不能为空',
        'mobile.number' => '手机号格式不正确',
        'mobile1.number' => '手机号格式不正确',
        'banquet_size.number' => '桌数区间只能是数字',
        'banquet_size_end.number' => '桌数区间只能是数字',
        'budget.number' => '预算区间只能是数字',
        'budget_end.number' => '预算区间只能是数字',
        'wedding_date.date' => '婚期格式不正确',
    ];
}