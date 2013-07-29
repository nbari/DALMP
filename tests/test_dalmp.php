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
class test_dalmp extends test_dalmp_base {

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
    $this->db = new DALMP\Database($GLOBALS['DSN']);
  }

}
