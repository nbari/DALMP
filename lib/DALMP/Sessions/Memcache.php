<?php
namespace DALMP\Sessions;

class Memcache implements \SessionHandlerInterface {
  /**
   * DALMP\Cache\Memcache instance
   *
   * @access protected
   * @var DALMP\Cache\Memcache
   */
  protected $memcache;

  /**
   * REF - field used for storing references
   *
   * @access private
   * @var mixed
   */
  private $dalmp_sessions_ref;

  /**
   * key used for creating more entropy when storing the sessions on a
   * key/value cache engine, useful when serving multiple sites
   *
   * @access private
   * @var mixed
   */
  private $dalmp_sessions_key;

  /**
   * constructor
   *
   * @param DALMP\Cache\Memcache $memcache instance
   * @param string $sessions_ref global variable to be stored as reference
   */
  public function __construct(\DALMP\Cache\Memcache $memcache, $sessions_ref = 'UID') {
    $this->memcache = $memcache;
    $this->sessions_ref = $sessions_ref;
    $this->dalmp_sessions_ref = defined('DALMP_SESSIONS_REF') ? DALMP_SESSIONS_REF : $sessions_ref;
    $this->dalmp_sessions_key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : __FILE__;
  }

  public function close() {
    return True;
  }

  public function destroy($session_id) {
  }

  public function gc($maxlifetime) {
    return True;
  }

  public function open($save_path, $name) {
    return True;
  }

  public function read($session_id) {
    $key = sprintf('DALMP_%s.sess', sha1($this->dalmp_sessions_ref . $session_id));
    return $this->memcache->Get($key, $session_data, $timeout);
  }

  public function write($session_id, $session_data) {
    $ref = (isset($GLOBALS[$this->dalmp_sessions_ref]) && !empty($GLOBALS[$this->dalmp_sessions_ref])) ? $GLOBALS[$this->dalmp_sessions_ref] : NULL;
    $timeout = ini_get('session.gc_maxlifetime');
    $expiry = time() + $timeout;

    $key = sprintf('DALMP_%s.sess', sha1($this->dalmp_sessions_ref . $session_id));
    $this->memcache->Set($key, $session_data, $timeout);

    /**
     * store REF on cache
     */
    if ($ref) {
      $ref_key = sprintf('DALMP_REF_%s', sha1($this->dalmp_sessions_ref . $this->dalmp_sessions_key));
      $refs = $this->memcache->Get($ref_key);

      if ($refs) {
        foreach ($refs as $rkey => $rexpiry) {
          if (current($rexpiry) < time()) {
            unset($refs[$rkey]);
          }
        }
      } else {
        $refs = array();
      }

      $refs[$key] = array($ref => $expiry);
      $this->memcache->Set($ref_key, $refs, 0);
    }

    return True;

  }

}
