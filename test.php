<?php
$star = '';
$arr = explode(',', $star);
print_r([]);
exit;

$json = '{"source":["179"],"coo":["6"],"assistant":["289","565"],"ceo":["575"],"cashier":["717"],"accounting":["587"]}';

function getNextConfirmItem($current='source', $sequence)
{
    foreach ($sequence as $key => $value) {
        next($sequence);
        if ($key == $current) break;
    }

    // next($arr);
    return key($sequence);
}

$sequence = json_decode($json, true);
$result = getNextConfirmItem('accounting', $sequence);
var_dump(is_null($result));
echo key($sequence);

// echo $key = key($row);