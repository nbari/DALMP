<?php
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------
$di = new DALMP\DI();

$user = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASS') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_HOST') ?: '3306';

$db = $di->database("utf8://$user:$password@$host:$port/dalmp");
$db->debug();

echo 'connect ', var_dump($db->connect());
sleep(3);
echo 'ping: ', var_dump($db->X()->ping());
echo 'thread_safe: ', var_dump($db->X()->thread_safe());
echo 'client_info: ', var_dump($db->X()->get_client_info());
echo 'client_version: ', var_dump($db->X()->client_version);
echo 'server_info: ', var_dump($db->X()->server_info);
echo 'server_version: ', var_dump($db->X()->server_version);

echo $timer->isCli(1), $db, $timer->isCli(1);

echo $timer->isCli(1), $db->GetOne('SELECT NOW()'), $timer->isCli(1);

$db->closeConnection();

echo 'is connected: ', var_dump($db->isConnected());
sleep(3);

echo 'ping: ', var_dump($db->X()->ping());
echo $timer->isCli(1), $db->GetOne('SELECT NOW()'), $timer->isCli(1);
sleep(3);

# ------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
