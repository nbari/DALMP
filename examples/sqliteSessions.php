<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------
 
# optional
#define('DALMP_SQLITE_ENC_KEY', 'my sqlite key');

/**
 * name of the $GLOBAL that will be stored on the db as the REF value
 */
define('DALMP_SESSIONS_REF', 'UID');

/**
 * sessions sqlite3 db path
 */
define('DALMP_SESSIONS_SQLITE_DB','/tmp/sessions.db');

$sessions = new DALMP_Sessions();

 
$GLOBALS['UID'] = 1 + $_SESSION['test'];

$_SESSION['test'] = 1 + @$_SESSION['test'];

echo $_SESSION['test'] .' - '.session_id();

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;

?>