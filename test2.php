<?php
// Measure Page Load Time
require_once 'mplt.php';
$timer = new mplt();
/**
 * require the DALMP class
 */
require_once 'lib/dalmp.php';

/**
 * example of a simple connection
 *
 * charset: default system
 * user: dalmp
 * password: password
 * host: 192.168.1.40
 * database: dalmptest
 *
 */
#$type = 'Redis';
#$cache_ns = "DALMP\Cache\\$type";
#$cache = new $cache_ns('/tmp/redis.sock');
#$cache = new $cache_ns();
#$cache = new $cache_ns();
#$cache = new DALMP\Cache\Disk('/tmp/dalmp');
#$cache = new DALMP\Cache\Memcache;

$cache = new DALMP\Cache(new DALMP\Cache\Disk('/tmp/dalmp'));
$log = new DALMP\Logger(0, '/tmp/my.log');
var_dump($cache->set('test', False, 300));
var_dump($cache->get('test'));
print_r($cache->stats());
#$cache->Flush();
$log->log('init');
$log->log('a');
$log->log('b');
$log->getLog();


# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
