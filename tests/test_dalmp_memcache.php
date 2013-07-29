<?php

require_once 'test_dalmp_base.php';

/**
 * test_dalmp
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class test_dalmp_memcache extends test_dalmp_base {

  /**
   * DB instance
   *
   * @var DALMP\Database
   */
  protected $db;

  public function setUp() {
    if (!extension_loaded('mysqli')) {
      $this->markTestSkipped('The mysqli extension is not available.');
    }

    /**
     * read DSN from phpunit.xml
     */
    $this->db = new DALMP\Database($GLOBALS['DSN_memcache']);
  }

  public function testCacheInstance() {
    $this->assertInstanceOf('Dalmp\Cache', $this->db->Cache());
  }

  public function testCacheGetall() {
    $rs = $this->db->CacheGetAll(300, 'SELECT * FROM Country WHERE Continent = "North America"');
    $this->assertEquals(37, count($rs));
    $this->assertEquals('Mexico', $rs['23'][1]);
    $this->assertEquals('414972.00', $rs['23']['GNP']);
  }



}
