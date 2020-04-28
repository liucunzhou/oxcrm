<?php
$json = '{"1":{"id":1,"title":"跟进中","is_valid":1},"2":{"id":2,"title":"成单客户","is_valid":1},"3":{"id":3,"title":"失效客户","is_valid":1},"4":{"id":4,"title":"无效客户","is_valid":1},"5":{"id":5,"title":"有效客户","is_valid":1},"6":{"id":6,"title":"意向客户","is_valid":1}}';
$arr = json_decode($json, true);
print_r($arr);

$item =  [
    'id'    => 0,
    'title' => '未跟进',
    'is_valid' => 1
];

array_unshift($arr, $item);

print_r($arr);