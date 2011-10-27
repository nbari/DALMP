<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------


$db1 = new DALMP('utf8://dalmp:password@mysql1.localbox.org:3306/dalmptest');
$db2 = new DALMP('mysql://root:'.rawurlencode('pass-?/:word').'@mysql2.localbox.org:3306/dalmptest');

$rs1 = $db1->FetchMode('ASSOC')->getall('SELECT * FROM Country limit 1');
print_r($rs1);

$rs2 = $db2->FetchMode('NUM')->getall('SELECT * FROM City limit 1');

print_r($rs2);

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;

?>