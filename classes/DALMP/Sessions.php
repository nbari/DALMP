<?php

/**
 * DALMP_Sessions - Session handler class
 *
 * git clone git://github.com/nbari/DALMP.git
 * @see http://dalmp.googlecode.com
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 2
 */

class DALMP_Sessions {

  /**
   * Sessions storage
   * @access private
   * @var string
   */
  private $storage;

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
   * @param $storage object
   *
   */
  public function __construct($storage = null) {
    $this->storage = is_object($storage) ? $storage : 'sqlite';

		if ($this->storage === 'sqlite') {
			$this->dalmp_sessions_sqlite_db = defined('DALMP_SESSIONS_SQLITE_DB') ? DALMP_SESSIONS_SQLITE_DB : $this->dalmp_sessions_sqlite_db;
		}

		$this->dalmp_sessions_table = defined('DALMP_SESSIONS_TABLE') ? DALMP_SESSIONS_TABLE : $this->dalmp_sessions_table;

    session_module_name('user');
    session_set_save_handler(array(&$this, 'Sopen'),
														 array(&$this, 'Sclose'),
														 array(&$this, 'Sread'),
														 array(&$this, 'Swrite'),
														 array(&$this, 'Sdestroy'),
														 array(&$this, 'Sgc'));
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

  public function Sopen() {
    switch (true) {
      case is_a($this->storage, 'DALMP'):
        break;

      case is_a($this->storage, 'DALMP_Cache'):
        break;

      default :
        $this->sdb = new SQLite3($this->dalmp_sessions_sqlite_db);
        if (defined('DALMP_SQLITE_ENC_KEY')) $this->sdb->exec("PRAGMA key='" . DALMP_SQLITE_ENC_KEY . "'");
        $this->sdb->exec('PRAGMA synchronous=OFF; PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY');
        $rs = $this->sdb->exec('CREATE TABLE IF NOT EXISTS ' . $this->dalmp_sessions_table . ' (sid varchar(40) NOT NULL, expiry INTEGER NOT NULL, data text, ref text, PRIMARY KEY(sid)); CREATE INDEX IF NOT EXISTS "dalmp_index" ON ' . $this->dalmp_sessions_table . ' ("sid" DESC, "expiry" DESC, "ref" DESC)');
        break;
    }
  }

  public function Sclose() {
    if (is_object($this->sdb)) {
      $this->sdb->close();
    }
    return true;
  }

  public function Sread($sid) {
    $expiry = time();
    switch (true) {
			case is_a($this->storage, 'DALMP_Cache'):
        $key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
        $cache = $this->storage->get($sid, $key, false, $this->dalmp_sessions_cache_type);
        $this->debug = $this->debug2 ?  : false;
        return $cache;
        break;

      case is_a($this->storage, 'DALMP'):
        return ($rs = $this->storage->PGetOne('SELECT data FROM ' . $this->dalmp_sessions_table . ' WHERE sid=? AND expiry >=?', $sid, $expiry)) ? $rs : '';
        break;

      default :
        $rs = $this->sdb->querySingle("SELECT data FROM $this->dalmp_sessions_table WHERE sid='$sid' AND expiry >= $expiry");
        return $rs ?  : false;
        break;
    }
  }

  public function Swrite($sid, $data) {

    $field = defined('DALMP_SESSIONS_REF') ? DALMP_SESSIONS_REF : null;
    $ref = (isset($GLOBALS[$field]) && !empty($GLOBALS[$field])) ? $GLOBALS[$field] : null;

    $timeout = ini_get('session.gc_maxlifetime');
    $expiry = time() + ini_get('session.gc_maxlifetime');

    switch (true) {

      case is_a($this->storage, 'DALMP_Cache'):
        $key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
        $rs = $this->storage->set($sid, $data, $timeout, $key, $this->dalmp_sessions_group_cache, false, $this->dalmp_sessions_cache_type);
        if (!$rs) {
          $write2db = true;
          trigger_error("Cache: $this->dalmp_sessions_cache_type, not running or responding", E_USER_NOTICE);
        } else {
          /**
           * store REF on cache
           */
          if (isset($ref)) {
            if (!$this->_sessionsRef($sid, $ref, $key)) {
              $write2db = true;
            }
          }
        }
        break;

      case is_a($this->storage, 'DALMP'):
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

  public function Sdestroy($sid) {

    switch (true) {
      case is_a($this->storage, 'DALMP_Cache'):
        $key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
        $rs = $this->CacheFlush($sid, $key, $this->dalmp_sessions_cache_type);
        /**
         * destroy REF on cache
         */
        $field = defined('DALMP_SESSIONS_REF') ? DALMP_SESSIONS_REF : null;
        $ref = isset($GLOBALS[$field]) ? $GLOBALS[$field] : null;
        if (isset($ref)) {
          $this->_sessionsRef($sid, $ref, $key, true);
        }
        return $rs;
        break;

      case is_a($this->storage, 'DALMP'):
        $sql = 'DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE sid=?';
        return $this->storage->PExecute($sql, $sid);
        break;

      default :
        $sql = "DELETE FROM $this->dalmp_sessions_table WHERE sid='$sid'";
        return $this->sdb->exec($sql);
        break;
    }
  }

  public function Sgc() {
    switch (true) {
      case is_a($this->storage, 'DALMP'):
        $sql = 'DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE expiry < UNIX_TIMESTAMP()';
        $this->storage->Execute($sql);
        $sql = 'OPTIMIZE TABLE ' . $this->dalmp_sessions_table;
        $this->storage->Execute($sql);
        return true;
        break;

      case 'sqlite':
        $sql = "DELETE FROM $this->dalmp_sessions_table WHERE expiry < " . time();
        $this->sdb->exec($sql);
        $this->sdb->exec('VACUUM');
        return true;
        break;
    }
  }

	public function getSessionsRefs($expiry=null) {
		$refs = array();
		switch (true) {
			case is_a($this->storage, 'DALMP_Cache'):
        $key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
				break;

			case is_a($this->storage, 'DALMP'):
	      $db_refs = isset($expiry) ? $this->storage->GetAll("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table WHERE expiry > UNIX_TIMESTAMP()") : $this->storage->GetAll("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table");
		    if ($db_refs) {
					foreach ($db_refs as $value) {
					  $refs[$value['sid']] = array($value['ref'] => $value['expiry']);
					}
				}
				break;

			default :
			  $db_refs = isset($expiry) ? $this->sdb->query("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table WHERE expiry > strftime('%s','now')") : $this->sdb->query("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table");
				while($value = $db_refs->fetchArray(SQLITE3_ASSOC)) {
					$refs[$value['sid']] = array($value['ref'] => $value['expiry']);
				}
		}
		return $refs;
	}

	public function delSessionRef($ref) {
		switch (true) {
			case is_a($this->storage, 'DALMP_Cache'):
				$key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
				break;

			case is_a($this->storage, 'DALMP'):
				return $this->storage->PExecute('DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE ref=?', $ref);
			break;

			default :
			return $this->sdb->exec("DELETE FROM $this->dalmp_sessions_table WHERE ref='$ref'");
		}
	}

	public function regenerate_id($check_ipv4_blocks = null) {
    $fingerprint = 'DALMP-|' . php_uname() . @$_SERVER['HTTP_ACCEPT_LANGUAGE'] . @$_SERVER['HTTP_USER_AGENT'] . '|';
    if ($check_ipv4_blocks) {
      $num_blocks = abs($check_ipv4_blocks);
      if ($num_blocks > 4) {
        $num_blocks = 4;
      }
      if (isset($_SERVER['REMOTE_ADDR'])) { // pending validation for ipv6
        $blocks = explode('.', $_SERVER['REMOTE_ADDR']);
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
    if(session_regenerate_id(true)) {
      $_SESSION['fingerprint'] = $fingerprint;
      return true;
    } else {
      return false;
    }
  }

}

?>