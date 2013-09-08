<?php
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------

$timer->setMark('start');

$password = 'mysql';
$db = new DALMP\Database('utf8://root:'.rawurlencode($password).'@127.0.0.1:3306/dalmp');

$db->FetchMode('NUM');

$sql = 'SELECT * FROM City';
$rs = $db->Execute($sql);

if ($rs) {
  while (($rows = $db->query()) != false) {
	  list($r1,$r2,$r3) = $rows;
	  echo "w1: $r1, w2: $r2, w3: $r3", $db->isCli(1);
  }
}

$timer->setMark('while');

/**
 * doing the same but consuming more memory.
 * Below the returned $rs2 array is not referential. Because of that, the system
 * will use excesive memory. With large columns.
 */
$rs2 = $db->GetAll($sql);
foreach ($rs2 as $value) {
  list($r1,$r2,$r3) = $value;
  echo "f1: $r1, f2: $r2, f3: $r3", $db->isCli(1);
}

$timer->setMark('foreach');

/**
 * prepared statements
 * and array needs to be passed as an argument
 */
$rs = $db->PExecute('SELECT * FROM Country WHERE Continent = ?', 'Europe');
$out = array();
while ($rows = $db->Pquery($out)) {
	print_r($out);
}

$rs = $db->PExecute('UPDATE Country SET code=? WHERE Code="PRT"', 'PRT');

/**
 * Returns the number of rows affected by INSERT, UPDATE, or DELETE query.
 * an UPDATE prepared statement which contains the same data as that already
 * in the database returns 0 for affected_rows
 */
echo $db->getNumOfRowsAffected(), PHP_EOL;

$timer->setMark('stmt');

echo $db->isCli(1);

# ------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80), PHP_EOL;
$timer->printMarks();
echo str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
