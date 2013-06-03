<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$db = new DALMP('utf8://root:'.rawurlencode('pass-?/:word').'@mysql2.localbox.org:3306/dalmptest');

foreach ($db->GetCol('SHOW TABLES') as $table) {
  $rs = $db->Execute("OPTIMIZE TABLE $table");
  echo "optimizing $table: $rs",PHP_EOL;
  $rs = $db->Execute("REPAIR TABLE $table QUICK");
  echo "repairing $table: $rs",PHP_EOL;
}

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
