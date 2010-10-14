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
define('MEMCACHE_HOSTS','127.0.0.1');
define('DSN', DB_CHARSET.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.':'.DB_PORT.'/'.DB_DATABASE.'?'.DB_CNAME);
require_once '../dalmp.php';

$db = DALMP::getInstance();
$db->debug(1);
$db->debugSessions();
$db->database(DSN);
$db->Cache('memcache',MEMCACHE_HOSTS, true); // true enables MEMCACHE_COMPRESSED
$db->SessionStart(1,'memcache');

if ((mt_rand() % 10) == 0) {
  $db->DALMP_session_regenerate_id(4);
}

$db->FetchMode('ASSOC');

$GLOBALS['UID'] = 1;

$_SESSION['test'] = 1 + $_SESSION['test'];

#$rs = $db->CachePGetRow('SELECT * FROM dalmp_sessions WHERE ref=?', $GLOBALS['UID']);
$rs = $db->PGetRow('SELECT * FROM dalmp_sessions WHERE ref=?', $GLOBALS['UID']);

print_r($rs);
echo $db->isCli(1);
echo session_id();
echo $db->isCli(1);
echo $_SESSION['test'];
echo $db->isCli(1);
echo '<pre>';
print_r($db->memCacheStats());
echo $db->isCli(1);
echo "\n\n".$timer->getPageLoadTime()."\n";
