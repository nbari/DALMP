<?php
namespace DALMP\Cache;

/**
 * Redis - Cache cache class
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class Disk implements CacheInterface {

  public $cache_dir;

  /**
   * Constructor
   *
   * @param string $dir
   */
  public function __construct($dir) {
    if (!file_exists($dir)) {
      if (!mkdir($dir, 0750, True)) {
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

    $cache_path = sprintf('%s/%s/%s/%s', $this->cache_dir, substr($key, 0, 2), substr($key, 2, 2),  substr($key, 4, 2));

    if (!file_exists($cache_path)) {
      if (!mkdir($cache_path, 0750, True)) {
        throw new Exception("Can't create cache directory tree : $cache_path");
      }
    }

    $cache_file = sprintf('%s/%s', $cache_path, "dalmp_{$key}.cache");

    if (!($fp = fopen($cache_file, 'w'))) {
      throw new Exception(__METHOD__ . ": Cannot create cache file $cache_file");
    }

    if (flock($fp, LOCK_EX) && ftruncate($fp, 0)) {
      if (fwrite($fp, serialize($value))) {
        flock($fp, LOCK_UN);
        fclose($fp);
        $time = time() + $expire;
        return touch($cache_file, $time);
      } else {
        return False;
      }
    } else {
      throw new Exception(__METHOD__ . ": Cannot lock/truncate the cache file: $cache_file");
    }
  }

  /**
   * Retrieve item from the server
   *
   * @param string $key
   */
  public function Get($key){
    $key = sha1($key);
    $cache_file = sprintf('%s/%s/%s/%s/%s', $this->cache_dir, substr($key, 0, 2), substr($key, 2, 2),  substr($key, 4, 2), "dalmp_{$key}.cache");
    $content = @file_get_contents($cache_file);
    if ($content) {
      $cache = unserialize($content);
      $cache_time = filemtime($cache_file);
      $life = $cache_time - time();
      if ($life > 0) {
        return $cache;
      } else {
        @unlink($cache_file);
        return False;
      }
    } else {
      return False;
    }
  }

  /**
   * Delete item from the server
   *
   * @param string $key
   */
  public function Delete($key){
    $key = sha1($key);
    $cache_file = sprintf('%s/%s/%s/%s/%s', $this->cache_dir, substr($key, 0, 2), substr($key, 2, 2),  substr($key, 4, 2), "dalmp_{$key}.cache");
    return @unlink($cache_file) ? $this : false;
  }

  /**
   * Flush cache
   */
  public function Flush(){
    return $this->delTree($this->cache_dir);
  }

  /**
   * Get cache stats
   */
  public function Stats(){
  }

  /**
   * X execute/call custom methods
   *
   * @return cache object
   */
  public function X(){
    return $this;
  }

  /**
   * try to establish a connection
   */
  private function connect() {
    return True;
  }

  /**
   * recursive method for deleting directories
   *
   * @param string $dir
   * @return TRUE on success or FALSE on failure.
   */
  protected function delTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  }

}
