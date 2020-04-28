<?php
$content = '{"source":["179"],"coo":["6"],"assistant":["289","565"],"ceo":["575"],"cashier":["717"],"accounting":["587"]}';
$arr = json_decode($content, true);

$first = array_shift($arr);
print_r($first);
exit;

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

$payments = array_column($payments, 'title', 'id');
print_r($payments);

$table = [
    'header'    => [
        [
            'field' => '',
            'title' => '',
            'width' => '',
            'sort'  => ''
        ]
    ],
    'body'      => [

    ]
];

$arr = [
    'field' => [
        'id'        => '',
        'title'     => '',
        'type'      => 'text',
        'value'     => '',
        'default'   => '0',
        'source'    => '',
        'validate'  => 'require',
        'error'     => '',
        'is_submit' => '',
        'relation'  => ''
    ],
];