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
class Redis implements Cache {
	private $host;
	private $port;
	private $timeout;

	public function __construct($host='127.0.0.1', $port=6379, $timeout=1, $compress=False) {
		$this->host = $host;
		$this->port = $port;
		$this->timeout = (int) $timeout;
	}

	/**
	 * Store data at the server
	 *
	 * @param string $key
	 * @param string $value
	 * @param int $expire time in seconds(default is 0 meaning unlimited)
	 */
	public function Set($key, $value, $expire = 0) {
		$rs = false;
		if ($this->connect()) {
			$rs = ($expire == 0 || $expire == -1) ? $this->cache->set($key, serialize($value)) : $this->cache->setex($key, $expire, serialize($value));
		}
		if ($rs) {
      return $rs;
		} else {
			/* set cache on disk */
		}
	}

	/**
	 * Retrieve item from the server
	 *
	 * @param string $key
	 */
	public function Get($key){
    $rs = false;
    if ($this->connect()) {
      $rs = unserialize($this->cache->get($key));
    }
    if ($rs) {
      return $rs;
    } else {
      /* get cache from disk */
    }
	}

	/*
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
	protected function connect() {
		if ($this->cache instanceof Redis) {
			return True;
		} else {
			if (!extension_loaded('redis')) {
				trigger_error('ERROR ->' . __METHOD__ . ': redis extension not loaded! - http://github.com/nicolasff/phpredis', E_USER_NOTICE);
				return False;
			}

			$redis = new Redis();

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
