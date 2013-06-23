<?php
namespace DALMP;

class Cache {

  private $cache_object;

  public function __construct(Cache\CacheInterface  $object) {
    $this->cache_object = $object;
  }

  public function __call($method, $args) {
    if (!method_exists($this->cache_object, $method)) {
      throw new \Exception("Undefined method {$method}");
    }

    return call_user_func_array(array($this->cache_object, $method), $args);

  }

}
