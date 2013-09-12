<?php

require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------

/**
 * sqlite queue instance
 */
$queue = new DALMP\Queue(new DALMP\Queue\SQLite('/tmp/dalmp_queue.db'));

echo 'enqueue status: ', var_dump($queue->enqueue('this is a teste')), $timer->isCli(1);

echo 'dequeue all: ', print_r($queue->dequeue()), $timer->isCli(1);

echo 'dequeue only 3: ', print_r($queue->dequeue(3)), $timer->isCli(1);

echo 'delete from queue: ', print_r($queue->delete(3)), $timer->isCli(1);


# ------------------------------------------------------------------------------
echo PHP_EOL, str_repeat('-', 80), PHP_EOL;
$timer->printMarks();
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
