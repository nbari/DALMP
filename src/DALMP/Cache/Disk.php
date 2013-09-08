<?php
namespace DALMP\Cache;

/**
 * Disk
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
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
  public function __construct() {
    $args = func_get_args();

    if (!$args || !isset($args[0])) {
      $cache_dir = defined('DALMP_CACHE_DIR') ? DALMP_CACHE_DIR : '/tmp/dalmp_cache';
    } else {
      $cache_dir = $args[0];
    }

    if (!is_writable($cache_dir)) {
      if (!is_dir($cache_dir) && !mkdir($cache_dir, 0700, true)) {
        throw new \InvalidArgumentException("$cache_dir  not accessible");
      }
    }

    $this->cache_dir = $cache_dir;
  }

  /**
   * Store data at the server
   *
   * @param string $key
   * @param string $value
   * @param int $expire time in seconds(default is 2592000 '30 days')
   */
  public function set($key, $value, $expire = 2592000) {
    $key = sha1($key);

    $cache_path = sprintf('%s/%s/%s/%s', $this->cache_dir, substr($key, 0, 2), substr($key, 2, 2),  substr($key, 4, 2));

    if (!file_exists($cache_path)) {
      if (!mkdir($cache_path, 0750, true)) {
        throw new \Exception("Can't create cache directory tree : $cache_path");
      }
    }

    $cache_file = sprintf('%s/%s', $cache_path, "dalmp_{$key}.cache");

    if (!($fp = fopen($cache_file, 'w'))) {
      throw new \Exception(__METHOD__ . ": Cannot create cache file $cache_file");
    }

    if (flock($fp, LOCK_EX) && ftruncate($fp, 0)) {
      if (fwrite($fp, serialize($value))) {
        flock($fp, LOCK_UN);
        fclose($fp);
        $time = time() + (int) $expire;
        return touch($cache_file, $time) ? $this : false;
      } else {
        return false;
      }
    } else {
      throw new \Exception(__METHOD__ . ": Cannot lock/truncate the cache file: $cache_file");
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

    if (file_exists($cache_file)) {
      if (filemtime($cache_file) > time()) {
        return unserialize(file_get_contents($cache_file));
      } else {
        unlink($cache_file);
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
    $key = sha1($key);
    $cache_file = sprintf('%s/%s/%s/%s/%s', $this->cache_dir, substr($key, 0, 2), substr($key, 2, 2),  substr($key, 4, 2), "dalmp_{$key}.cache");
    return file_exists($cache_file) ? unlink($cache_file) : true;
  }

  /**
   * Flush cache
   */
  public function Flush(){
    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->cache_dir, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);

    foreach ($files as $fileinfo) {
      if ($fileinfo->isDir()) {
        rmdir($fileinfo->getRealPath());
      } else {
        unlink($fileinfo->getRealPath());
      }
    }

    return rmdir($this->cache_dir);
  }

  /**
   * Get cache stats
   */
  public function Stats(){
    $total_bytes = 0;
    $total_files = 0;

    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->cache_dir, \RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($files as $fileinfo) {
      $total_bytes += $fileinfo->getSize();
      $total_files++;
    }

    return array('Total Bytes' => number_format($total_bytes),
      'Files' => $total_files);
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
