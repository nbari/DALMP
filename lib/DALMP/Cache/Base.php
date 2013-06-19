<?php
namespace DALMP\Cache;

/**
 * CacheBase abstract class
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */

abstract class CacheBase {

  public function Set() {
    if (!$this->_set(....)) {
      disco
    }
    switch (true) {
      case $rs:
        return $this;
        break;

      default :
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
        break;
    }
  }

  /**
   * Retrieve item from the server
   *
   * @param string $key
   */
  public function diskGet($key) {
    $rs = false;
    if ($this->connect()) {
      switch ($this->type) {
        case 'apc':
          $rs = apc_fetch($key);
          break;

        case 'memcache':
          $rs = $this->cache->get($key);
          break;

        case 'redis':
          $rs = unserialize($this->cache->get($key));
          break;
      }
    }
    switch (true) {
      case $rs:
        return $rs;
        break;

      default :
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
        break;
    }
  }

  /*
	 * Delete item from the server
	 *
	 * @param string $key
	 * @chainable
	 */
  public function diskDelete($key = null) {
    $rs = false;
    if ($this->connect()) {
      switch ($this->type) {
        case 'apc':
          $rs = apc_delete($key);
          break;

        case 'memcache':
          $rs = $this->cache->delete($key);
          break;

        case 'redis':
          $rs = $this->cache->delete($key);
          break;
      }
    }

    switch (true) {
      case $rs:
        return $this;
        break;

      default :
        $dalmp_cache_dir = defined('DALMP_CACHE_DIR') ? DALMP_CACHE_DIR : '/tmp/dalmp';
        $dalmp_cache_dir = $dalmp_cache_dir . '/' . substr($key, 0, 2);
        $cache_file = "$dalmp_cache_dir/dalmp_$key.cache";
        return @unlink($cache_file) ? $this : false;
        break;
    }
  }

  /**
   * Flush cache
   */
  public function diskFlush() {
    $rs = false;
    if ($this->connect()) {
      switch ($this->type) {
        case 'apc':
          $rs = apc_clear_cache('user');
          break;

        case 'memcache':
          $rs = $this->cache->flush();
          break;

        case 'redis':
          $rs = $this->cache->flushDB();
          break;
      }
    }

    switch (true) {
      case $rs:
        return $rs;
        break;

      default :
        $dalmp_cache_dir = defined('DALMP_CACHE_DIR') ? DALMP_CACHE_DIR : '/tmp/dalmp';
        return $this->delTree($dalmp_cache_dir);
        break;
    }
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
