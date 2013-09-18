<?php
error_reporting(-1);
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------
$user = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASS') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_HOST') ?: '3306';

$db = new DALMP\Database("utf8://$user:$password@$host:$port/dalmp");

$db->StartTrans();
echo 'select ...', PHP_EOL;
echo 'valor do select max: ', $db->PGetOne('SELECT MAX(id) from t_test2'), PHP_EOL;
echo 'update', PHP_EOL;
#$db->Execute('DELETE from t_test2');
$db->PExecute('UPDATE t_test2 set foo=? WHERE id = ?', 'this should not be written', 1);
echo ' waiting for update',PHP_EOL;
echo 'valor do select max: ', $db->PGetOne('SELECT MAX(id) from t_test2'), PHP_EOL;
$rs = $db->CompleteTrans();
echo 'Transaction returned: ', var_dump($rs);

# ------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
