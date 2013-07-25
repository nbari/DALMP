<?php
namespace DALMP\Cache;

/**
 * CacheInterface
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
interface CacheInterface {

  /**
   * Store data at the server
   *
   * @param string $key
   * @param string $value
   * @param int $ttl time in seconds(default is 0 meaning unlimited)
   * @return bool
   */
  public function Set($key, $value, $ttl = 0);

  /**
   * Retrieve item from the server
   *
   * @param string $key
   * @return mixed
   */
  public function Get($key);

  /*
   * Delete item from the server
   *
   * @param string $key
   * @return bool
   */
  public function Delete($key);

  /**
   * Flush cache
   *
   * @return bool
   */
  public function Flush();

  /**
   * Get cache stats
   *
   * @return bool
   */
  public function Stats();

  /**
   * X execute/call custom methods
   *
   * @return cache object
   */
  public function X();

}
