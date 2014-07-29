<?php
namespace DALMP;

/**
 * Cache
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0.3
 */
class Cache
{

    /**
     * CacheInterface instance
     *
     * @var CacheInterface
     * @access private
     */
    private $cache_object;

    public function __construct(Cache\CacheInterface $object)
    {
        $this->cache_object = $object;
    }

    public function __call($method, $args)
    {
        if (!method_exists($this->cache_object, $method)) {
            throw new \Exception("Undefined method {$method}");
        }

        return call_user_func_array(array($this->cache_object, $method), $args);
    }

}
