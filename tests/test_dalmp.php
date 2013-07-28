<?php

/**
 * test_dalmp
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class test_dalmp extends PHPUnit_Framework_TestCase {

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

    #$db = new DALMP\Database('utf8://user:password@host:3306/your_database');
    $this->db = new DALMP\Database('utf8://root@localhost:3306/dalmp');
  }

  public function testAttributes() {
    $this->assertClassHasAttribute('DB', 'DALMP\Database');
    $this->assertClassHasAttribute('dsn', 'DALMP\Database');
    $this->assertClassHasAttribute('_rs', 'DALMP\Database');
    $this->assertClassHasAttribute('_stmt', 'DALMP\Database');
    $this->assertClassHasAttribute('cache', 'DALMP\Database');
    $this->assertClassHasAttribute('debug', 'DALMP\Database');
    $this->assertClassHasAttribute('fetchMode', 'DALMP\Database');
    $this->assertClassHasAttribute('numOfRows', 'DALMP\Database');
    $this->assertClassHasAttribute('numOfRowsAffected', 'DALMP\Database');
    $this->assertClassHasAttribute('numOfFields', 'DALMP\Database');
    $this->assertClassHasAttribute('stmtParams', 'DALMP\Database');
    $this->assertClassHasAttribute('trans', 'DALMP\Database');
  }

  public function testGetall() {
    $rs = $this->db->GetAll('SELECT * From City');
    $this->assertEquals('4079', count($rs));
    $this->assertEquals('Mexico', $rs['863'][1]);
  }

  public function testGetRow() {
    $this->assertStringMatchesFormat('%i', $this->db->GetOne('SELECT UNIX_TIMESTAMP()'));
  }

  public function testGetCol() {
    $this->assertStringMatchesFormat('%i', $this->db->GetOne('SELECT UNIX_TIMESTAMP()'));
  }

  public function testGetOne() {
    $this->assertStringMatchesFormat('%i', $this->db->GetOne('SELECT UNIX_TIMESTAMP()'));
  }

  public function testGetAssoc() {
    $this->assertStringMatchesFormat('%i', $this->db->GetOne('SELECT UNIX_TIMESTAMP()'));
  }




  /**
   * @depends testExecute
   *
  public function testConnected() {
    #print_r($this->db->isConnected());
    #exit;
  }
   */


}
