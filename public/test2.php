<?php
$client = new swoole_client(SWOOLE_SOCK_TCP);
if (!$client->connect('121.42.184.177', 9501, -1))
{
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send("liucunzhou\n");
echo $client->recv();
$client->close();