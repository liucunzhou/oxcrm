<?php
$str = 'think|a:1:{s:4:"user";a:29:{s:2:"id";i:717;s:3:"ids";s:0:"";s:7:"role_id";s:2:"29";s:13:"department_id";i:54;s:8:"admin_id";i:0;s:8:"nickname";s:11:"13651962131";s:8:"realname";s:9:"关慧芳";s:8:"password";s:32:"e10adc3949ba59abbe56e057f20f883e";s:8:"dingding";s:0:"";s:6:"mobile";s:11:"13651962131";s:9:"telephone";s:0:"";s:5:"email";s:0:"";s:4:"sort";i:0;s:8:"is_valid";i:1;s:11:"delete_time";i:0;s:11:"modify_time";s:16:"2020-05-05 10:58";s:11:"create_time";s:16:"2020-04-09 18:03";s:7:"user_no";s:32:"15f94ac8d3a5dd56ff35be56851aae50";s:8:"user_nos";N;s:7:"in_time";N;s:13:"family_mobile";N;s:7:"id_card";N;s:6:"avatar";N;s:3:"sex";N;s:20:"origin_department_id";N;s:11:"province_id";i:0;s:7:"city_id";i:0;s:4:"uuid";s:0:"";s:8:"position";N;}}';
$arr = unserialize($str);

print_r($arr);
