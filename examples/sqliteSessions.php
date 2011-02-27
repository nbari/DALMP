<?php
require_once '../mplt.php';
$timer = new mplt();

define('DB_USERNAME', 'dalmp');
define('DB_PASSWORD', 'password');
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_DATABASE', 'dalmptest');
define('DB_CHARSET', 'utf8');
define('DB_CNAME', 'db1');
define('DALMP_SESSIONS_REF', 'UID');
define('DALMP_SESSIONS_MAXLIFETIME',1800);
define('DALMP_SESSIONS_SQLITE_DB','/tmp/sessions.db');
define('DSN', DB_CHARSET.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.':'.DB_PORT.'/'.DB_DATABASE.'?'.DB_CNAME);
require_once '../dalmp.php';

$db = DALMP::getInstance();
$db->debug(1);
$db->debugSessions();
$db->database(DSN);
$db->SessionStart(1,'sqlite');

$GLOBALS['UID'] = 1 + $_SESSION['test'];

$_SESSION['test'] = 1 + $_SESSION['test'];

echo $_SESSION['test'] .' - '.session_id();
?>