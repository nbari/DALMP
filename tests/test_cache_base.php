<?php

/**
 * abstract class to test Cache instances
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
abstract class test_cache_base extends PHPUnit_Framework_TestCase {

  abstract public function testAttributes();

  public function testSet() {
    $tmp_string = '';
    foreach (range(1,1000) as $id) {
      $this->assertEquals(True, $this->cache->set("test_dalmp_key_{$id}", "val_{$id}"));
      /**
       * test expiry TTL
       */
      $this->assertEquals(True, $this->cache->set("test_dalmp_key_ttl_{$id}", "val_{$id}", 2));
      $tmp_string .= sha1($id);
    }

    $this->assertEquals(True, $this->cache->set("test_dalmp_key_tmp_string", $tmp_string));
  }

  /**
   * @medium
   */
  public function testGet() {
    /**
     * wait 2 seconds to let keys expire
     */
    sleep(2);
    $tmp_string = '';
    foreach (range(1,1000) as $id) {
      $this->assertEquals("val_{$id}", $this->cache->get("test_dalmp_key_{$id}"));
      $this->assertEquals(False, $this->cache->get("test_dalmp_key_ttl_{$id}"));
      $tmp_string .= sha1($id);
    }

    $this->assertEquals($tmp_string, $this->cache->get("test_dalmp_key_tmp_string"));
  }

  public function testDelete() {
    foreach (range(1,1000) as $id) {
      $this->assertEquals(True, $this->cache->delete("test_dalmp_key_{$id}"));
    }

    $this->assertEquals(True, $this->cache->delete("test_dalmp_key_tmp_string"));
  }

  public function testFlush() {
    $this->cache->set('x', 'y');
    $this->assertTrue($this->cache->Flush());
    $this->assertEquals(False, $this->cache->get('x'));
  }

}
