<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$db = new DALMP('utf8://root:'.rawurlencode('pass-?/:word').'@mysql2.localbox.org:3306/dalmptest?redis:127.0.0.1:6379');

/**
 *  load zone files to mysql
 *  mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root mysql
 */
$db->PExecute('SET time_zone=?','UTC');

$db->FetchMode('ASSOC');

$sql = 'SELECT Name, Continent FROM Country WHERE Population > ? AND Code LIKE ?';
$rs = $db->PGetAll($sql, 1000000, '%P%');

print_r($rs);
/**
 * status value is 0 or 1 on table
 * NOTICE the use of ===
 */
$sql = 'SELECT status FROM test WHERE id=?';
$rs = $db->PgetOne($sql, 3);
if ($rs === false) { 
	echo "no result".$db->isCli(1);
} elseif ($rs == 0) {
	echo "$rs = 0".$db->isCli(1);
} else {
	echo "$rs > 0".$db->isCli(1);
}

/**
 * passing an array as an argument
 * helpful in cases where searching float values stored on varchar fields
 * $db->PGetAll($sql, array('s' => 99.3, 1));
 */
$sql = 'SELECT * FROM test WHERE id=? AND colB=?';
$rs = $db->PGetAll($sql, array(3, 's' => 'string'));
#print_r($rs);

/**
 * using the Prepare method,
 * Useful when building dynamic queries that require prepared statements
 * The prepare method automatically detect the input type,
 * you can also override this, using something like: Prepare('s','1e1');
 * if no input it will return the array with the prepared statements
 */
$X = 3;
$id = 1;
$db->Prepare($id);
$sql = 'SELECT * FROM test WHERE id=? ';
if ($X == 3) {
	$db->Prepare($X);
	$sql .= 'AND id !=? ';
}
$db->Prepare('s', 'colb');
$sql .= 'AND colB=?';

/**
 * this will produce a query like:
 * "sql: SELECT * FROM test WHERE id=? AND id !=? AND colB=? params:",["iis",1,3,"colb"]
 */

$rs = $db->PgetAll($sql, $db->Prepare());
print_r($rs);

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;

?>