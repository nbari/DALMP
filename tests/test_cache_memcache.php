<?php

require_once 'test_cache_base.php';

/**
 * Test for Cache\Memcache
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class test_cache_memcache extends test_cache_base {

  /**
   * Cache instance
   *
   * @var Cache
   */
  protected $cache;

  public function setUp() {
    if (!extension_loaded('memcache')) {
      $this->markTestSkipped('The memcache extension is not available.');
    }
    $this->cache = new DALMP\Cache\Memcache;
  }

  public function testAttributes() {
    $this->assertClassHasAttribute('host', 'DALMP\Cache\Memcache');
    $this->assertClassHasAttribute('port', 'DALMP\Cache\Memcache');
    $this->assertClassHasAttribute('timeout', 'DALMP\Cache\Memcache');
    $this->assertClassHasAttribute('compress', 'DALMP\Cache\Memcache');
    $this->assertClassHasAttribute('cache', 'DALMP\Cache\Memcache');
  }

  public function testX() {
    $this->assertContainsOnlyInstancesOf('DALMP\Cache\Memcache', array($this->cache));
  }

}
