<?php
// Measure Page Load Time
require_once 'mplt.php';
$timer = new mplt();
/**
 * require the DALMP class
 */
require_once 'lib/dalmp.php';

#$so = new DALMP\Sessions\Files('/tmp/sess');
#$so = new DALMP\Sessions\Files();
#$so = new DALMP\Sessions\SQLite('/tmp/sessions.db');
#$so = new DALMP\Sessions\Memcache(new DALMP\Cache\Memcache);
#$so = new DALMP\Sessions\Memcache(new DALMP\Cache\Memcache);
$so = new DALMP\Sessions\Redis(new DALMP\Cache\Redis);
$ses = new DALMP\Sessions($so);

$GLOBALS['UID'] = 3;

$_SESSION['test'] = 1 + @$_SESSION['test'];

if ((mt_rand() % 10) == 0) {
  $ses->regenerate_id();
}

echo $_SESSION['test'].' -- '.session_id();

$rs = $ses->getSessionsRefs();
#$rs = $ses->getSessionRef(3);
#$rs = $ses->delSessionRef(3);

echo '<pre>';
print_r($rs);

# -----------------------------------------------------------------------------------------------------------------
if (php_sapi_name() != 'cli') echo '<pre>';
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
