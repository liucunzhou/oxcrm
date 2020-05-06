<?php
$arr = [];

$a1 = array_column($arr, 'totals');

$sum = array_sum($a1);

echo $sum;
print_r($a1);