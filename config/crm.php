<?php
$allocateTypes = ['分配获取', '全号搜索', '公海申请', '自行添加'];
$newsTypes = ['婚宴信息', '婚庆信息', '一站式','婚纱摄影','婚车','婚纱礼服','男装','宝宝宴','会务'];
$paymentTypes = [1=>'定金', 2=>'中款', 3=>'尾款', 4=>'意向金', 5=>'二销'];
$payments = [1=>'支付宝-g', 2=>'支付宝-s', 3=>'微信-g', 4=>'微信-s', 5=>'银行汇款-g', 6=>'银行汇款-s', 7=>'直付酒店', 8=>'现金', 9=>'POS机', 10=>'其他'];
$suborderTypes = ['否', '婚宴二销', '婚庆二销'];
$checkStatusList = ['待审核', '已通过', '已驳回'];
$cooperationModes = [1=>'返佣单',2=>'代收代付',3=>'代收代付+返佣单',4=>'一单一议'];

$check_sequence = [
    'source' => [
        'id'    => 'source',
        'title' => '客服渠道',
        'type'  => 'staff'
    ],
    'coo' => [
        'id'    => 'coo',
        'title' => '运营总监',
        'type'  => 'role'

    ],
    'assistant' => [
        'id'    => 'assistant',
        'title' => '行政助理',
        'type'  => 'staff'
    ],
    'ceo' => [
        'id'    => 'ceo',
        'title' => '总经理',
        'type'  => 'staff'
    ],
    'cashier' => [
        'id'    => 'cashier',
        'title' => '出纳',
        'type'  => 'staff'
    ],
    'accounting' => [
        'id'    => 'accounting',
        'title' => '会计',
        'type'  => 'staff'
    ],
];

return [
    // 分配类型
    'allocate_type_list' => $allocateTypes,

    'news_type_list'    => $newsTypes,

    'cooperation_mode'  =>  $cooperationModes,

    'levels' => [
        999 => [
            'title' => '非常重要',
            'btn' => 'btn-danger'
        ],
        998 => [
            'title' => '重要',
            'btn' => 'btn-warning'
        ],
        997 => [
            'title' => '一般',
            'btn' => 'btn-primary'
        ],
        0 => [
            'title' => '无',
            'btn' => 'btn-info'
        ]
    ],

    'into_status_list'  =>  [
        '0' =>  [
            'id'    =>  '0',
            'title' =>  '待进店'
        ],
        '1' =>  [
            'id'    =>  '1',
            'title' =>  '进店'
        ],
        '2' =>  [
            'id'    =>  '2',
            'title' =>  '未进店'
        ],
        '3' =>  [
            'id'    =>  '3',
            'title' =>  '改期'
        ]
    ],

    'check_sequence'    => $check_sequence
];