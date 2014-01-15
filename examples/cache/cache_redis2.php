<?php

require_once '../../MPLT.php';
$timer = new MPLT();

/**
 * using composer autoloader PSR-0
 */
require_once '../../src/dalmp.php';

$cache = new DALMP\Cache\Redis('127.0.0.1', 6379);

$cache->set('mykey', 'xpto', 300);
var_dump($cache->get('mykey'));

$cache->X()->HSET('myhash', 'field1', 'hello');
var_dump($cache->X()->HGET('myhash', 'field1'));
var_dump($cache->X()->HGETALL('myhash'));

#------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
