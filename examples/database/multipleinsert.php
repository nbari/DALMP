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
$db->debug();

$values = array(
  array(1,2,3),
  array(1,3),
  array('date','select', 3),
  array('niño','coraçao', 'Ú'),
  array(null,5,7)
);

$rs = $db->multipleInsert('tests', array('col1', 'col2', 'col3'), $values);

var_dump($rs);

print_r($db->FetchMode('ASSOC')->GetALL('SELECT * FROM tests'));

# ------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
