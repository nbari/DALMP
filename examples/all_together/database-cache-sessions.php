<?php

require_once '../../src/dalmp.php';

$di = new DALMP\DI();

$user = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASS') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_PORT') ?: '3306';


$DSN = "utf8://$user:$password".'@127.0.0.1/test';

$db = $di->database($DSN);

$redis_cache = $di->cache_redis('127.0.0.1', 6379);

$cache = $di->cache($redis_cache);

$sessions = $di->sessions($di->sessions_redis($redis_cache), 'sha512');
$sessions->regenerate_id(true);

$db->useCache($cache);

$now = $db->CachegetOne('SELECT NOW()');

echo $now, PHP_EOL;

echo session_id();
