<?php
namespace DALMP\Cache;

/**
 * Redis
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class Redis implements CacheInterface {
  private $host = '127.0.0.1';
  private $port = 6379;
  private $timeout = 1;
  protected $cache;

  /**
   * Constructor
   *
   * @param string $host
   * @param int $port
   * @param int $timeout
   */
  public function __construct() {
    $args = func_get_args();

    if ($args) {
      $this->host = $args[0];
      $this->port = isset($args[1]) ? (int) $args[1] : 6379;
      $this->timeout = isset($args[2]) ? (int) $args[2] : 1;
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
      if ($expire == 0 || $expire == -1) {
        return $this->cache->set($key, serialize($value)) ? $this : False;
      } else {
        return $this->cache->setex($key, $expire, serialize($value)) ? $this : False;
      }
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
    return $this->connect() ? unserialize($this->cache->get($key)) : False;
  }

  /**
   * Delete item from the server
   *
   * @param string $key
   */
  public function Delete($key) {
    return $this->connect() ? $this->cache->delete($key) : False;
  }

  /**
   * Flush cache
   */
  public function Flush() {
    return $this->connect() ? $this->cache->flushDB() : False;
  }

  /**
   * Get cache stats
   */
  public function Stats() {
    return $this->connect() ? $this->cache->info() : False;
  }

  /**
   * X execute/call custom methods
   *
   * @return cache object
   */
  public function X() {
    return $this->connect() ? $this->cache : False;
  }

  /**
   * try to establish a connection
   */
  private function connect() {
    if ($this->cache instanceof Redis) {
      return True;
    } else {
      if (!extension_loaded('redis')) {
        throw new \Exception(__CLASS__ . 'redis extension not loaded! - http://github.com/nicolasff/phpredis');
      }

      $redis = new \Redis();
      try {
        /**
         * if a / found try to connect via socket
         */
        if (strpos($this->host, '/') !== false) {
          return $this->cache = $redis->connect($this->host) ? $redis : False;
        } else {
          return $this->cache = $redis->connect($this->host, $this->port, $this->timeout) ? $redis : False;
        }
      } catch (\RedisException $e) {
        trigger_error('ERROR ->' . __METHOD__ . $e->getMessage(), E_USER_NOTICE);
        return False;
      }
    }
  }

}
