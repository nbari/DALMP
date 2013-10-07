<?php

require_once 'test_dalmp_cache_base.php';

/**
 * test_dalmp_cache_memcache
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class test_dalmp_cache_memcache extends test_dalmp_cache_base
{
  public function setUp()
  {
    if (!extension_loaded('mysqli')) {
      $this->markTestSkipped('The mysqli extension is not available.');
    }

    /**
     * read DSN from phpunit.xml
     */
    $this->db = new DALMP\Database($GLOBALS['DSN']);
    $this->db->useCache(new DALMP\Cache(new DALMP\Cache\Memcache()));
  }

}
