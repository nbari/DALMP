<?php
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------

$user = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASS') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_PORT') ?: '3306';

$db = new DALMP\Database("utf8://$user:$password@$host:$port/dalmp");

$db->FetchMode('ASSOC');
$ors = $db->map('SELECT * FROM City WHERE Name="Toluca"');

echo sprintf('ID: %d CountryCode: %s', $ors->ID, $ors->CountryCode), PHP_EOL;

print_r($ors);

# ------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80), PHP_EOL;
echo str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
