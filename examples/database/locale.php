<?php
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------

$user = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASS') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_PORT') ?: '3306';

define('DALMP_MYSQLI_INIT_COMMAND', 'SET time_zone="-05:00"');

$db = new DALMP\Database("utf8://$user:$password@$host:$port/dalmp");

echo "-05:00 time: ", $db->GetOne('SELECT NOW()');

/**
 *  load zone files to mysql
 *  mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root mysql
 */
$db->PExecute('SET time_zone=?','+00:00');

echo PHP_EOL, 'UTC time: ', $db->GetOne('SELECT NOW()');
echo PHP_EOL, 'lc_time_names: ', $db->GetOne('SELECT @@lc_time_names');
echo ': ', $db->PGetOne("SELECT DATE_FORMAT(?,'%W %a %M %b')", '2010-01-01');

$db->PExecute('SET lc_time_names=?', 'es_MX');

echo PHP_EOL, 'lc_time_names: ', $db->GetOne('SELECT @@lc_time_names');
echo ': ', $db->PGetOne("SELECT DATE_FORMAT(?,'%W %a %M %b')", '2010-01-01');

$db->PExecute('SET lc_time_names=?', 'pt_BR');
echo PHP_EOL, 'lc_time_names: ', $db->GetOne('SELECT @@lc_time_names');
echo ': ', $db->PGetOne("SELECT DATE_FORMAT(?,'%W %a %M %b')", '2010-01-01');

# ------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
