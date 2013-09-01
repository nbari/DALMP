<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$cache = new DALMP_Cache('redis');

$cache->set('mykey','my value', 30);

echo $cache->get('mykey'),PHP_EOL;

/**
 * php >= 5.4 style defining host and port
 */
// $cache = (new DALMP_Cache('redis'))->host('127.0.0.1')->port('6379');
// print_r( $cache->stats() );

$cache->X()->HSET('myhash', 'field1', 'hello');
echo $cache->X()->HGET('myhash', 'field1');

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
