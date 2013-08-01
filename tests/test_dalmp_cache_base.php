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
    $this->assertInstanceOf('Dalmp\Cache', $this->db->cache);
  }

  public function testCacheGetAll_0() {
    $rs = $this->db->CacheGetAll(2, "SELECT *, UNIX_TIMESTAMP() AS timestamp,  FLOOR(0 + (RAND() * 1000)) AS rand FROM Country WHERE Continent = 'North America'");
    $this->assertEquals(37, count($rs));
    $this->assertEquals('Mexico', $rs['23'][1]);
    $this->assertEquals('414972.00', $rs['23']['GNP']);
    return $rs;
  }

  /**
   * @depends testCacheGetAll_0
   */
  public function testCacheGetAll_1($data) {
    $rs = $this->db->CacheGetAll(1, "SELECT *, UNIX_TIMESTAMP() AS timestamp,  FLOOR(0 + (RAND() * 1000)) AS rand FROM Country WHERE Continent = 'North America'");
    $this->assertEquals($data, $rs);
    return $data;
  }

  /**
   * @depends testCacheGetAll_1
   */
  public function testCacheGetAll_2($data) {
    sleep(2);
    $rs = $this->db->CacheGetAll(1, "SELECT *, UNIX_TIMESTAMP() AS timestamp,  FLOOR(0 + (RAND() * 1000)) AS rand FROM Country WHERE Continent = 'North America'");
    $this->assertNotEquals($data, $rs);
  }

  public function testExpectedResultsGetAll_0() {
    $rs = $this->db->FetchMode('ASSOC')->CacheGetALL(2, 'SELECT UNIX_TIMESTAMP() AS timestamp,  FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000');
    return $rs;
  }

  /**
   * @depends testExpectedResultsGetAll_0
   */
  public function testExpectedResultsGetAll_1($data) {
    $rs = $this->db->FetchMode('ASSOC')->CacheGetALL(1, 'SELECT UNIX_TIMESTAMP() AS timestamp,  FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000');
    $this->assertEquals($data, $rs);
    return $rs;
  }

  /**
   * @depends testExpectedResultsGetAll_1
   */
  public function testExpectedResultsGetAll_2($data) {
    sleep(2);
    $rs = $this->db->FetchMode('ASSOC')->CacheGetALL(1, 'SELECT UNIX_TIMESTAMP() AS timestamp,  FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000');
    $this->assertNotEquals($data, $rs);
  }

  public function testExpectedResultsPGetAll_0() {
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(2, 'SELECT t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000);
    return $rs;
  }

  /**
   * @depends testExpectedResultsPGetAll_0
   */
  public function testExpectedResultsPGetAll_1($data) {
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(1, 'SELECT t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000);
    $this->assertEquals($data, $rs);
    return $rs;
  }

  /**
   * @depends testExpectedResultsPGetAll_1
   */
  public function testExpectedResultsPGetAll_2($data) {
    sleep(2);
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(1, 'SELECT t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000);
    $this->assertEquals($data, $rs);
    return $rs;
  }

}
