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

  public function testCacheFlush() {
    $rs = $this->db->CacheFlush();
    $this->assertTrue($rs);
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
    $rs2 = $this->db->GetAll("SELECT *, UNIX_TIMESTAMP() AS timestamp,  FLOOR(0 + (RAND() * 1000)) AS rand FROM Country WHERE Continent = 'North America'");
    $this->assertEquals($data, $rs);
    $this->assertNotEquals($rs2, $rs);
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
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(2, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 1000);
    $this->assertTrue(is_array($rs));
    return $rs;
  }

  /**
   * @depends testExpectedResultsPGetAll_0
   */
  public function testExpectedResultsPGetAll_1($data) {
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(1, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 1000);
    $this->assertEquals($data, $rs);
    return $rs;
  }

  /**
   * @depends testExpectedResultsPGetAll_1
   */
  public function testExpectedResultsPGetAll_2($data) {
    sleep(2);
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(1, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 1000);
    $this->assertNotEquals($data, $rs);
  }

  public function testExpectedResultsPGroupCache_0() {
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(3, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000, 'group:A');
    $this->assertTrue(is_array($rs));
    return $rs;
  }

  /**
   * @depends testExpectedResultsPGroupCache_0
   */
  public function testExpectedResultsPGroupCache_1($data) {
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000);
    $rs2 = $this->db->FetchMode('ASSOC')->PGetALL('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000);
    $this->assertEquals($data, $rs);
    $this->assertNotEquals($data, $rs2);
    return $rs;
  }

  /**
   * @depends testExpectedResultsPGroupCache_1
   */
  public function testExpectedResultsPGroupCache_2($data) {
    $this->assertTrue($this->db->CacheFlush('group:A'));
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(2, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000);
    $this->assertNotEquals($data, $rs);
  }

  public function testExpectedResultsGroupCache_0() {
    $rs = $this->db->FetchMode('ASSOC')->CacheGetALL(3, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000', 'group:A');
    $this->assertTrue(is_array($rs));
    return $rs;
  }

  /**
   * @depends testExpectedResultsGroupCache_0
   */
  public function testExpectedResultsGroupCache_1($data) {
    $rs = $this->db->FetchMode('ASSOC')->CacheGetALL(2, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000');
    $rs2 = $this->db->FetchMode('ASSOC')->GetALL('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000');
    $this->assertEquals($data, $rs);
    $this->assertNotEquals($data, $rs2);
    return $data;
  }

  /**
   * @depends testExpectedResultsGroupCache_1
   */
  public function testExpectedResultsGroupCache_2($data) {
    $this->assertTrue($this->db->CacheFlush('group:A'));
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(2, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000');
    $this->assertNotEquals($data, $rs);
  }

  public function testCacheKey_0() {
    $rs = $this->db->FetchMode('ASSOC')->CacheGetALL(9, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000', 'mykey');
    $this->assertTrue(is_array($rs));
    return $rs;
  }

  /**
   * @depends testCacheKey_0
   */
  public function testCacheKey_1($data) {
    $rs = $this->db->FetchMode('ASSOC')->CacheGetALL('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000', 'mykey');
    $this->assertEquals($data, $rs);
    $rs = $this->db->FetchMode('ASSOC')->CacheGetALL(2, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000');
    $this->assertNotEquals($data, $rs);
    $rs2 = $this->db->FetchMode('ASSOC')->CacheGetALL('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000');
    $this->assertEquals($rs, $rs2);
    return $data;
  }

  /**
   * @depends testCacheKey_1
   */
  public function testCacheKey_2($data) {
    $rs = $this->db->FetchMode('ASSOC')->CacheGetALL('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000', 'mykey');
    $this->assertEquals($data, $rs);
    $rs2 = $this->db->CacheFlush('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000', 'mykey');
    $this->assertTrue((boolean) $rs2);
    $rs = $this->db->FetchMode('ASSOC')->CacheGetALL(2, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000', 'mykey');
    $this->assertNotEquals($data, $rs);
    sleep(1);
    $rs2 = $this->db->FetchMode('ASSOC')->CacheGetALL('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000', 'mykey');
    $this->assertEquals($rs2, $rs);
    sleep(1);
    $rs = $this->db->FetchMode('ASSOC')->CacheGetALL(1, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000', 'mykey');
    $this->assertNotEquals($rs2, $rs);
  }

  public function testPCacheKey_0() {
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(3, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000, 'mykey');
    $this->assertTrue(is_array($rs));
    return $rs;
  }

  /**
   * @depends testPCacheKey_0
   */
  public function testPCacheKey_1($data) {
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000, 'mykey');
    $this->assertEquals($data, $rs);
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(2, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000);
    $this->assertNotEquals($data, $rs);
    $rs2 = $this->db->FetchMode('ASSOC')->CachePGetALL('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000);
    $this->assertEquals($rs, $rs2);
    return $data;
  }

  /**
   * @depends testPCacheKey_1
   */
  public function testPCacheKey_2($data) {
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000, 'mykey');
    $this->assertEquals($data, $rs);
    $rs2 = $this->db->CacheFlush('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 'mykey');
    $this->assertTrue((boolean) $rs2);
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(2, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000, 'mykey');
    $this->assertNotEquals($data, $rs);
    sleep(1);
    $rs2 = $this->db->FetchMode('ASSOC')->CachePGetALL('SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000, 'mykey');
    $this->assertEquals($rs2, $rs);
    sleep(1);
    $rs = $this->db->FetchMode('ASSOC')->CachePGetALL(1, 'SELECT UNIX_TIMESTAMP() AS timestamp, FLOOR(0 + (RAND() * 1000)) AS rand, t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000, 'mykey');
    $this->assertNotEquals($rs2, $rs);
  }

  public function testCacheFlushEnd() {
    $rs = $this->db->CacheFlush();
    $this->assertTrue($rs);
  }

}
