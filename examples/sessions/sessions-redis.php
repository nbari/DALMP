<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

/**
 * start the cache object using redis
 */
$cache = new DALMP_Cache('redis');

/**
 * define sessions reference globals var name
 */
define('DALMP_SESSIONS_REF', 'ID');

/**
 * to store sessions on redis you need to pass the $cache DALMP_Cache object if not defaults to sqlite
 */
$sessions = new DALMP_Sessions($cache);

/**
 * fake id of user to be 37
 */
$GLOBALS['ID'] = 37;

/**
 * randomly aprox avery 10 hits regenerate the id
 */
if ((mt_rand() % 10) == 0) {
  /**
   * parameter int 4 means to use the full 4 IPv4 (255.255.255.255) blocks
   */
  $sessions->regenerate_id(4);
}

$_SESSION['test'] = 1 + @$_SESSION['test'];

/**
 * print all references
 */
print_r($sessions->getSessionsRefs());

/**
 * print reference where id = 37
 */
if ($rs = $sessions->getSessionRef(37)) {
  /**
   * prints all the sessions with REF = 37
   * Based on your code, this could be the times the user as logged in and is active
   */
  print_r($rs);
} else {
  echo 'no ref found';
}

// $sessions->delSessionRef(37);
# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
