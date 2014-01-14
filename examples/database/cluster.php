<?php
error_reporting(-1);
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$user = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASS') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_PORT') ?: '3306';

$dsns = array(
  'utf8://root:mysql@127.0.0.1/dalmp3',
  'utf8://root:mysql@127.0.0.1/dalmp4',
  'utf8://root:mysql@127.0.0.1/dalmp5',
  "utf8://$user:$password@$host:$port/dalmp");

$cdb = new DALMP\Database($dsns[0]);

$result = false;
while (!$result) {
  try {
    $result = $cdb->FetchMode('ASSOC')->PgetAll('SELECT NOW()');
  } catch (\Exception $e) {
    array_shift($dsns);
    $cdb = new DALMP\Database($dsns[0]);
    if ($dsns) {
      $cdb = new DALMP\Database($dsns[0]);
    } else {
      throw $e;
    }
    echo $e->getMessage(), PHP_EOL;
  }
}
print_r($result);

#------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
