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
class Disk implements ICache {

  public $cache_dir;
  /**
   * Constructor
   *
   * @param string $host
   * @param int $port
   * @param int $timeout
   * @param bool $compress
   */
  public function __construct($dir) {
    $cache_dir = defined('DALMP_CACHE_DIR') ? DALMP_CACHE_DIR : '/tmp/dalmp';
    $dalmp_cache_dir = $dalmp_cache_dir . '/' . substr($key, 0, 2);
    $this->cache_dir = $dir;
  }

  /**
   * Store data at the server
   *
   * @param string $key
   * @param string $value
   * @param int $expire time in seconds(default is 0 meaning unlimited)
   */
  public function set($key, $value, $expire = 0) {
    $dalmp_cache_dir = defined('DALMP_CACHE_DIR') ? DALMP_CACHE_DIR : '/tmp/dalmp';
    $dalmp_cache_dir = $dalmp_cache_dir . '/' . substr($key, 0, 2);
    if (!file_exists($dalmp_cache_dir)) {
      if (!mkdir($dalmp_cache_dir, 0750, true)) {
        trigger_error('ERROR -> ' . __METHOD__ . ": dirCache - Cannot create: $dalmp_cache_dir", E_USER_NOTICE);
        return false;
      }
    }
    $cache_file = "$dalmp_cache_dir/dalmp_$key.cache";
    if (!($fp = fopen($cache_file, 'w'))) {
      trigger_error('ERROR -> ' . __METHOD__ . ": dirCache - Cannot create cache file $cache_file", E_USER_NOTICE);
      return false;
    }
    if (flock($fp, LOCK_EX) && ftruncate($fp, 0)) {
      if (fwrite($fp, serialize($value))) {
        flock($fp, LOCK_UN);
        fclose($fp);
        chmod($cache_file, 0644);
        $time = time() + $expire;
        touch($cache_file, $time);
        return $this;
      } else {
        return false;
      }
    } else {
      trigger_error('ERROR -> ' . __METHOD__ . ": dirCache Cannot lock/truncate the cache file: $cache_file", E_USER_NOTICE);
      return false;
    }
  }

  /**
   * Retrieve item from the server
   *
   * @param string $key
   */
  public function Get($key){
    $dalmp_cache_dir = defined('DALMP_CACHE_DIR') ? DALMP_CACHE_DIR : '/tmp/dalmp';
    $dalmp_cache_dir = $dalmp_cache_dir . '/' . substr($key, 0, 2);
    $cache_file = "$dalmp_cache_dir/dalmp_$key.cache";
    $content = @file_get_contents($cache_file);
    if ($content) {
      $cache = unserialize(file_get_contents($cache_file));
      $time = time();
      $cache_time = filemtime($cache_file);
      $life = $cache_time - $time;
      if ($life > 0) {
        return $cache;
      } else {
        @unlink($cache_file);
        return false;
      }
    } else {
      return false;
    }
  }

  /**
   * Delete item from the server
   *
   * @param string $key
   */
  public function Delete($key){
  }

  /**
   * Flush cache
   */
  public function Flush(){
  }

  /**
   * Get cache stats
   */
  public function getStats(){
  }

  /**
   * X execute/call custom methods
   *
   * @return cache object
   */
  public function X(){
  }

  /**
   * try to establish a connection
   */
  private function connect() {
    if ($this->cache instanceof Redis) {
      return True;
    } else {
      if (!extension_loaded('redis')) {
        trigger_error('ERROR ->' . __METHOD__ . ': redis extension not loaded! - http://github.com/nicolasff/phpredis', E_USER_NOTICE);
        return False;
      }

      $redis = new \Redis();

      try {
        /**
         * if a / found try to connect via socket
         */
        if (strpos($this->host, '/') !== false) {
          $this->cache = $redis->connect($this->host) ? $redis : False;
        } else {
          $this->cache = $redis->connect($this->host, $this->port, $this->timeout) ? $redis : False;
        }
      } catch (RedisException $e) {
        trigger_error('ERROR ->' . __METHOD__ . $e->getMessage(), E_USER_NOTICE);
        return False;
      }
      return True;
    }
  }

}
