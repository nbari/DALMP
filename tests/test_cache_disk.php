<?php

require_once 'test_cache_base.php';

/**
 * Test for Disk
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class test_cache_disk extends test_cache_base {

  /**
   * Cache instance
   *
   * @var Cache
   */
  public $cache;

  public function setUp() {
    $this->cache = new DALMP\Cache\Disk('/tmp/test_dalmp_disk');
  }

  public function testAttributes() {
    $this->assertClassHasAttribute('cache_dir', 'DALMP\Cache\Disk');
  }

  public function testX() {
    $this->assertContainsOnlyInstancesOf('DALMP\Cache\Disk', array($this->cache));
  }

}
