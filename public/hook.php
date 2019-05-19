<?php
$valid_token = 'oxcrm';
$valid_ip = array('127.0.0.1'); //这里填你的gitlab服务器ip
$client_token = $_SERVER['HTTP_X_GITLAB_TOKEN'];
$client_ip = $_SERVER['REMOTE_ADDR'];
if ($client_token !== $valid_token) die('Token mismatch!');
if (!in_array($client_ip, $valid_ip)) die('Ip mismatch!');
exec("cd /data/platform; git pull origin master");
// exec("cd /var/www/html/; git pull origin master 2>&1", $output);
// var_dump($output);
