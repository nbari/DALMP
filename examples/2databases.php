<?php
require_once '../mplt.php';
$timer = new mplt();

define('DB1_USERNAME', 'dalmp');
define('DB1_PASSWORD', 'password');
define('DB1_HOST', 'localhost');
define('DB1_PORT', 3306);
define('DB1_DATABASE', 'dalmptest');
define('DB1_CHARSET', 'utf8'); 
define('DB1_CNAME', 'db1');
define('DB2_USERNAME', 'dalmp');
define('DB2_PASSWORD', 'password');
define('DB2_HOST', 'localhost');
define('DB2_PORT', 3306);
define('DB2_DATABASE', 'dalmptest2');
define('DB2_CHARSET', 'utf8'); 
define('DB2_CNAME', 'db2');
define('DALMP_SESSIONS_REF', 'UID');
define('MEMCACHE_HOSTS','127.0.0.1');
define('DSN1', DB1_CHARSET.'://'.DB1_USERNAME.':'.DB1_PASSWORD.'@'.DB1_HOST.':'.DB1_PORT.'/'.DB1_DATABASE.'?'.DB1_CNAME);
define('DSN2', DB2_CHARSET.'://'.DB2_USERNAME.':'.DB2_PASSWORD.'@'.DB2_HOST.':'.DB2_PORT.'/'.DB2_DATABASE.'?'.DB2_CNAME);
require_once '../dalmp.php';

$db = DALMP::getInstance();
$db->debug(1);
$db->debugSessions();
$db->database(DSN1);
$db->database(DSN2);
$db->Cache('memcache',MEMCACHE_HOSTS, true); // true enables MEMCACHE_COMPRESSED
$db->SessionStart(1,'memcache','db1'); // store sessions on database 1

if ((mt_rand() % 10) == 0) {
  $db->DALMP_session_regenerate_id(4);
}

$db->FetchMode('ASSOC');

$GLOBALS['UID'] = 1;

$_SESSION['test'] = 1 + $_SESSION['test'];

#$rs = $db->CachePGetRow('SELECT * FROM dalmp_sessions WHERE ref=?', $GLOBALS['UID']);
$rs = $db->PGetRow('SELECT * FROM dalmp_sessions WHERE ref=?', $GLOBALS['UID'], 'db1');
print_r($rs);
echo $db->isCli(1);

$rs = $db->GetRow('SELECT * FROM test', 'db2'); 
print_r($rs);
echo $db->isCli(1);
echo $_SESSION['test'];
echo $db->isCli(1);
echo "\n".$timer->getPageLoadTime()." - ".$timer->getMemoryUsage();

?>