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

$db->Execute('CREATE TABLE IF NOT EXISTS t_test2 (id INT NOT NULL PRIMARY KEY, credit DECIMAL(9,2)) ENGINE=InnoDB');
$db->Execute('TRUNCATE TABLE t_test2');
$db->FetchMode('ASSOC');
$db->Execute('INSERT INTO t_test2 VALUES(1, 100)');

for ($i = 1; $i <= 3; ++$i) {
  $pid = pcntl_fork();

  if (!$pid) {
    switch ($i) {
    case 1:
      echo "In process: $i", PHP_EOL;
      $db = new DALMP\Database("utf8://$user:$password@$host:$port/dalmp");

      $db->StartTrans();
      $credit = $db->PGetOne('SELECT credit FROM t_test2 WHERE id=? FOR UPDATE', 1);
      if ($credit > 0) {
        $db->PExecute('UPDATE t_test2 SET credit=credit - ? WHERE id = ?', 100, 1);
      }
      echo "process $i credit: ", $db->PGetOne('SELECT credit FROM t_test2'), PHP_EOL;
      $rs = $db->CompleteTrans();
      echo 'Transaction returned: ', (bool) $rs, PHP_EOL;
      exit($i);
      break;

    case 2:
      echo "In process: 2", PHP_EOL;
      $db = new DALMP\Database("utf8://$user:$password@$host:$port/dalmp");
      $db->StartTrans();
      $credit = $db->PGetOne('SELECT credit FROM t_test2 WHERE id=? FOR UPDATE', 1);
      if ($credit > 0) {
        $db->PExecute('UPDATE t_test2 SET credit=credit - ? WHERE id = ?', 100, 1);
      }
      echo "process $i credit: ", $db->PGetOne('SELECT credit FROM t_test2'), PHP_EOL;
      $rs = $db->CompleteTrans();
      echo 'Transaction returned: ', (bool) $rs, PHP_EOL;
      exit($i);
      break;

    case 3:
      echo "In process: 3", PHP_EOL;
      $db = new DALMP\Database("utf8://$user:$password@$host:$port/dalmp");
      $db->StartTrans();
      $credit = $db->PGetOne('SELECT credit FROM t_test2 WHERE id=? FOR UPDATE', 1);
      if ($credit > 0) {
        $db->PExecute('UPDATE t_test2 SET credit=credit - ? WHERE id = ?', 100, 1);
      }
      echo "process $i credit: ", $db->PGetOne('SELECT credit FROM t_test2'), PHP_EOL;
      $rs = $db->CompleteTrans();
      echo 'Transaction returned: ', (bool) $rs, PHP_EOL;
      exit($i);
    }
  }
}

while (pcntl_waitpid(0, $status) != -1) {
  $status = pcntl_wexitstatus($status);
  echo "Child $status completed", PHP_EOL;
}

# ------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
