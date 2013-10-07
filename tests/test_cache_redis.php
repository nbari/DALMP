<?php

require_once 'test_cache_base.php';

/**
 * Test for Cache\Redis
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class test_cache_redis extends test_cache_base
{
  /**
   * Cache instance
   *
   * @var Cache
   */
  protected $cache;

  public function setUp()
  {
    if (!extension_loaded('redis')) {
      $this->markTestSkipped('The redis extension is not available.');
    }
    $this->cache = new DALMP\Cache\Redis;
  }

  public function testAttributes()
  {
    $this->assertClassHasAttribute('host', 'DALMP\Cache\Redis');
    $this->assertClassHasAttribute('port', 'DALMP\Cache\Redis');
    $this->assertClassHasAttribute('timeout', 'DALMP\Cache\Redis');
    $this->assertClassHasAttribute('cache', 'DALMP\Cache\Redis');
  }

  public function testX()
  {
    $this->assertContainsOnlyInstancesOf('DALMP\Cache\Redis', array($this->cache));
  }

  public function testPing()
  {
    $this->assertEquals('+PONG', $this->cache->X()->ping());

    $count = 1000;
    while ($count --) {
      $this->assertEquals('+PONG', $this->cache->X()->ping());
    }
  }

}
