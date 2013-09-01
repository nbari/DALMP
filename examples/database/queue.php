<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

/**
 * path of the queue
 */
define('DALMP_QUEUE_DB','/tmp/queue.db');

# optional if you want to encrypt the sqlite db
#define('DALMP_SQLITE_ENC_KEY', 'na1ujhrjhqev{5#nyxx~oaV9aqrf3kll');

$db = new DALMP('utf8://root@localbox/dalmptest');

/**
 * In case something goes wrong, the database is unavailable, fields missing,  etc, you can save 'sql query' and later process it again.
 */
$sql = "INSERT INTO testX SET colA=(NOW())";
try {
  $rs = $db->Execute($sql);
} catch (Exception $e) {
  $db->queue($sql, 'my-queue');
}

/**
 * Save some $_POST/$_GET data in json format
 */
$get = array('uuid' => $db->UUID(), 'cdate' => @date('c'), 'field1' => 1, 'field2' => 2);
$db->queue(json_encode($get), 'json');

/**
 * this can be called on a cron or manually
 */
foreach ($db->readQueue() as $key => $value) {
  $queue = $value['queue'];
  $data = base64_decode($value['data']);
  $cdate = $value['cdate'];
  echo "$queue - $data - $cdate".$db->isCli(1);
  /**
   * try to re-execute the query
   */
  # $rs = $db->Execute($data);
}

/**
 * helpful on CLI
 */
echo $db->isCli(1);
// read all queues
$db->readQueue('*', 1);
echo $db->isCli(1);
// read only json queue
$db->readQueue('json', 1);

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
