<?php
// Measure Page Load Time
require_once 'mplt.php';
$timer = new mplt();
/**
 * require the DALMP class
 */
require_once 'lib/dalmp.php';

$so = new DALMP\Sessions\Files('/tmp/sess');
$so = new DALMP\Sessions\SQLite('/tmp/sessions.db', 'id');
$ses = new DALMP\Sessions($so);


$_SESSION['test'] = 1 + @$_SESSION['test'];

$ses->regenerate_id();

echo $_SESSION['test'];

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
