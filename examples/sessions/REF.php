<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$db = new DALMP('utf8://dalmp:password@192.168.1.40:3306/dalmptest');

/**
 * name of the REF field global var
 */
define('DALMP_SESSIONS_REF', 'UID');
/**
 * to store sessions on mysql you need to pass the $db DALMP object
 */
$sessions = new DALMP_Sessions($db);

/**
 * here you can declare the user ID so later check how many users are logged in or also avoid users to login twice
 */
$uid = 1;
$GLOBALS['UID'] = $uid;

if ((mt_rand() % 10) == 0) {
  $sessions->regenerate_id(4);  // always after your $GLOBALS
}

/**
 * get the REF stored on DB or Cache
 */
$rs = $sessions->getSessionRef($uid);
echo '<pre>';
print_r($rs);
echo '</pre>';
echo $db->isCli(1);

/**
 * delete the REF stored on DB or Cache
 */
$rs = $sessions->delSessionRef(1);
echo $db->isCli(1);
@$_SESSION['test']++;
echo $_SESSION['test'];
echo $db->isCli(1);

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
