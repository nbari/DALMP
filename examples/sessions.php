<?php
require_once '../mplt.php';
$timer = new mplt();

/**
 * -----------------------------------------------------------------------------------------------------------------
 *
-- ----------------------------
--  Table structure for `dalmp_sessions`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dalmp_sessions` (
  `sid` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `expiry` int(11) unsigned NOT NULL DEFAULT '0',
  `data` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `ref` varchar(255) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sid`),
  KEY `index` (`ref`,`sid`,`expiry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 *
 * -----------------------------------------------------------------------------------------------------------------
 */

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

if ((mt_rand() % 10) == 0) {
  $db->DALMP_session_regenerate_id(4);  // always after your $GLOBALS
}

echo PHP_EOL,$timer->getPageLoadTime()," - ",$timer->getMemoryUsage(),PHP_EOL;

?>