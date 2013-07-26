<?php

require_once 'test_sessions_base.php';

/**
 * Test for Sessions\MySQL
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class test_sessions_mysql extends test_sessions_base {

  /**
   * SessionHandler instance
   *
   * @var sess
   */
  protected $sess;

  public function setUp() {
    if (!extension_loaded('mysqli')) {
      $this->markTestSkipped('The mysqli extension is not available.');
    }

    #$db = new DALMP\Database('utf8://user:password@host:3306/your_database');
    $db = new DALMP\Database('utf8://root@localhost:3306/dalmp');
    $this->sess = new DALMP\Sessions\MySQL($db);
  }

  public function testAttributes() {
    $this->assertClassHasAttribute('DB', 'DALMP\Sessions\MySQL');
    $this->assertClassHasAttribute('dalmp_sessions_ref', 'DALMP\Sessions\MySQL');
    $this->assertClassHasAttribute('dalmp_sessions_table', 'DALMP\Sessions\MySQL');
  }

}
