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
define('DALMP_SESSIONS_REF', 'UID');
define('DALMP_QUEUE_DB','myqueue.db');
define('DSN', DB_CHARSET.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.':'.DB_PORT.'/'.DB_DATABASE.'?'.DB_CNAME);
require_once '../dalmp.php';

$db = DALMP::getInstance();
$db->debug(1);
$db->database(DSN);

/**
 * In case something goes wrong, the database is unavailable, fields missing,  etc, you can save 'sql query' and later process it again.
 */
$sql = "INSERT INTO table SET r1='test'";
$rs = $db->Execute($sql);
if (!$rs) {
  $db->queue($sql);
}

/**
 * this can be called on a cron or manually
 */
foreach ($db->readQueue() as $key => $value) {
  $sql = base64_decode($value['sql']);
  $cdate = $value['cdate'];
  echo "$sql - $cdate".$db->isCli(1);
  /**
   * try to re-execute the query
   */
  # $rs = $db->Execute($sql);
}

/**
 * helpful on CLI
 */
$db->readQueue(1);

echo "\n".$timer->getPageLoadTime()." - ".$timer->getMemoryUsage();

?>