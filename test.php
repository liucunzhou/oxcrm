<?php
echo 'today:'.date('Y-m-d H:i:s', strtotime('today'));
echo "\n";
echo 'yesterday:'.date('Y-m-d H:i:s', strtotime('yesterday'));
echo "\n";
echo 'tomorrow:'.date('Y-m-d H:i:s', strtotime('tomorrow'));

echo "\n";
echo 'week:'.date('Y-m-d H:i:s', strtotime('last sunday') + 86400);
echo "\n";
echo 'month:'.date('Y-m-d H:i:s', strtotime(date('Y-m-01')));