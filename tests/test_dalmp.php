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
    $this->db->debug(True);
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
    $this->assertEquals('Mexico', $rs['863']['Name']);
  }

  public function testGetRow() {
    $rs = $this->db->GetRow('SELECT * From City');
    $this->assertEquals('10', count($rs));
    $this->assertEquals(1, $rs['ID']);
    $this->assertEquals('Kabul', $rs['Name']);
  }

  public function testGetCol() {
    $rs = $this->db->GetCol('SELECT Name From City');
    $this->assertEquals('4079', count($rs));
    $this->assertEquals('Toluca', $rs[2533]);
  }

  public function testGetOne() {
    $this->assertStringMatchesFormat('%i', $this->db->GetOne('SELECT UNIX_TIMESTAMP()'));
    $rs = $this->db->GetOne('SELECT Name From City');
    $this->assertEquals(False, is_array($rs));
  }

  public function testGetAssoc() {
    $rs = $this->db->GetAssoc('SELECT Name, CountryCode From City');
    $this->assertEquals(4001, count($rs));
    $this->assertEquals('USA', $rs['Gainesville']);
  }

  public function testExpectedResultsGetAll() {
    $rs = $this->db->FetchMode('ASSOC')->GetALL('SELECT t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000');
    $this->assertEquals(json_decode($this->expected, True), $rs);
  }

  public function testExpectedResultsPGetAll() {
    $rs = $this->db->FetchMode('ASSOC')->PGetALL('SELECT t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000);
    $this->assertEquals(json_decode($this->expected, True), $rs);
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
