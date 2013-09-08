<?php

require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------

/**
 * memcache cache instance
 */
$memcache = new DALMP\Cache(new DALMP\Cache\Memcache());

/**
 * redis cache instance
 */
$redis = new DALMP\Cache(new DALMP\Cache\Redis());

/**
 * disk cache instance
 */
$disk = new DALMP\Cache(new DALMP\Cache\Disk());

/**
 * database instance
 */
$db = new DALMP\Database('utf8://root@127.0.0.1:3306/dalmp');
$sql = 'SELECT * FROM Country LIMIT 2';

/**
 * Cache for 5 minutes with key: mykey using memcache cache
 */
$db->useCache($memcache);
$rs = $db->CacheGetAll(300, $sql, 'mykey');
$timer->setMark('memcache');
echo count($rs),PHP_EOL;
$rs = $db->CacheGetAll(300, $sql, 'mykey');
$timer->setMark('memcache2');
echo count($rs),PHP_EOL;

/**
 * Cache for 5 minutes with key: mykey using redis cache
 */
$db->debug();
$db->useCache($redis);
$rs = $db->CacheGetAll(300, $sql, 'mykey');
$timer->setMark('redis');
echo count($rs),PHP_EOL;
$rs = $db->CacheGetAll(300, $sql, 'mykey');
$db->debug('off');
$timer->setMark('redis2');
echo count($rs),PHP_EOL;

/**
 * Cache for 5 minutes with key: mykey using disk cache
 */
$db->useCache($disk);
$rs = $db->CacheGetAll(300, $sql, 'mykey');
$timer->setMark('disk');
echo count($rs),PHP_EOL;
$rs = $db->CacheGetAll(300, $sql, 'mykey');
$timer->setMark('disk2');
echo count($rs),PHP_EOL;

/**
 * flush the query $sql with key on DISK cache instance
 */
$db->CacheFlush($sql, 'mykey');

/**
 * flush the query $sql with key only on Redis cache instance
 */
$db->useCache($redis);
$db->CacheFlush($sql, 'mykey');

/**
 * flush all the cache in all instances
 */
foreach (array('memcache', 'redis', 'disk') as $val) {
  $db->useCache(${$val});
  $db->CacheFlush($sql, 'mykey');
}

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80), PHP_EOL;
$timer->printMarks();
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
