<?php

require_once '../../MPLT.php';
$timer = new MPLT();

/**
 * using composer autoloader PSR-0
 */
require_once '../../vendor/autoload.php';

$cache= new DALMP\Cache\Memcache('127.0.0.1', 11211);

$cache->set('mykey', 'xpto', 300);
var_dump($cache->get('mykey'));

$cache->X()->replace('mykey', 'otpx', false, 300);
var_dump($cache->get('mykey'));

#------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
