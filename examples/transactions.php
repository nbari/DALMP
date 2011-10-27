<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$db = new DALMP('utf8://root:'.rawurlencode('pass-?/:word').'@mysql2.localbox.org:3306/dalmptest');
$db->debug(1);

$timer->setMark('start');


#$db->Execute('CREATE TABLE t_test (id INT NOT NULL PRIMARY KEY) ENGINE=InnoDB');

$db->StartTrans();
$db->Execute('INSERT INTO t_test VALUES(1)');
  $db->StartTrans();
  $db->Execute('INSERT INTO t_test VALUES(2)');
  print_r($db->GetAll('SELECT * FROM t_test'));
    $db->StartTrans();
    $db->Execute('INSERT INTO t_test VALUES(3)');
    print_r($db->GetAll('SELECT * FROM t_test'));
	  $db->StartTrans();
	  $db->Execute('INSERT INTO t_test VALUES(7)');
	  print_r($db->GetALL('SELECT * FROM t_test'));
	  $db->RollBackTrans();
    $db->CompleteTrans();
  $db->CompleteTrans();
$db->CompleteTrans();

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;

?>