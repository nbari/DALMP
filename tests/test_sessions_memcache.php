<?php

require_once 'test_sessions_base.php';

/**
 * Test for Sessions\Memcache
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class test_sessions_memcache extends test_sessions_base {

  /**
   * SessionHandler instance
   *
   * @var sess
   */
  public $sess;

  public function setUp() {
    if (!extension_loaded('memcache')) {
      $this->markTestSkipped('The memcache extension is not available.');
    }
    $this->sess = new DALMP\Sessions\Memcache(new DALMP\Cache\Memcache);
  }

  public function testAttributes() {
    $this->assertClassHasAttribute('cache', 'DALMP\Sessions\Memcache');
    $this->assertClassHasAttribute('cache_ref_key', 'DALMP\Sessions\Memcache');
    $this->assertClassHasAttribute('dalmp_sessions_ref', 'DALMP\Sessions\Memcache');
    $this->assertClassHasAttribute('dalmp_sessions_key', 'DALMP\Sessions\Memcache');
  }

}
