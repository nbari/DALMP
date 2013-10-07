<?php
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------

$user = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASS') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_HOST') ?: '3306';

/**
 * Cache engine defined on DSN
 */
$db = new DALMP\Database("utf8://$user:$password@$host:$port/dalmp?redis:127.0.0.1:6379");

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
 * lazy connection test query DB only when needed
 */
$db->debug();
$db->closeConnection();

/**
 * Cache for 5 minutes, group A
 */
$rs = $db->CachePGetAll('SELECT * FROM Country WHERE Region = ?', 'Caribbean');
echo count($rs), PHP_EOL;
$timer->setMark('lazy');

/**
 * flush only group A
 */
$db->CacheFlush('group:A');

/**
 * Cache for 5 minutes, group A
 */
$rs = $db->CachePGetAll(300,'SELECT * FROM Country WHERE Region = ?', 'Caribbean', 'group:A');
echo count($rs), PHP_EOL;
$timer->setMark('connect');

# ------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80), PHP_EOL;
$timer->printMarks();
echo str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
