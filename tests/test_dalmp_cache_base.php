<?php

/**
 * abstract class test_dalmp_cache_base
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
abstract class test_dalmp_cache_base extends PHPUnit_Framework_TestCase {

  /**
   * DB instance
   *
   * @var DALMP\Database
   */
  protected $db;

  public function testCacheInstance() {
    $this->assertInstanceOf('Dalmp\Cache', $this->db->Cache());
  }

  public function testCacheGetAll_0() {
    $rs = $this->db->CacheGetAll(2, "SELECT *, UNIX_TIMESTAMP() AS timestamp,  FLOOR(0 + (RAND() * 1000)) AS rand FROM Country WHERE Continent = 'North America'");
    $this->assertEquals(37, count($rs));
    $this->assertEquals('Mexico', $rs['23'][1]);
    $this->assertEquals('414972.00', $rs['23']['GNP']);
    return array($rs[0]['timestamp'], $rs[35]['rand']);
  }

  /**
   * @depends testCacheGetAll_0
   */
  public function testCacheGetAll_1($data) {
    $rs = $this->db->CacheGetAll(1, "SELECT *, UNIX_TIMESTAMP() AS timestamp,  FLOOR(0 + (RAND() * 1000)) AS rand FROM Country WHERE Continent = 'North America'");
    $this->assertEquals($data, array($rs[0]['timestamp'], $rs[35]['rand']));
    return $data;
  }

  /**
   * @depends testCacheGetAll_1
   */
  public function testCacheGetAll_2($data) {
    sleep(2);
    $rs = $this->db->CacheGetAll(2, "SELECT *, UNIX_TIMESTAMP() AS timestamp,  FLOOR(0 + (RAND() * 1000)) AS rand FROM Country WHERE Continent = 'North America'");
    $this->assertNotEquals($data, array($rs[0]['timestamp'], $rs[35]['rand']));
  }

}
