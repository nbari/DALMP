<?php

/**
 * class to test the Database instance
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class test_dalmp extends PHPUnit_Framework_TestCase
{
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
  protected $expected = '[{"name":"South Hill","District":"\u2013","Capital":"62","Localname":"Anguilla","Region":"Caribbean","SurfaceArea":"96.00","Population":"8000"},{"name":"The Valley","District":"\u2013","Capital":"62","Localname":"Anguilla","Region":"Caribbean","SurfaceArea":"96.00","Population":"8000"},{"name":"Bantam","District":"Home Island","Capital":"2317","Localname":"Cocos (Keeling) Islands","Region":"Australia and New Zealand","SurfaceArea":"14.00","Population":"600"},{"name":"West Island","District":"West Island","Capital":"2317","Localname":"Cocos (Keeling) Islands","Region":"Australia and New Zealand","SurfaceArea":"14.00","Population":"600"},{"name":"Flying Fish Cove","District":"\u2013","Capital":"1791","Localname":"Christmas Island","Region":"Australia and New Zealand","SurfaceArea":"135.00","Population":"2500"},{"name":"Stanley","District":"East Falkland","Capital":"763","Localname":"Falkland Islands","Region":"South America","SurfaceArea":"12173.00","Population":"2000"},{"name":"Kingston","District":"\u2013","Capital":"2806","Localname":"Norfolk Island","Region":"Australia and New Zealand","SurfaceArea":"36.00","Population":"2000"},{"name":"Alofi","District":"\u2013","Capital":"2805","Localname":"Niue","Region":"Polynesia","SurfaceArea":"260.00","Population":"2000"},{"name":"Adamstown","District":"\u2013","Capital":"2912","Localname":"Pitcairn","Region":"Polynesia","SurfaceArea":"49.00","Population":"50"},{"name":"Jamestown","District":"Saint Helena","Capital":"3063","Localname":"Saint Helena","Region":"Western Africa","SurfaceArea":"314.00","Population":"6000"},{"name":"Longyearbyen","District":"L\u00e4nsimaa","Capital":"938","Localname":"Svalbard og Jan Mayen","Region":"Nordic Countries","SurfaceArea":"62422.00","Population":"3200"},{"name":"Saint-Pierre","District":"Saint-Pierre","Capital":"3067","Localname":"Saint-Pierre-et-Miquelon","Region":"North America","SurfaceArea":"242.00","Population":"7000"},{"name":"Fakaofo","District":"Fakaofo","Capital":"3333","Localname":"Tokelau","Region":"Polynesia","SurfaceArea":"12.00","Population":"2000"},{"name":"Citt\u00e0 del Vaticano","District":"\u2013","Capital":"3538","Localname":"Santa Sede\/Citt\u00e0 del Vaticano","Region":"Southern Europe","SurfaceArea":"0.40","Population":"1000"}]';

  public function setUp()
  {
    if (!extension_loaded('mysqli')) {
      $this->markTestSkipped('The mysqli extension is not available.');
    }

    /**
     * read DSN from phpunit.xml
     */
    $this->db = new DALMP\Database($GLOBALS['DSN']);
  }

  public function testAttributes()
  {
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

  public function testGetall()
  {
    $rs = $this->db->GetAll('SELECT * FROM Country WHERE Continent = "North America"');
    $this->assertEquals(37, count($rs));
    $this->assertEquals('Mexico', $rs['23'][1]);
    $this->assertEquals('414972.00', $rs['23']['GNP']);
  }

  public function testPGetall()
  {
    $rs = $this->db->PGetAll('SELECT * FROM Country WHERE Continent = ?', 'North America');
    $this->assertEquals(37, count($rs));
    $this->assertEquals('Mexico', $rs['23'][1]);
    $this->assertEquals('414972.00', $rs['23']['GNP']);
  }

  public function testGetRow()
  {
    $rs = $this->db->GetRow("SELECT * From Country WHERE Continent = 'Europe'");
    $this->assertEquals(30, count($rs));
    $this->assertEquals('Europe', $rs[2]);
    $this->assertEquals('Europe', $rs['Continent']);
  }

  public function testPGetRow()
  {
    $rs = $this->db->PGetRow('SELECT * From Country WHERE Continent = ?', 'Europe');
    $this->assertEquals(30, count($rs));
    $this->assertEquals('Europe', $rs[2]);
    $this->assertEquals('Europe', $rs['Continent']);
  }

  public function testGetCol()
  {
    $rs = $this->db->GetCol("SELECT * From Country WHERE Continent='Oceania' AND Population < 10000");
    $this->assertEquals(7, count($rs));
    $this->assertEquals(array('CCK', 'CXR', 'NFK', 'NIU', 'PCN', 'TKL', 'UMI'), $rs);
  }

  public function testPGetCol()
  {
    $rs = $this->db->PGetCol('SELECT * From Country WHERE Continent=? AND Population < ?', 'Oceania', 10000);
    $this->assertEquals(7, count($rs));
    $this->assertEquals(array('CCK', 'CXR', 'NFK', 'NIU', 'PCN', 'TKL', 'UMI'), $rs);
  }

  public function testGetOne()
  {
    $this->assertStringMatchesFormat('%i', $this->db->GetOne('SELECT UNIX_TIMESTAMP()'));
    $rs = $this->db->GetOne("SELECT * From Country WHERE Continent='Oceania' AND Population < 10000");
    $this->assertEquals(false, is_array($rs));
    $this->assertEquals('CCK', $rs);
  }

  public function testPGetOne()
  {
    $this->assertStringMatchesFormat('%i', $this->db->GetOne('SELECT UNIX_TIMESTAMP()'));
    $rs = $this->db->PGetOne('SELECT * From Country WHERE Continent=? AND Population < ?', 'Oceania', 10000);
    $this->assertEquals(false, is_array($rs));
    $this->assertEquals('CCK', $rs);
  }

  public function testGetAssoc()
  {
    $rs = $this->db->GetAssoc("SELECT Name, CountryCode From City WHERE District='Florida' AND population < 100000");
    $this->assertEquals(3, count($rs));
    $this->assertEquals(array('Clearwater' => 'USA', 'Miami Beach' => 'USA', 'Gainesville' => 'USA'), $rs);
  }

  public function testPGetAssoc()
  {
    $rs = $this->db->PGetAssoc('SELECT Name, CountryCode From City WHERE District=? AND Population < ?', 'Florida', 100000);
    $this->assertEquals(3, count($rs));
    $this->assertEquals(array('Clearwater' => 'USA', 'Miami Beach' => 'USA', 'Gainesville' => 'USA'), $rs);
  }

  public function testExpectedResultsGetAll()
  {
    $rs = $this->db->FetchMode('ASSOC')->GetALL('SELECT t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < 10000');
    $this->assertEquals(json_decode($this->expected, true), $rs);
  }

  public function testExpectedResultsPGetAll()
  {
    $rs = $this->db->FetchMode('ASSOC')->PGetALL('SELECT t1.name, t1.District, t2.Capital, t2.Localname, t2.Region, t2.SurfaceArea, t2.Population FROM City t1 LEFT JOIN Country t2 ON t1.countrycode=t2.code WHERE t2.population < ?', 10000);
    $this->assertEquals(json_decode($this->expected, true), $rs);
  }

  public function testExecuteDropCreate()
  {
    $rs = $this->db->Execute('DROP TABLE IF EXISTS `tests`');
    $this->assertTrue(($rs !== false) ? true : false);
    $this->assertEquals($rs, 0);
    $rs = $this->db->Execute('CREATE TABLE `tests` (id INT(11) unsigned NOT NULL AUTO_INCREMENT, col1 varchar(255), col2 varchar(255), col3 varchar(255), PRIMARY KEY (id))');
    $this->assertTrue(($rs !== false) ? true : false);
    $this->assertEquals($rs, 0);
    $this->assertEquals($this->db->getnumOfRows(), $this->db->getnumOfRowsAffected());
  }

  public function testPExecuteDropCreate()
  {
    $rs = $this->db->PExecute('DROP TABLE IF EXISTS `tests`');
    $this->assertTrue(($rs !== false) ? true : false);
    $this->assertEquals($rs, 0);
    $rs = $this->db->PExecute('CREATE TABLE `tests` (id INT(11) unsigned NOT NULL AUTO_INCREMENT, col1 varchar(255), col2 varchar(255), col3 varchar(255), PRIMARY KEY (id))');
    $this->assertTrue(($rs !== false) ? true : false);
    $this->assertEquals($rs, 0);
    $this->assertEquals($this->db->getnumOfRows(), $this->db->getnumOfRowsAffected());
  }

  public function testAutoExecute()
  {
    $rs = $this->db->AutoExecute('tests', array('col1' => 1, 'col2' => 2, 'col3' => 3));
    $this->assertTrue($rs);
    $rs = $this->db->FetchMode('ASSOC')->GetAll('SELECT * FROM tests');
    $this->assertEquals($rs, array(array('id' => 1, 'col1' => 1, 'col2' => 2, 'col3' => 3)));
    $rs = $this->db->AutoExecute('tests', array('col1' => 7), 'UPDATE', 'id=1');
    $this->assertTrue($rs);
    $rs = $this->db->FetchMode('ASSOC')->GetAll('SELECT * FROM tests');
    $this->assertEquals($rs, array(array('id' => 1, 'col1' => 7, 'col2' => 2, 'col3' => 3)));
    $this->assertEquals($this->db->getnumOfRows(), $this->db->getnumOfRowsAffected());
  }

  public function testMultipleInsert()
  {
    $rs = $this->db->multipleInsert('tests', array('col1', 'col2'), array(array(1,2), array(1,2), array(1), array('date', 'table'), array('niño','coração', 'tres'), array('date', 'table')));
    $this->assertTrue($rs);
    $rs = $this->db->FetchMode('ASSOC')->GetAll('SELECT * FROM tests where id > 1');
    $this->assertEquals(json_encode($rs), '[{"id":"2","col1":"1","col2":"2","col3":null},{"id":"3","col1":"1","col2":"2","col3":null},{"id":"4","col1":"1","col2":null,"col3":null},{"id":"5","col1":"date","col2":"table","col3":null},{"id":"6","col1":"ni\u00f1o","col2":"cora\u00e7\u00e3o","col3":null},{"id":"7","col1":"date","col2":"table","col3":null}]');
  }

  public function testUUID()
  {
    $uuids = array();
    for ($i = 0; $i < 10000; $i++) {
      $uuid = $this->db->uuid();
      $this->assertTrue((boolean) preg_match('#[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[8,9,a,b][0-9a-f]{3}-[0-9a-f]{12}#', $uuid));
      $uuids[] = $uuid;
    }
    $uuids = array_unique($uuids);
    $this->assertEquals(count($uuids), 10000);
  }

  public function testUpdate()
  {
    $this->assertEquals(0, $this->db->PExecute('UPDATE tests SET col1=? WHERE id=1', 7));
    $this->assertEquals($this->db->getnumOfRows(), $this->db->getnumOfRowsAffected());
    $this->assertEquals(0, $this->db->Execute('UPDATE tests SET col1=7 WHERE id=1'));
    $this->assertEquals($this->db->getnumOfRows(), $this->db->getnumOfRowsAffected());
    $this->assertTrue($this->db->Execute('UPDATE tests SET col1=8 WHERE id=1'));
    $this->assertNotEquals($this->db->getnumOfRows(), $this->db->getnumOfRowsAffected());
    $this->assertTrue($this->db->PExecute('UPDATE tests SET col1=? WHERE id=1', 7));
    $this->assertNotEquals($this->db->getnumOfRows(), $this->db->getnumOfRowsAffected());
  }

  public function testExecuteQuery()
  {
    $continent = $this->db->qstr('Africa');
    $this->assertTrue($this->db->Execute("SELECT * From Country WHERE Continent = '$continent'"));
    $this->assertEquals($this->db->getnumOfRows(), 58);
    $this->assertEquals($this->db->getnumOfRowsAffected(), 58);
    $this->assertFalse($this->db->Execute("SELECT * From Country WHERE Continent = 'naranjas'"));
  }

  public function testPExecuteQuery()
  {
    $rs = $this->db->PExecute('SELECT * From Country WHERE Continent = ?', 'Africa');
    $this->assertTrue($rs);
    $this->assertEquals($this->db->getnumOfRows(), 58);
    $this->assertEquals($this->db->getnumOfRowsAffected(), 58);
    $this->assertFalse($this->db->PExecute('SELECT * From Country WHERE Continent = ?', 'naranjas'));
  }

  public function testTransactions()
  {
    $this->assertEquals(0, $this->db->Execute('CREATE TABLE IF NOT EXISTS t_test (id INT NOT NULL PRIMARY KEY) ENGINE=InnoDB'));
    $this->assertEquals(0, $this->db->Execute('TRUNCATE TABLE t_test'));
    $this->assertEquals(0, $this->db->StartTrans());
    $this->assertTrue($this->db->Execute('INSERT INTO t_test VALUES(1)'));
    $this->assertEquals(0, $this->db->StartTrans());
    $this->assertTrue($this->db->Execute('INSERT INTO t_test VALUES(2)'));
    $this->assertEquals(array(array('id' => 1), array('id' => 2)), $this->db->FetchMode('ASSOC')->GetAll('SELECT * FROM t_test'));
    $this->assertEquals(0, $this->db->StartTrans());
    $this->assertTrue($this->db->Execute('INSERT INTO t_test VALUES(3)'));
    $this->assertEquals(array(array('id' => 1), array('id' => 2), array('id' => 3)), $this->db->FetchMode('ASSOC')->GetAll('SELECT * FROM t_test'));
    $this->assertEquals(0, $this->db->StartTrans());
    $this->assertTrue($this->db->Execute('INSERT INTO t_test VALUES(7)'));
    $this->assertEquals(array(array('id' => 1), array('id' => 2), array('id' => 3), array('id' => 7)), $this->db->FetchMode('ASSOC')->GetAll('SELECT * FROM t_test'));
    $this->assertEquals(0, $this->db->RollBackTrans());
    $this->assertTrue($this->db->CompleteTrans());
    $this->assertTrue($this->db->CompleteTrans());
    $this->assertTrue($this->db->CompleteTrans());
    $this->assertEquals(array(array('id' => 1), array('id' => 2), array('id' => 3)), $this->db->FetchMode('ASSOC')->GetAll('SELECT * FROM t_test'));
  }

}
