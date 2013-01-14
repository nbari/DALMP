<?php

/**
 * DALMP_Cache - Cache class supporting redis, memcache, apc and dir storage
 *
 * git clone git://github.com/nbari/DALMP.git
 * @see http://dalmp.googlecode.com
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 2
 */

class DALMP_Cache {

  /**
   * Cache types
   * dir, apc, memcache, redis
   * @access private
   * @var string
   */
  private $type = 'dir';

  /**
   * host where Cache is listening for connections
   * This parameter may also specify other transports like
   * unix:///path/to/memcached.sock to use UNIX domain sockets,
   * in this case port must also be set to 0.
   * @access private
   * @var mixed
   */
  private $host;

  /**
   * Port where Cache is listening for connections.
   * to 0 when using UNIX domain sockets.
   * @access private
   * @var int
   */
  private $port;

  /**
   * Value in seconds which will be used for connecting to the Cache
   * @access private
   * @var int
   */
  private $timeout = 1;

  /**
   * Compress
   * @access private
   * @var int
   */
  private $compress = 0;

  /**
   * Cache instance
   * @access protected
   * @var mixed
   */
  protected $cache;

  /**
   * Constructs the Cache object.
   *
   * @param Cache $type string
   */
  public function __construct($type = null) {
    $this->type = in_array($type, array('dir', 'apc', 'memcache', 'redis')) ? $type : 'dir';
  }

  /**
   * host, IP, or the path to a unix domain socket
   *
   * @param string $host
   * @chainable
   */
  public function host($host = null) {
    $this->host = $host ? $host : '127.0.0.1';
    return $this;
  }

  /**
   * Point to the port where the cache is listening for connections.
   * Set this parameter to 0 when using UNIX domain sockets.
   *
   * @param int $port
   * @chainable
   */
  public function port($port = null) {
    $this->port = $port ? (int) $port : null;
    return $this;
  }

  /**
   * Value in seconds which will be used for connecting
   *
   * @param int $timeout
   * @chainable
   */
  public function timeout($timeout = 1) {
    $this->timeout = (int) $timeout;
    return $this;
  }

  /**
   * Change the type of cache
   *
   * @param string $type
   * @chainable
   */
  public function type($type) {
    $this->type = in_array($type, array('dir', 'apc', 'memcache', 'redis')) ? $type : $this->type;
    return $this;
  }

  /**
   * currently only works for memcache
   * @param $enable int
   * @chainable
   */
  public function compress($enable) {
    if ($enable) {
      switch ($this->type) {
        case 'memcache':
          $this->compress = MEMCACHE_COMPRESSED;
          break;
      }
    } else {
      $this->compress = 0;
    }
    return $this;
  }

  /**
   * Connects to a CACHE instance.
   *
   * @access protected
   */
  protected function connect() {
    switch ($this->type) {
      case 'apc':
        return $this->cache ? true : $this->apcCache();
        break;

      case 'memcache':
        return (bool) ($this->cache instanceof MemCache) ? true : $this->memCache();
        break;

      case 'redis':
        return (bool) ($this->cache instanceof Redis) ? true : $this->redisCache();
        break;

      default :
        return false;
        break;
    }
  }

  /**
   * APC cache
   *
   * @access protected
   */
  protected function apcCache() {
    if (!extension_loaded('apc') && !ini_get('apc.enabled')) {
      trigger_error('ERROR -> ' . __METHOD__ . ': APC PECL extension not loaded or enabled!', E_USER_NOTICE);
      return false;
    }
    $this->cache = true;
    return true;
  }

  /**
   * Connects to a memCache server
   *
   * @access protected
   */
  protected function memCache() {
    if (!extension_loaded('memcache')) {
      trigger_error('ERROR -> ' . __METHOD__ . ': Memcache PECL extension not loaded! - http://pecl.php.net/package/memcache', E_USER_NOTICE);
      return false;
    }

    $host = isset($this->host) ? $this->host : '127.0.0.1';
    $port = isset($this->port) ? $this->port : 11211;

    if (strstr($this->host, '/')) {
      $port = 0;
      $host = "unix://$host";
    }

    ($con = memcache_pconnect($host, $port, $this->timeout)) && $this->cache = $con;

    return $con;
  }

  /**
   * Connects to a Redis server
   *
   * @access protected
   */
  protected function redisCache() {
    if (!extension_loaded('redis')) {
      trigger_error('ERROR ->' . __METHOD__ . ': redis extension not loaded! - http://github.com/nicolasff/phpredis', E_USER_NOTICE);
      return false;
    }

    $host = isset($this->host) ? $this->host : '127.0.0.1';
    $port = isset($this->port) ? $this->port : 6379;

    $redis = new Redis();
    try {
      $this->cache = $redis->connect($host, $port, $this->timeout) ? $redis : false;
    } Catch (RedisException $e) {
      trigger_error('ERROR ->' . __METHOD__ . $e->getMessage(), E_USER_NOTICE);
    }
    return $this->cache;
  }

  /**
   * Store data at the server
   *
   * @param string $key
   * @param string $value
   * @param int $expire time in seconds(default is 0 meaning unlimited)
   * @chainable
   */
  public function Set($key, $value, $expire = 0) {
    $rs = false;
    if ($this->connect()) {
      switch ($this->type) {
        case 'apc':
          $rs = apc_store($key, $value, $expire);
          break;

        case 'memcache':
          ( $this->compress === 0) && $this->cache->setCompressThreshold(0);
          $rs = $this->cache->set($key, $value, $this->compress, $expire);
          break;

        case 'redis':
          /**
           * @link https://github.com/igbinary/igbinary/
           */
          if (function_exists('igbinary_serialize')) {
            $rs = ($expire == 0 || $expire == -1) ? $this->cache->set($key, igbinary_serialize($value)) : $this->cache->setex($key, $expire, igbinary_serialize($value));
          } else {
            $rs = ($expire == 0 || $expire == -1) ? $this->cache->set($key, serialize($value)) : $this->cache->setex($key, $expire, serialize($value));
          }
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
  public function Get($key) {
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
          if (function_exists('igbinary_serialize')) {
            $rs = igbinary_unserialize($this->cache->get($key));
          } else {
            $rs = unserialize($this->cache->get($key));
          }
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
  public function Delete($key = null) {
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
  public function Flush() {
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

  /**
   * Get cache stats
   * @return stats
   */
  public function Stats() {
    if ($this->connect()) {
      switch ($this->type) {
        case 'apc':
          return apc_cache_info();
          break;

        case 'memcache':
          return $this->cache->getExtendedStats();
          break;

        case 'redis':
          return $this->cache->info();
          break;
      }
    }
  }

  /**
   * return the cache object
   * works only for memcache / redis
   */
  public function X() {
    if ($this->connect()) {
      switch ($this->type) {
        case 'memcache':
          return $this->cache;
          break;

        case 'redis':
          return $this->cache;
          break;
      }
    }
  }

}
