<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$cache = new DALMP_Cache('dir');

$cache->set('mykey','my value stored in dir', 30);

echo $cache->get('mykey');

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
