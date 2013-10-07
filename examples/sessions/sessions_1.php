<?php
require_once '../vendor/autoload.php';
$timer = new DALMP\MPLT();
# -----------------------------------------------------------------------------------------------------------------

/**
 * to store sessions on mysql you need to pass the $db DALMP object if not defaults to sqlite
 */
$sessions = new DALMP\Sessions();

$GLOBALS['UID'] = 1;

if ((mt_rand() % 10) == 0) {
  $sessions->regenerate_id(4);
}

$_SESSION['test'] = 1 + @$_SESSION['test'];

echo $_SESSION['test'];

echo session_id();

echo '<pre>';
# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
