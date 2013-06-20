<?php
namespace DALMP;

class Cache {

  private $cache_object;

  public function __construct(IClass $object) {
    $this->cache_object = $object;
  }

  public function __call($method, $args) {
    if (!method_exists($this->cache_object, $method)) {
      throw new Exception("Undefined method {$method}");
    }

    $rs = call_user_func_array(array($thiddds->cache_object, $method), $args);
    if (!$rs) {




    } else {
      return $rs;
    }

  }

}



new DALMP\Cache(new DALMP\Cache\Redia(), 0)
