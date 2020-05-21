<?php

namespace app\common\validate;


use think\Validate;

class OrderCommission extends Validate
{
    protected $rule = [
        'company_id' => 'require|number',
        'news_type' => 'require|number',
        'cooperation_mode' => 'require|number',
        'sign_date' => 'require|date',
        'bridegroom' => 'require',
        'bridegroom_mobile' => 'require|number'
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
        'bridegroom_mobile.number' => '请输入正确的电话格式'
    ];
}