<?php
namespace DALMP;

/**
 * Sessions - Session handler class
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class Sessions {

  /**
   * Sessions storage
   * @access private
   * @var string
   */
  private $storage;

  /**
   * REF - field used for storing references
   * @access private
   * @var mixed
   */
  private $dalmp_sessions_ref;

  /**
   * key used for sessions when stored on cache
   * @access private
   * @var mixed
   */
  private $dalmp_sessions_key;

  /**
   * table to use for sessions
   * @access private
   * @var mixed
   */
  private $dalmp_sessions_table = 'dalmp_sessions';

  /**
   * sqlite database for sessions
   * @access private
   * @var mixed
   */
  private $dalmp_sessions_sqlite_db = 'dalmp_sessions.db';

  /**
   * sqlite object
   */
  protected $sdb;

  /**
   * Sessions object.
   *
   * @param $storage DALMP_DB or DALMP_Cache object
   */
  public function __construct($storage = null) {
    $this->storage = is_object($storage) ? $storage : 'sqlite';

    if ($this->storage === 'sqlite') {
      $this->dalmp_sessions_sqlite_db = defined('DALMP_SESSIONS_SQLITE_DB') ? DALMP_SESSIONS_SQLITE_DB : $this->dalmp_sessions_sqlite_db;
    }

    $this->dalmp_sessions_table = defined('DALMP_SESSIONS_TABLE') ? DALMP_SESSIONS_TABLE : $this->dalmp_sessions_table;

    $this->dalmp_sessions_ref = defined('DALMP_SESSIONS_REF') ? DALMP_SESSIONS_REF : 'UID';

    $this->dalmp_sessions_key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : 'dalmp_sessions_key';

    session_module_name('user');
    session_set_save_handler(array($this, 'Sopen'),
      array($this, 'Sclose'),
      array($this, 'Sread'),
      array($this, 'Swrite'),
      array($this, 'Sdestroy'),
      array($this, 'Sgc'));
    register_shutdown_function('session_write_close');

    ini_set('session.gc_maxlifetime', defined('DALMP_SESSIONS_MAXLIFETIME') ? DALMP_SESSIONS_MAXLIFETIME : get_cfg_var('session.gc_maxlifetime'));
    ini_set('session.name', 'DALMP');
    ini_set('session.use_cookies', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_trans_sid', 0);
    @ini_set('session.hash_function', 1); // sha1
    @ini_set('session.hash_bits_per_character', 5);

    session_start();
  }

  /**
   * The open handler
   */
  public function Sopen() {
    switch (true) {
    case $this->storage instanceof DALMP:
      break;

    case $this->storage instanceof DALMP_Cache:
      break;

    default :
      $this->sdb = new SQLite3($this->dalmp_sessions_sqlite_db);
      $this->sdb->busyTimeout(2000);
      if (defined('DALMP_SQLITE_ENC_KEY')) $this->sdb->exec("PRAGMA key='" . DALMP_SQLITE_ENC_KEY . "'");
      $this->sdb->exec('PRAGMA synchronous=OFF; PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY');
      $rs = $this->sdb->exec('CREATE TABLE IF NOT EXISTS ' . $this->dalmp_sessions_table . ' (sid varchar(40) NOT NULL, expiry INTEGER NOT NULL, data text, ref text, PRIMARY KEY(sid)); CREATE INDEX IF NOT EXISTS "dalmp_index" ON ' . $this->dalmp_sessions_table . ' ("sid" DESC, "expiry" DESC, "ref" DESC)');
      break;
    }
    return true;
  }

  /**
   * The close handlr
   */
  public function Sclose() {
    if (is_object($this->sdb)) {
      $this->sdb->busyTimeout(0);
      $this->sdb->close();
    }
    return true;
  }

  /**
   * The read handler
   */
  public function Sread($sid) {
    $expiry = time();
    switch (true) {
    case $this->storage instanceof DALMP_Cache:
      $key = sha1($this->dalmp_sessions_key.$sid);
      return $this->storage->get($key);
      break;

    case $this->storage instanceof DALMP:
      return ($rs = $this->storage->PGetOne('SELECT data FROM ' . $this->dalmp_sessions_table . ' WHERE sid=? AND expiry >=?', $sid, $expiry)) ? $rs : '';
      break;

    default :
      $rs = $this->sdb->querySingle("SELECT data FROM $this->dalmp_sessions_table WHERE sid='$sid' AND expiry >= $expiry");
      return $rs ?  : false;
      break;
    }
  }

  /**
   * The write handler
   */
  public function Swrite($sid, $data) {
    $ref = (isset($GLOBALS[$this->dalmp_sessions_ref]) && !empty($GLOBALS[$this->dalmp_sessions_ref])) ? $GLOBALS[$this->dalmp_sessions_ref] : null;
    $timeout = ini_get('session.gc_maxlifetime');
    $expiry = time() + $timeout;

    switch (true) {
    case $this->storage instanceof DALMP_Cache:
      $key = sha1($this->dalmp_sessions_key.$sid);
      $rs = $this->storage->Set($key, $data, $timeout);
      /**
       * store REF on cache
       */
      if ($ref) {
        $ref_key = sha1($this->dalmp_sessions_ref.$this->dalmp_sessions_key);
        $refs = $this->storage->Get($ref_key);
        switch (true) {
          case $refs;
          foreach ($refs as $rkey => $rexpiry) {
            if (current($rexpiry) < time()) {
              unset($refs[$rkey]);
            }
          }
          break;

        default :
          $refs = array();
        }
        $refs[$key] = array($ref => $expiry);
        $this->storage->Set($ref_key, $refs, 0);
      }
      break;

    case $this->storage instanceof DALMP:
      $sql = "REPLACE INTO $this->dalmp_sessions_table (sid, expiry, data, ref) VALUES(?,?,?,?)";
      $this->storage->PExecute($sql, $sid, $expiry, $data, $ref);
      break;

    default :
      $sql = "INSERT OR REPLACE INTO $this->dalmp_sessions_table (sid, expiry, data, ref) VALUES ('$sid',$expiry,'$data','$ref')";
      $this->sdb->exec($sql);
      break;
    }
    return true;
  }

  /**
   * The destroy handler
   */
  public function Sdestroy($sid) {
    switch (true) {
    case $this->storage instanceof DALMP_Cache:
      $key = sha1($this->dalmp_sessions_key.$sid);
      $this->storage->delete($key);
      /**
       * destroy REF on cache
       */
      $ref_key = sha1($this->dalmp_sessions_ref.$this->dalmp_sessions_key);
      $refs = $this->storage->Get($ref_key);
      if (is_array($refs)) {
        unset($refs[$key]);
      }
      $this->storage->Set($ref_key, $refs, 0);
      return true;
      break;

    case $this->storage instanceof DALMP:
      $sql = 'DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE sid=?';
      return $this->storage->PExecute($sql, $sid);
      break;

    default :
      $sql = "DELETE FROM $this->dalmp_sessions_table WHERE sid='$sid'";
      return $this->sdb->exec($sql);
      break;
    }
  }

  /**
   * The garbage collector
   */
  public function Sgc() {
    switch (true) {
    case $this->storage instanceof DALMP:
      $sql = 'DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE expiry < UNIX_TIMESTAMP()';
      $this->storage->Execute($sql);
      $sql = 'OPTIMIZE TABLE ' . $this->dalmp_sessions_table;
      $this->storage->Execute($sql);
      return true;
      break;

    case $this->sdb instanceof SQLite3:
      $sql = "DELETE FROM $this->dalmp_sessions_table WHERE expiry < " . time();
      $this->sdb->exec($sql);
      $this->sdb->exec('VACUUM');
      return true;
      break;

    default :
      return true;
    }
  }

  /**
   * getSessionsRefs - get all sessions containint references
   *
   * @param int $expiry
   * @return array sessions
   */
  public function getSessionsRefs($expiry=null) {
    $refs = array();
    switch (true) {
    case $this->storage instanceof DALMP_Cache:
      $ref_key = sha1($this->dalmp_sessions_ref.$this->dalmp_sessions_key);
      $refs = $this->storage->Get($ref_key) ?: array();
      break;

    case $this->storage instanceof DALMP:
      $db_refs = isset($expiry) ? $this->storage->GetAll("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table WHERE expiry > UNIX_TIMESTAMP()") : $this->storage->GetAll("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table");
      if ($db_refs) {
        foreach ($db_refs as $value) {
          $refs[$value['sid']] = array($value['ref'] => $value['expiry']);
        }
      }
      break;

    default :
      $db_refs = isset($expiry) ? $this->sdb->query("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table WHERE expiry > strftime('%s','now')") : $this->sdb->query("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table");
      while ($value = $db_refs->fetchArray(SQLITE3_ASSOC)) {
        $refs[$value['sid']] = array($value['ref'] => $value['expiry']);
      }
    }
    return $refs;
  }

  /**
   * getSessionsRef - get session containing a specific reference
   *
   * @param string $ref
   * @return array sessions
   */
  public function getSessionRef($ref) {
    $refs = $this->getSessionsRefs();
    $rs = array();
    foreach ($refs as $key => $expiry) {
      if (key($expiry) == $ref) {
        $rs[$key] = key($expiry);
      }
    }
    return $rs;
  }

  /**
   * del sessions ref - deletes sessions containing a reference
   *
   * @param string $ref
   */
  public function delSessionRef($ref) {
    switch (true) {
    case $this->storage instanceof DALMP_Cache:
      $ref_key = sha1($this->dalmp_sessions_ref.$this->dalmp_sessions_key);
      $refs = $this->storage->Get($ref_key);
      if (is_array($refs)) {
        foreach ($refs as $key => $expiry) {
          if (key($expiry) == $ref) {
            unset($refs[$key]);
            $this->storage->Delete($key);
          }
        }
        $this->storage->Set($ref_key, $refs, 0);
      }
      return true;
      break;

    case $this->storage instanceof DALMP:
      return $this->storage->PExecute('DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE ref=?', $ref);
      break;

    default :
      return $this->sdb->exec("DELETE FROM $this->dalmp_sessions_table WHERE ref='$ref'");
    }
  }

  /**
   * regenerate id - regenerate sessions and create a fingerprint, helps to
   * prevent HTTP session hijacking attacks.
   *
   * @param int $check_ipv4_blocks
   */
  public function regenerate_id($check_ipv4_blocks = null) {
    $fingerprint = 'DALMP-|' . @$_SERVER['HTTP_ACCEPT_LANGUAGE'] . @$_SERVER['HTTP_USER_AGENT'] . '|';
    if ($check_ipv4_blocks) {
      $num_blocks = abs($check_ipv4_blocks);
      if ($num_blocks > 4) {
        $num_blocks = 4;
      }
      if ($ip = $this->getIPv4()) { // pending validation for ipv6
        $blocks = explode('.', $ip);
        for ($i = 0; $i < $num_blocks; $i++) {
          $fingerprint.= $blocks[$i] . '.';
        }
      }
    }
    $fingerprint = sha1($fingerprint);
    $old_sid = session_id();
    if ( (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint'] != $fingerprint) ) {
      $_SESSION = array();
      session_destroy();
    }
    if (session_regenerate_id(true)) {
      $_SESSION['fingerprint'] = $fingerprint;
      return true;
    } else {
      return false;
    }
  }

  /**
   * getIPv4 - find the IP address of the client
   *
   * @return IPv4
   */
  public function getIPv4() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
      if (array_key_exists($key, $_SERVER) === true) {
        foreach (explode(',', $_SERVER[$key]) as $ip) {
          $ip = trim($ip);
          if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
            return $ip;
          }
        }
      }
    }
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
  }

}
