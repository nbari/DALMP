<?php

require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

/**
 * define the cache on the DSN query: type:host:port:compression
 * example:
 * redis/memcache on localhost
 * ?redis
 *   
 * redis server on host 192.168.1.1 default port 777
 * ?redis:192.168.1.1:777
 *   
 * memcache server on host 192.168.1.1 default port and with compression
 * ?memcache:192.168.1.1::1
 *   
 */ 

 
# DSN with memcache on host 127.0.0.1, port 11211 and compression enabled
$db = new DALMP('utf8://root:'.rawurlencode('pass-?/:word').'@mysql2.localbox.org:3306/dalmptest?memcache:127.0.0.1:11211:1'); 

$sql = 'SELECT * FROM Country';

/**
 * Cache for 5 minutes with key: mykey
 */
$rs = $db->CacheGetAll(300, $sql, 'mykey');
print_r($rs);

/**
 * flush the query $sql with key
 */
$db->CacheFlush($sql, 'mykey');

/**
 * flush all the cache
 */

#$db->CacheFlush();


# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;

?>