<?php
$arr = [
    [
        'id'    => 100,
        'name'  => '1',
        'age'   => '1000',
        'sex'   => 1
    ],
    [
        'id'    => 101,
        'name'  => '1',
        'age'   => '1000',
        'sex'   => 1
    ],
    [
        'id'    => 102,
        'name'  => '1',
        'age'   => '1000',
        'sex'   => 1
    ]
];

$new = array_column($arr, 'id');

print_r($new);