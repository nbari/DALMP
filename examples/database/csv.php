<?php
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------

$db = new DALMP\Database('utf8://root:'.rawurlencode('mysql').'@127.0.0.1:3306/dalmp');

/**
 * CSV - export to comma separated values.
 */

// simple query
$db->csv("SELECT * FROM Country WHERE Continent = 'Europe'");

// prepared statements
$db->csv('SELECT * FROM Country WHERE Continent = ?', 'Europe');

# ------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
