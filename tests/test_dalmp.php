<?php

/**
 * class to test the Database instance
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

  /**
   * expected data
   *
   * @var json_encode
   */
  protected  $expected = '[{"name":"South Hill","District":"\u2013","Capital":"62","Localname":"Anguilla","Region":"Caribbean","SurfaceArea":"96.00","Population":"8000"},{"name":"The Valley","District":"\u2013","Capital":"62","Localname":"Anguilla","Region":"Caribbean","SurfaceArea":"96.00","Population":"8000"},{"name":"Bantam","District":"Home Island","Capital":"2317","Localname":"Cocos (Keeling) Islands","Region":"Australia and New Zealand","SurfaceArea":"14.00","Population":"600"},{"name":"West Island","District":"West Island","Capital":"2317","Localname":"Cocos (Keeling) Islands","Region":"Australia and New Zealand","SurfaceArea":"14.00","Population":"600"},{"name":"Flying Fish Cove","District":"\u2013","Capital":"1791","Localname":"Christmas Island","Region":"Australia and New Zealand","SurfaceArea":"135.00","Population":"2500"},{"name":"Stanley","District":"East Falkland","Capital":"763","Localname":"Falkland Islands","Region":"South America","SurfaceArea":"12173.00","Population":"2000"},{"name":"Kingston","District":"\u2013","Capital":"2806","Localname":"Norfolk Island","Region":"Australia and New Zealand","SurfaceArea":"36.00","Population":"2000"},{"name":"Alofi","District":"\u2013","Capital":"2805","Localname":"Niue","Region":"Polynesia","SurfaceArea":"260.00","Population":"2000"},{"name":"Adamstown","District":"\u2013","Capital":"2912","Localname":"Pitcairn","Region":"Polynesia","SurfaceArea":"49.00","Population":"50"},{"name":"Jamestown","District":"Saint Helena","Capital":"3063","Localname":"Saint Helena","Region":"Western Africa","SurfaceArea":"314.00","Population":"6000"},{"name":"Longyearbyen","District":"L\u00e4nsimaa","Capital":"938","Localname":"Svalbard og Jan Mayen","Region":"Nordic Countries","SurfaceArea":"62422.00","Population":"3200"},{"name":"Saint-Pierre","District":"Saint-Pierre","Capital":"3067","Localname":"Saint-Pierre-et-Miquelon","Region":"North America","SurfaceArea":"242.00","Population":"7000"},{"name":"Fakaofo","District":"Fakaofo","Capital":"3333","Localname":"Tokelau","Region":"Polynesia","SurfaceArea":"12.00","Population":"2000"},{"name":"Citt\u00e0 del Vaticano","District":"\u2013","Capital":"3538","Localname":"Santa Sede\/Citt\u00e0 del Vaticano","Region":"Southern Europe","SurfaceArea":"0.40","Population":"1000"}]';

  public function setUp() {
    if (!extension_loaded('mysqli')) {
      $this->markTestSkipped('The mysqli extension is not available.');
    }

    /**
     * read DSN from phpunit.xml
     */
    $this->db = new DALMP\Database($GLOBALS['DSN']);
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
    $rs = $this->db->GetAll('SELECT * FROM Country WHERE Continent = "North America"');
    $this->assertEquals(37, count($rs));
    $this->assertEquals('Mexico', $rs['23'][1]);
    $this->assertEquals('414972.00', $rs['23']['GNP']);
  }

  public function testPGetall() {
    $rs = $this->db->PGetAll('SELECT * FROM Country WHERE Continent = ?', 'North America');
    $this->assertEquals(37, count($rs));
    $this->assertEquals('Mexico', $rs['23'][1]);
    $this->assertEquals('414972.00', $rs['23']['GNP']);
  }

  public function testGetRow() {
    $rs = $this->db->GetRow("SELECT * From Country WHERE Continent = 'Europe'");
    $this->assertEquals(30, count($rs));
    $this->assertEquals('Europe', $rs[2]);
    $this->assertEquals('Europe', $rs['Continent']);
  }

  public function testPGetRow() {
    $rs = $this->db->PGetRow('SELECT * From Country WHERE Continent = ?', 'Europe');
    $this->assertEquals(30, count($rs));
    $this->assertEquals('Europe', $rs[2]);
    $this->assertEquals('Europe', $rs['Continent']);
  }

  public function testGetCol() {
    $rs = $this->db->GetCol("SELECT * From Country WHERE Continent='Oceania' AND Population < 10000");
    $this->assertEquals(7, count($rs));
    $this->assertEquals(array('CCK', 'CXR', 'NFK', 'NIU', 'PCN', 'TKL', 'UMI'), $rs);
  }

  public function testPGetCol() {
    $rs = $this->db->PGetCol('SELECT * From Country WHERE Continent=? AND Population < ?', 'Oceania', 10000);
    $this->assertEquals(7, count($rs));
    $this->assertEquals(array('CCK', 'CXR', 'NFK', 'NIU', 'PCN', 'TKL', 'UMI'), $rs);
  }

  public function testGetOne() {
    $this->assertStringMatchesFormat('%i', $this->db->GetOne('SELECT UNIX_TIMESTAMP()'));
    $rs = $this->db->GetOne("SELECT * From Country WHERE Continent='Oceania' AND Population < 10000");
    $this->assertEquals(False, is_array($rs));
    $this->assertEquals('CCK', $rs);
  }

  public function testPGetOne() {
    $this->assertStringMatchesFormat('%i', $this->db->GetOne('SELECT UNIX_TIMESTAMP()'));
    $rs = $this->db->PGetOne('SELECT * From Country WHERE Continent=? AND Population < ?', 'Oceania', 10000);
    $this->assertEquals(False, is_array($rs));
    $this->assertEquals('CCK', $rs);
  }

  public function testGetAssoc() {
    $rs = $this->db->GetAssoc("SELECT Name, CountryCode From City WHERE District='Florida' AND population < 100000");
    $this->assertEquals(3, count($rs));
    $this->assertEquals(array('Clearwater' => 'USA', 'Miami Beach' => 'USA', 'Gainesville' => 'USA'), $rs);
  }

  public function testPGetAssoc() {
    $rs = $this->db->PGetAssoc('SELECT Name, CountryCode From City WHERE District=? AND Population < ?', 'Florida', 100000);
    $this->assertEquals(3, count($rs));
    $this->assertEquals(array('Clearwater' => 'USA', 'Miami Beach' => 'USA', 'Gainesville' => 'USA'), $rs);
  }

  public function testExpectedResultsGetAll() {
    $rs = $this->db->FetchMode('ASSOC')->GetALL('SELECT t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000');
    $this->assertEquals(json_decode($this->expected, True), $rs);
  }

  public function testExpectedResultsPGetAll() {
    $rs = $this->db->FetchMode('ASSOC')->PGetALL('SELECT t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000);
    $this->assertEquals(json_decode($this->expected, True), $rs);
  }

}
