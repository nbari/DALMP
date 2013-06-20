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
$type = 'Redis';
$cache_ns = "DALMP\Cache\\$type";
$cache = new $cache_ns('/tmp/redis.sock');
var_dump($cache->set('test', False, 300));
var_dump($cache->get('test'));
# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
