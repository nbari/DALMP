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
$db = new DALMP\Database('utf8://127.0.0.1/test');
try {
  $rs = $db->getOne('SELECT now()');
} catch (Exception $e) {
  print_r($e->getMessage());
}

echo $rs, PHP_EOL;

print $db;

echo $db; // will print: DALMP :: connected to: db4, Character set: utf8, Localhost via UNIX socket,...

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
