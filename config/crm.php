<?php
$allocateTypes = ['分配获取', '全号搜索', '公海申请', '自行添加'];

return [
    // 分配类型
    'allocate_type_list' => $allocateTypes,

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
];