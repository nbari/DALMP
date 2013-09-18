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

$db->Execute('CREATE TABLE IF NOT EXISTS t_test2 (id INT NOT NULL PRIMARY KEY, foo VARCHAR(255)) ENGINE=InnoDB');
$db->Execute('TRUNCATE TABLE t_test2');
$db->FetchMode('ASSOC');
$db->Execute('INSERT INTO t_test2 VALUES(1, "update me")');

$db->StartTrans();
$db->PExecute('SELECT MAX(id) from t_test2 for update');
sleep(5);
$db->PExecute('UPDATE t_test2 set foo=?, id= 3 WHERE id = ?', 'block update', 1);
echo 'valor do select max: ', $db->PGetOne('SELECT MAX(id) from t_test2'), PHP_EOL;
sleep(10);
$rs = $db->CompleteTrans();
echo 'Transaction returned: ', var_dump($rs);

# ------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
