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
define('DALMP_DEBUG_FILE', '/tmp/dalmp.log');
require_once '../dalmp.php';

$db = DALMP::getInstance();
$db->debug(1);
$db->database(DSN);

/**
 *  load zone files to mysql
 *  mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root mysql
 */
$db->PExecute('SET time_zone=?','UTC');

$db->FetchMode('ASSOC');

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
 * in here you are limited to only use one type per query
 * this wont work array('i' => 3, 'i' => 4); it will use only one 'i' value
 */
$sql = 'SELECT * FROM test WHERE id=? AND colB=?';
$rs = $db->PGetAll($sql, array('i' => 3, 's' => 'string'));
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

echo $db->isCli(1).$timer->getPageLoadTime()." - ".$timer->getMemoryUsage();
?>