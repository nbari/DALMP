<?php
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------
$user = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASS') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_HOST') ?: '3306';

$db = new DALMP\Database("utf8://$user:$password@$host:$port/dalmp");

$db->Execute('CREATE TABLE IF NOT EXISTS t_test (id INT NOT NULL PRIMARY KEY) ENGINE=InnoDB');
$db->Execute('TRUNCATE TABLE t_test');
$db->FetchMode('ASSOC');

try {
  $db->StartTrans();
  $db->Execute('INSERT INTO t_test VALUES(1)');
    $db->StartTrans();
    $db->Execute('INSERT INTO t_test VALUES(2)');
    print_r($db->GetAll('SELECT * FROM t_test'));
      $db->StartTrans();
      $db->Execute('INSERT INTO t_test VALUES(3)');
      print_r($db->GetAll('SELECT * FROM t_test'));
        $db->debug();
        $db->StartTrans();
        $db->Execute('INSERT INTO t_test VALUES(7)');
        print_r($db->GetALL('SELECT * FROM t_test'));
        $db->RollBackTrans();
      print_r($db->GetALL('SELECT * FROM t_test'));
      $db->CompleteTrans();
    $db->CompleteTrans();
  $db->CompleteTrans();
} catch (\Exception $e) {
  echo $e->getMessage(),PHP_EOL;
}

# ------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
