<?php
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------

$db1 = new DALMP\Database('utf8://dalmp:password@127.0.0.1:3306/dalmptest');
$db2 = new DALMP\Database('uft8://root:'.rawurlencode('pass-?/:word').'@remote.localbox.org:3307/dalmptest');

$rs1 = $db1->FetchMode('ASSOC')->getall('SELECT * FROM Country limit 1');
print_r($rs1);

$rs2 = $db2->FetchMode('NUM')->getall('SELECT * FROM City limit 1');
print_r($rs2);

# ------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
