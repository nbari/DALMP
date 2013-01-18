<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$cache = new DALMP_Cache('memcache');
$cache->host('127.0.0.1')->port('11211');
$cache->set('mykey','my value', 30);

echo $cache->get('mykey');

print_r ($cache->stats());

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
