<?php
namespace DALMP\Sessions;

class Memcache implements \SessionHandlerInterface {
  /**
   * DALMP\Cache\Memcache instance
   *
   * @access protected
   * @var DALMP\Cache\Memcache
   */
  protected $cache;

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
   * @param DALMP\Cache\Memcache $cache instance
   * @param string $sessions_ref global variable to be stored as reference
   */
  public function __construct(\DALMP\Cache\Memcache $cache, $sessions_ref = 'UID') {
    $this->cache = $cache;
    $this->sessions_ref = $sessions_ref;
    $this->dalmp_sessions_ref = defined('DALMP_SESSIONS_REF') ? DALMP_SESSIONS_REF : $sessions_ref;
    $this->dalmp_sessions_key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : __FILE__;
  }

  public function close() {
    return True;
  }

  public function destroy($session_id) {
    $key = sprintf('DALMP_%s', sha1($this->dalmp_sessions_ref . $session_id));
    $this->cache->Delete($key);

    /**
     * destroy REF on cache
     */
    $ref_key = sprintf('DALMP_REF_%s', sha1($this->dalmp_sessions_ref . $this->dalmp_sessions_key));
    $refs = $this->cache->Get($ref_key);

    if (is_array($refs)) {
      unset($refs[$key]);
    }

    $this->cache->Set($ref_key, $refs, 86400);

    return True;
  }

  public function gc($maxlifetime) {
    return True;
  }

  public function open($save_path, $name) {
    return True;
  }

  public function read($session_id) {
    $key = sprintf('DALMP_%s', sha1($this->dalmp_sessions_ref . $session_id));
    return $this->cache->Get($key);
  }

  public function write($session_id, $session_data) {
    $ref = (isset($GLOBALS[$this->dalmp_sessions_ref]) && !empty($GLOBALS[$this->dalmp_sessions_ref])) ? $GLOBALS[$this->dalmp_sessions_ref] : NULL;
    $timeout = ini_get('session.gc_maxlifetime');
    $expiry = time() + $timeout;

    $key = sprintf('DALMP_%s', sha1($this->dalmp_sessions_ref . $session_id));
    $this->cache->Set($key, $session_data, $timeout);

    /**
     * store REF on cache
     */
    if ($ref) {
      $ref_key = sprintf('DALMP_REF_%s', sha1($this->dalmp_sessions_ref . $this->dalmp_sessions_key));
      $refs = $this->cache->Get($ref_key);

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
      $this->cache->Set($ref_key, $refs, 86400);
    }

    return True;
  }

  /**
   * getSessionsRefs
   *
   * @param int $expiry
   * @return array of sessions containing any reference
   */
  public function getSessionsRefs($expired_sessions = False) {
    $ref_key = sprintf('DALMP_REF_%s', sha1($this->dalmp_sessions_ref . $this->dalmp_sessions_key));
    return $this->cache->Get($ref_key) ?: array();
  }

  /**
   * getSessionRef
   *
   * @param string $ref
   * @return array of session containing a specific reference
   */
  public function getSessionRef($ref) {
    $refs = $this->getSessionsRefs();
    $rs = array();

    foreach ($refs as $key => $data) {
      if (key($data) == $ref) {
        $rs[$key] = $data;
      }
    }

    return $rs;
  }

  /**
   * delSessionRef - delete sessions containing a specific reference
   *
   * @param string $ref
   * @return boolean
   */
  public function delSessionRef($ref) {
    $ref_key = sprintf('DALMP_REF_%s', sha1($this->dalmp_sessions_ref . $this->dalmp_sessions_key));
    $refs = $this->cache->Get($ref_key);

    if (is_array($refs)) {
      foreach ($refs as $key => $data) {
        if (key($data) == $ref) {
          unset($refs[$key]);
          $this->cache->Delete($key);
        }
      }
    }

    return $this->cache->Set($ref_key, $refs, 86400);
  }

}
