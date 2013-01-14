<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$db = new DALMP('utf8://root:'.rawurlencode('pass-?/:word').'@mysql2.localbox.org:3306/dalmptest?redis:127.0.0.1:6379');

$db->FetchMode('ASSOC');

/**
 * Cache for 5 minutes, group A
 */
$rs = $db->CachePGetAll(300,'SELECT * FROM Country WHERE Region = ?', 'Caribbean', 'group:A');
#print_r($rs);

/**
 * Cache for 1 day (86400 seconds), group B
 */
$rs = $db->CachePGetAll(86400, 'SELECT * FROM Country WHERE Continent = ?', 'Europe', 'group:B');
#print_r($rs);

/**
 * Cache for 1 hour (default), group C
 */
$rs = $db->CachePGetAll('SELECT * FROM Country WHERE Population <= ?', 100000, 'group:C');
#print_r($rs);

/**
 * flush only group A
 */
$db->CacheFlush('group:A');

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
