<?php
$allocateTypes = ['分配获取', '全号搜索', '公海申请', '自行添加'];

$newsTypes = ['婚宴信息', '婚庆信息', '一站式','婚纱摄影','婚车','婚纱礼服','男装','宝宝宴','会务'];

$levels = [
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
];

$paymentTypeList = [
    [
        'id'    => 1,
        'title' => '定金',
    ],
    [
        'id'    => 2,
        'title' => '中款'
    ],
    [
        'id'    => 3,
        'title' => '尾款'
    ],
    [
        'id'    => 4,
        'title' => '意向金'
    ],
    [
        'id'    => 5,
        'title' => '二销'
    ]
];

$payments = [
    [
        'id'    => 1,
        'title' =>'支付宝-g',
    ],
    [
        'id'    => 2,
        'title' => '支付宝-s'
    ],
    [
        'id'    => 3,
        'title' => '微信-g'
    ],
    [
        'id'    => 4,
        'title' => '微信-s'
    ],
    [
        'id'    => 5,
        'title' => '银行汇款-g'
    ],
    [
        'id'    => 6,
        'title' => '银行汇款-s'
    ],
    [
        'id'    => 7,
        'title' => '直付酒店'
    ],
    [
        'id'    => 8,
        'title' => '现金'
    ],
    [
        'id'    => 9,
        'title' => 'POS机'
    ],
    [
        'id'    => 10,
        'title' => '其他'
    ]
];

$suborderTypes = ['否', '婚宴二销', '婚庆二销'];
$checkStatusList = ['待审核','审核中', '审核通过','审核驳回', '已完成'];
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
    // 信息类型
    'news_type_list'    => $newsTypes,
    // 合作模式
    'cooperation_mode'  =>  $cooperationModes,
    'levels' => $levels,
    // 进店状态列表
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
    // 审核流程
    'check_sequence'    => $check_sequence,
    // 审核状态列表
    'check_status'   =>  $checkStatusList,
    // 付款方式，支付宝支付、微信支付等
    'payments'          => $payments,
    // 付款性质，如定金、中款、尾款等
    'payment_type_list' => $paymentTypeList
];