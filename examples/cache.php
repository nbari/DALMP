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


$sql = 'SELECT * FROM test';
/**
 * it is recommended to set the connection name DB1_CNAME in case later you need to connect to a second database and flush a specific cache 
 */
$rs = $db->CacheGetAll('memcache',300, $sql, 'mykey', DB1_CNAME);
$rs = $db->CacheGetAll('redis', 300, $sql, 'mykey', DB1_CNAME);
$rs = $db->CacheGetAll('dir', 300, $sql, 'mykey', DB1_CNAME);
$rs = $db->CacheGetAll(300, $sql, 'mykey', DB1_CNAME);
print_r($rs);

/**
 * flush the query on memcache
 */
$db->CacheFlush($sql,'mykey', DB1_CNAME, 'memcache');

/**
 * flush the query on all the available caches (memcache / redis / dir)
 */
$db->CacheFlush($sql,'mykey', DB1_CNAME);

/**
 * flush all the cache
 */
$db->CacheFlush();

print_r($db->memCacheStats());
print_r($db->redisStats());

echo "\n".$timer->getPageLoadTime()." - ".$timer->getMemoryUsage();

?>