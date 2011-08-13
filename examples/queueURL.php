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
define('DALMP_QUEUE_URL_DB','myURLqueue.db');
define('DSN', DB_CHARSET.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.':'.DB_PORT.'/'.DB_DATABASE.'?'.DB_CNAME);
require_once '../dalmp.php';

$db = DALMP::getInstance();
$db->debug(1);
$db->database(DSN);

/**
 * send data to a site and expect an 'OK'
 */
$db->http_client('http://post.dalmp.com?foo=bar','OK', 1, 'site1');

/**
 * this can be called on a cron or manually
 */
foreach ($db->readQueueURL() as $key => $value) {
  echo "$value[id]: $value[queue] - $value[url] - $value[expectedValue] - $value[cdate]".$db->isCli(1);
}

/**
 * helpful on CLI
 */
echo $db->isCli(1);
// read all queues
$db->readQueueURL('*', 1);
echo $db->isCli(1);
// read a single queue
$db->readQueueURL('site1', 1);

echo "\n".$timer->getPageLoadTime()." - ".$timer->getMemoryUsage();

?>