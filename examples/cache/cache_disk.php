<?php

require_once '../../MPLT.php';
$timer = new MPLT();

/**
 * using composer autoloader PSR-0
 */
require_once '../../vendor/autoload.php';

$cache = new DALMP\Cache\Disk('/tmp/my_cache_path');

$cache->set('mykey', 'xpto', 300);
var_dump($cache->get('mykey'));

print_r($cache->stats());
#------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
