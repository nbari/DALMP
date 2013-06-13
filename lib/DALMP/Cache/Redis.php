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

  public function __construct() {
      echo "my namespace is: ",__NAMESPACE__;
  }

  /**
   * host, IP, or the path to a unix domain socket
   *
   * @param string $host
   */
  public function host($host = null) {
  }

  /**
   * Point to the port where the cache is listening for connections.
   * Set this parameter to 0 when using UNIX domain sockets.
   *
   * @param int $port
   */
  public function port($port = null) {
  }

  /**
   * Value in seconds which will be used for connecting
   *
   * @param int $timeout
   */
  public function timeout($timeout = 1){
  }

  /**
   * Store data at the server
   *
   * @param string $key
   * @param string $value
   * @param int $expire time in seconds(default is 0 meaning unlimited)
   */
  public function Set($key, $value, $expire = 0){
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

}
