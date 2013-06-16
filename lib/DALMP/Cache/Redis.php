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
		if (strpos($string, '/') !== false) {
			$this->sock = True;
		}
		$this->host = $host;
		$this->port = $port;
		$this->timeout = (int) $timeout;
	}

	/**
	 * host, IP, or the path to a unix domain socket
	 *
	 * @param string $host
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
	 */
	public function port($port = null) {
		$this->port = $port ? (int) $port : null;
		return $this;
	}

	/**
	 * Value in seconds which will be used for connecting
	 *
	 * @param int $timeout
	 */
	public function timeout($timeout = 1){
		$this->timeout = (int) $timeout;
		return $this;
	}

	/**
	 * Enable / disable compression
	 * currently only works for memcache (nginx)
	 *
	 * @param int $status
	 */
	public function compress($status) {
		return $this;
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
		} else {
			/* cache on disk */
		}
	}

	/**
	 * Retrieve item from the server
	 *
	 * @param string $key
	 */
	public function Get($key){
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
				if (strpos($string, '/') !== false) {
					$this->cache = $redis->connect($this->host) ? $redis : False;
				} else {
					$this->cache = $redis->connect($this->host, $this->port, $this->timeout) ? $redis : False;
				}
			}Catch(RedisException $e) {
				trigger_error('ERROR ->' . __METHOD__ . $e->getMessage(), E_USER_NOTICE);
				return False;
			}
			return True;
		}
	}

}
