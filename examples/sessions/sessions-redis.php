<?php
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$cache= new DALMP\Cache\Redis('127.0.0.1', 6379);

$handler = new DALMP\Sessions\Redis($cache, 'UID');

$sessions = new DALMP\Sessions($handler, 'sha512');

/**
 * your login logic goes here, for example suppose a user logins and has user id=37
 * therefore you store the user id on the globals UID.
 */
$GLOBALS['UID'] = 37;

/**
 * To check if there is no current user logged in you could use:
 */
if ($sessions->getSessionRef($GLOBALS['UID'])) {
    // user is online
    exit('user already logged');
} else {
    $sessions->regenerate_id(true);
}

/**
 * You can use $_SESSIONS like always
 */
$_SESSIONS['foo'] = 'bar';

echo session_id();
# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80), PHP_EOL, 'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
