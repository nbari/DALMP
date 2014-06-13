<?php
namespace DALMP\Cache;

/**
 * Memcache
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0.2
 */
class Memcache implements CacheInterface
{
    private $host = '127.0.0.1';
    private $port = 11211;
    private $timeout = 1;
    private $compress = false;
    protected $cache;

    /**
     * Constructor
     *
     * @param string $host
     * @param int    $port
     * @param int    $timeout
     * @param int    $compress
     */
    public function __construct()
    {
        $args = func_get_args();

        if ($args) {
            $this->host = isset($args[0]) ? $args[0] : '127.0.0.1';
            $this->port = isset($args[1]) ? (int) $args[1] : 11211;
            $this->timeout = isset($args[2]) ? (int) $args[2] : 1;
            if (isset($args[3])) {
                $this->compress = MEMCACHE_COMPRESSED;
            }
        }
    }

    /**
     * Store data at the server
     *
     * @param string $key
     * @param string $value
     * @param int    $expire time in seconds(default is 0 meaning unlimited)
     */
    public function Set($key, $value, $expire = 0)
    {
        if ($this->connect()) {
            ($this->compress === false) && $this->cache->setCompressThreshold(0);

            return $this->cache->set($key, $value, $this->compress, $expire);
        } else {
            return false;
        }
    }

    /**
     * Retrieve item from the server
     *
     * @param string $key
     */
    public function Get($key)
    {
        return $this->connect() ? $this->cache->get($key) : false;
    }

    /**
     * Delete item from the server
     *
     * @param string $key
     */
    public function Delete($key)
    {
        return $this->connect() ? $this->cache->delete($key) : false;
    }

    /**
     * Flush cache
     */
    public function Flush()
    {
        return $this->connect() ? $this->cache->flush() : false;
    }

    /**
     * Get cache stats
     */
    public function Stats()
    {
        return $this->connect() ? $this->cache->getStats() : false;
    }

    /**
     * X execute/call custom methods
     *
     * @return cache object
     */
    public function X()
    {
        return $this->connect() ? $this->cache : false;
    }

    /**
     * try to establish a connection
     */
    private function connect()
    {
        if ($this->cache instanceof MemCache) {
            return true;
        } else {
            if (!extension_loaded('memcache')) {
                throw new \Exception(__CLASS__ . 'Memcache PECL extension not loaded! - http://pecl.php.net/package/memcache');
            }

            $memcache = new \Memcache();

            /**
             * if a / found try to connect via socket
             */
            if (strpos($this->host, '/') !== false) {
                return $this->cache = $memcache->connect($this->host) ? $memcache : false;
            } else {
                return $this->cache = $memcache->connect($this->host, $this->port, $this->timeout) ? $memcache : false;
            }
        }
    }

}
