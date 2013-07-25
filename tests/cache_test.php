<?php
# ../bin/phpunit --verbose cache_test.php

class CacheRedisTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    $this->cache = new DALMP\Cache\Redis;
  }

  public function testSet() {
    $this->assertClassHasAttribute('host', 'DALMP\Cache\Redis');
#    $this->assertClassHasAttribute('port', 'DALMP\Cache\Redis');
  }

}
