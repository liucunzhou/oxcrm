<?php
$json = '{"source":["179"],"coo":["6"],"assistant":["289","565"],"ceo":["575"],"cashier":["717"],"accounting":["587"]}';

$arr = json_decode($json, true);
array_
$row = next($arr['coo']);
print_r($row);
// echo $key = key($row);