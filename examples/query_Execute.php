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
 

$db->FetchMode('NUM');

$sql = 'SELECT * FROM test';
$rs = $db->Execute($sql);

if($rs) {
  while($rows = $db->FetchRow()){
	list($r1,$r2,$r3) = $rows;
	echo "r1: $r1, r2: $r2, r3: $r3\n";
  }	
}

$timer->setMark('while');

/**
 * doing the same but consuming more memory.
 * Below the returned $rs2 array is not referential. Because of that, the system will use excesive memory. With large columns.
 */
$rs2 = $db->GetAll($sql);
foreach ($rs2 as $value) {
  list($r1,$r2,$r3) = $value;
  echo "r1: $r1, r2: $r2, r3: $r3\n";
}

echo $db->isCli(1);
foreach($timer->getPageLoadTime(1) as $key=>$mark){
	echo "$key - $mark\n";
}
echo $db->isCli(1);
echo $timer->getMemoryUsage();
?>