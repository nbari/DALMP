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
   * @param string $dir
   */
  public function __construct($dir) {
    if (!file_exists($dir)) {
      if (!mkdir($dalmp_cache_dir, 0750, True)) {
        throw new Exception("Can't create cache directory: $dir");
      }
    }
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
    $key = sha1($key);
    $cache_file = $this->cache_dir . "/dalmp_{$key}.cache";
    if (!($fp = fopen($cache_file, 'w'))) {
      throw new Exception('ERROR -> ' . __METHOD__ . ": Cannot create cache file $cache_file");
    }
    if (flock($fp, LOCK_EX) && ftruncate($fp, 0)) {
      if (fwrite($fp, serialize($value))) {
        flock($fp, LOCK_UN);
        fclose($fp);
        chmod($cache_file, 0644);
        $time = time() + $expire;
        return touch($cache_file, $time);
      } else {
        return False;
      }
    } else {
      throw new Exception('ERROR -> ' . __METHOD__ . ": Cannot lock/truncate the cache file: $cache_file");
    }
  }

  /**
   * Retrieve item from the server
   *
   * @param string $key
   */
  public function Get($key){
    $key = sha1($key);
    $cache_file = $this->cache_dir . "/dalmp_{$key}.cache";
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