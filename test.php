<?php
$str = '2020/3/18-0010040';
$index = strpos($str, '-');
echo substr($str, 0, $index);