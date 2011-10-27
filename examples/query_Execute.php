<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$timer->setMark('start');

$db = new DALMP('utf8://root:'.rawurlencode('pass-?/:word').'@mysql2.localbox.org:3306/dalmptest');

$db->FetchMode('NUM');

$sql = 'SELECT * FROM City';
$rs = $db->Execute($sql);

if($rs) {
  while (($rows = $db->query()) != false){
	  list($r1,$r2,$r3) = $rows;
	  echo "w1: $r1, w2: $r2, w3: $r3",$db->isCli(1);
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
  echo "f1: $r1, f2: $r2, f3: $r3",$db->isCli(1);
}

$timer->setMark('foreach');

echo $db->isCli(1);
foreach($timer->getPageLoadTime(1) as $key=>$mark){
	echo "$key - $mark\n";
}

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;

?>