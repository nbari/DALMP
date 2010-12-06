<?php
require_once '../mplt.php';
$timer = new mplt();

define('DB_USERNAME', 'dalmp');
define('DB_PASSWORD', 'password');
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_DATABASE', 'dalmptest');
define('DB_CHARSET', 'utf8'); 
define('DB_CNAME', 'db1');
define('DSN', DB_CHARSET.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.':'.DB_PORT.'/'.DB_DATABASE.'?'.DB_CNAME);
require_once '../dalmp.php';

$timer->setMark('start');

$db = DALMP::getInstance();
$db->debug(1);
$db->debugSessions();
$db->database(DSN);
 
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


echo $db->isCli(1);
echo $timer->getMemoryUsage();
?>