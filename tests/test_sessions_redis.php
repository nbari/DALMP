<?php

require_once 'test_sessions_base.php';

/**
 * Test for Sessions\Redis
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class test_sessions_redis extends test_sessions_base {

  /**
   * SessionHandler instance
   *
   * @var sess
   */
  public $sess;

  public function setUp() {
    if (!extension_loaded('redis')) {
      $this->markTestSkipped('The redis extension is not available.');
    }
    $this->sess = new DALMP\Sessions\Redis(new DALMP\Cache\Redis);
  }

  public function testAttributes() {
    $this->assertClassHasAttribute('cache', 'DALMP\Sessions\Redis');
    $this->assertClassHasAttribute('cache_ref_key', 'DALMP\Sessions\Redis');
    $this->assertClassHasAttribute('dalmp_sessions_ref', 'DALMP\Sessions\Redis');
    $this->assertClassHasAttribute('dalmp_sessions_key', 'DALMP\Sessions\Redis');
  }

}
