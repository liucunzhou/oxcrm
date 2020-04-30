<?php
$json = '{"source":["179"],"coo":["6"],"assistant":["289","565"],"ceo":["575"],"cashier":["717"],"accounting":["587"]}';

$arr = json_decode($json, true);
$current = 'coo';
foreach ($arr as $key=>$value) {
    next($arr);
    if($key == $current) break;
}
// next($arr);
echo key($arr);
// echo $key = key($row);