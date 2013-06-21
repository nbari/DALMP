<?php
namespace DALMP\Cache;

/**
 * Redis - Cache cache class
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 2.1
 */
class Memcache implements ICache {
  private $host;
  private $port;
  private $timeout;
  private $compress = False;
  protected $cache;

  /**
   * Constructor
   *
   * @param string $host
   * @param int $port
   * @param int $timeout
   * @param int $compress
   */
  public function __construct($host='127.0.0.1', $port=11211, $timeout=1, $compress=False) {
    $this->host = $host;
    $this->port = $port;
    $this->timeout = (int) $timeout;
    if ($compress) {
      $this->compress = MEMCACHE_COMPRESSED;
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
    if ($this->connect()) {
      ( $this->compress === 0) && $this->cache->setCompressThreshold(0);
      return (bool) $this->cache->set($key, $value, $this->compress, $expire);
    } else {
      return False;
    }
  }

  /**
   * Retrieve item from the server
   *
   * @param string $key
   */
  public function Get($key) {
    return $this->connect() ? $this->cache->get($key) : False;
  }

  /**
   * Delete item from the server
   *
   * @param string $key
   */
  public function Delete($key){
    return $this->connect() ? $this->cache->delete($key) : False;
  }

  /**
   * Flush cache
   */
  public function Flush(){
    return $this->connect() ? $this->cache->flush() : False;
  }

  /**
   * Get cache stats
   */
  public function Stats(){
    return $this->connect() ? $this->cache->getStats() : False;
  }

  /**
   * X execute/call custom methods
   *
   * @return cache object
   */
  public function X(){
    return $this->connect() ? $this->cache : False;
  }

  /**
   * try to establish a connection
   */
  private function connect() {
    if ($this->cache instanceof MemCache) {
      return True;
    } else {
      if (!extension_loaded('memcache')) {
        throw new Exception(__CLASS__ . 'Memcache PECL extension not loaded! - http://pecl.php.net/package/memcache');
      }

      $memcache = new \Memcache();

      /**
       * if a / found try to connect via socket
       */
      if (strpos($this->host, '/') !== false) {
        return $this->cache = $memcache->connect($this->host) ? $memcache : False;
      } else {
        return $this->cache = $memcache->connect($this->host, $this->port, $this->timeout) ? $memcache : False;
      }
    }
  }

}
