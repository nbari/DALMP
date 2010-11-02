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
/**
 * to always store REF on database define DALMP_SESSIONS_REDUNDANCY
 */
#define('DALMP_SESSIONS_REDUNDANCY', false);

define('MEMCACHE_HOSTS','127.0.0.1');
define('DSN', DB_CHARSET.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.':'.DB_PORT.'/'.DB_DATABASE.'?'.DB_CNAME);
require_once '../dalmp.php';

$db = DALMP::getInstance();
$db->debug(1);
$db->debugSessions();
$db->database(DSN);
$db->Cache('memcache',MEMCACHE_HOSTS, true); // true enables MEMCACHE_COMPRESSED
$db->SessionStart(1,'memcache');


$db->FetchMode('ASSOC');

/**
 * here you can declare the user ID so later check how many users are logged in or also avoid users to login twice 
 */
$uid = 1;
$GLOBALS['UID'] = $uid; 

/**
 * get the REF stored on DB or Cache
 */
$rs = $db->getSessionRef($uid);
echo '<pre>';
print_r($rs);
echo '</pre>';
echo $db->isCli(1);

/**
 * delete the REF stored on DB or Cache
 */
$rs = $db->delSessionRef(1);
echo $db->isCli(1);
$_SESSION['test']++;
echo $_SESSION['test'];
echo $db->isCli(1);

if ((mt_rand() % 10) == 0) {
  $db->DALMP_session_regenerate_id(4);  // always after your $GLOBALS
}

echo "\n".$timer->getPageLoadTime()." - ".$timer->getMemoryUsage();

?>