<?php
namespace DALMP\Cache;

/**
 * APC
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class APC implements CacheInterface {

  protected $cache;

  /**
   * Constructor
   *
   * @param string $host
   * @param int $port
   * @param int $timeout
   * @param int $compress
   */
  public function __construct() {
    if (!extension_loaded('apc') && !ini_get('apc.enabled')) {
      throw new \Exception(__CLASS__ . ': APC PECL extension not loaded or enabled!');
    }
  }

  /**
   * Store data at the server
   *
   * @param string $key
   * @param string $value
   * @param int $expire time in seconds(default is 0 meaning unlimited)
   */
  public function set($key, $value, $expire = 0) {
    return apc_store($key, $value, $expire) ? $this : false;
  }

  /**
   * Retrieve item from the server
   *
   * @param string $key
   */
  public function Get($key) {
    return apc_fetch($key);
  }

  /**
   * Delete item from the server
   *
   * @param string $key
   */
  public function Delete($key){
    return apc_delete($key);
  }

  /**
   * Flush cache
   */
  public function Flush(){
    return apc_clear_cache('user');
  }

  /**
   * Get cache stats
   */
  public function Stats(){
    return apc_cache_info();
  }

  /**
   * X execute/call custom methods
   *
   * @return cache object
   */
  public function X(){
    return $this;
  }

}
