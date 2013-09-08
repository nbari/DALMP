<?php
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$password = 'mysql';
$db = new DALMP\Database('utf8://root:'.rawurldecode($password).'@127.0.0.1/dalmp');

/**
 * use redis as cache engine
 */
$db->useCache(new DALMP\Cache(new DALMP\Cache\Redis('127.0.0.1', 6379)));

$db->FetchMode('ASSOC');

/**
 * Cache for 5 minutes, group A
 */
$rs = $db->CachePGetAll(300,'SELECT * FROM Country WHERE Region = ?', 'Caribbean', 'group:A');
echo count($rs), PHP_EOL;
$timer->setMark('300');

/**
 * Cache for 1 day (86400 seconds), group B
 */
$rs = $db->CachePGetAll(86400, 'SELECT * FROM Country WHERE Continent = ?', 'Europe', 'group:B');
echo count($rs), PHP_EOL;
$timer->setMark('86400');

/**
 * Cache for 1 hour (default), group C
 */
$rs = $db->CachePGetAll('SELECT * FROM Country WHERE Population <= ?', 100000, 'group:C');
echo count($rs), PHP_EOL;
$timer->setMark('default');

/**
 * Cache for 5 minutes, group A
 */
$rs = $db->CachePGetAll(300,'SELECT * FROM Country WHERE Region = ?', 'Caribbean', 'group:A');
echo count($rs), PHP_EOL;
$timer->setMark('300-2');

/**
 * flush only group A
 */
$db->CacheFlush('group:A');

/**
 * Cache for 5 minutes, group A
 */
$rs = $db->CachePGetAll(300,'SELECT * FROM Country WHERE Region = ?', 'Caribbean', 'group:A');
echo count($rs), PHP_EOL;
$timer->setMark('300-3');


#------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80), PHP_EOL;
$timer->printMarks();
echo str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
