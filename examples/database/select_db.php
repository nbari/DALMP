<?php
error_reporting(-1);
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$user = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASS') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_PORT') ?: '3306';

$dsn = "utf8://$user:$password@$host:$port/dalmp";

$db = new DALMP\Database($dsn);

$rs = $db->GetOne('SELECT DATABASE()');
echo $rs, PHP_EOL;

$db->X()->select_db('mysql');

$rs = $db->GetOne('SELECT DATABASE()');
echo $rs, PHP_EOL;

#------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
