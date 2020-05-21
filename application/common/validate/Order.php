<?php

namespace app\common\validate;


use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'company_id' => 'require|number',
        'news_type' => 'require|number',
        'cooperation_mode' => 'require|number',
        'sign_date' => 'require|date',
        'bridegroom' => 'require',
        'bridegroom_mobile' => 'require|number',
        'totals' => 'require|number',
        'earnest_money_date' => 'require|date',
        'earnest_money' => 'require|number',
        'middle_money_date' => 'require|date',
        'middle_money' => 'require|number',
        'tail_money_date' => 'require|date',
        'tail_money' => 'require|number',
    ];

    protected $message = [
        'company_id.require' => '请选择公司',
        'company_id.number' => '请选择公司',
        'news_type.require' => '请选择订单类型',
        'news_type.number' => '订单类型必须为数字',
        'cooperation_mode.require' => '请选择合作类型',
        'cooperation_mode.number' => '请选择合作类型',
        'sign_date.require' => '请选择签约日期',
        'sign_date.date' => '签约日期必须是日期',
        'bridegroom.require' => '新郎姓名不能为空',
        'bridegroom_mobile.require' => '新郎电话不能为空',
        'bridegroom_mobile.number' => '请输入正确的电话格式',
        'totals.require' => '订单金额不能为空',
        'totals.number' => '订单金额必须为数字',
        'earnest_money_date.require' => '请选择订单类型',
        'earnest_money_date.date' => '请选择订单类型',
        'earnest_money.require' => '请选择订单类型',
        'earnest_money.number' => '请选择订单类型',
        'middle_money_date.require' => '请选择订单类型',
        'middle_money_date.date' => '请选择订单类型',
        'middle_money.require' => '请选择订单类型',
        'middle_money.number' => '请选择订单类型',
        'tail_money_date.require' => '请选择订单类型',
        'tail_money_date.date' => '请选择订单类型',
        'tail_money.require' => '请选择订单类型',
        'tail_money.number' => '请选择订单类型',
    ];
}