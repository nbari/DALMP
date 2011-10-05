<?php
require_once '../mplt.php';
$timer = new mplt();

define('DB_USERNAME', 'dalmp');
define('DB_PASSWORD', 'password');
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_DATABASE', 'dalmptest');
define('DB_CHARSET', 'utf8');
define('DB1_CNAME', 'db1');
define('MEMCACHE_HOSTS','127.0.0.1');
define('REDIS_HOST','127.0.0.1');
define('REDIS_PORT', 6379);
define('DSN', DB_CHARSET.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.':'.DB_PORT.'/'.DB_DATABASE.'?'.DB1_CNAME);
require_once '../dalmp.php';

$db = DALMP::getInstance();
$db->debug(1);
$db->database(DSN);
$db->Cache('redis',REDIS_HOST, REDIS_PORT);
$db->Cache('memcache',MEMCACHE_HOSTS, true); // true enables MEMCACHE_COMPRESSED


/**
 * it is recommended to set the connection name DB1_CNAME in case later you need to connect to a second database and flush a specific cache
 */
// add this query to the cache group:memcache
$rs = $db->CachePGetAll('memcache', 300, 'SELECT * FROM test WHERE r1=?', 'test', 'group:memcache', DB1_CNAME);

// cache with default timeout 3600 (1 hour), and also added to the cache group:memcache
$rs = $db->CachePGetAll('memcache', 'SELECT * FROM test WHERE r1=?', 'row 2', 'group:memcache', DB1_CNAME);

// the first cache declared is the one used, in this case 'redis', also this query is added to a group called redis
$rs = $db->CachePGetAll('SELECT * FROM test WHERE r2=?', 'col b', 'group:redis', DB1_CNAME);

/**
 * get all the keys stored for a certain group
 */
#$groups = $db->getCache('cache_group', 'group:memcache',DB1_CNAME);
#print_r($groups);

/**
 * randomly flush the chache for group:sessions
 * DALMP groups all the sessions by default on group:sessions
 */
if ((mt_rand() % 10) == 0) {
  $db->CacheFlush('group:sessions');
}

echo PHP_EOL,$timer->getPageLoadTime()," - ",$timer->getMemoryUsage(),PHP_EOL;

?>