<?php
require_once '../../vendor/autoload.php';

$redis = new DALMP\Cache\Redis('127.0.0.1', 6381);
$cache = new DALMP\Cache($redis);

print_r($redis->get('mykey'));
