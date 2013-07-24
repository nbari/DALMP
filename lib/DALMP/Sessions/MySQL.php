<?php
namespace DALMP\Sessions;

/**
 * Sessions\MySQL
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class MySQL implements \SessionHandlerInterface {

  /**
   * DALMP\Database instance
   *
   * @access protected
   * @var DALMP\Database
   */
  protected $DB;

  /**
   * REF - field used for storing references
   *
   * @access private
   * @var mixed
   */
  private $dalmp_sessions_ref;

  /**
   * table to use for sessions
   * @access private
   * @var mixed
   */
  private $dalmp_sessions_table = 'dalmp_sessions';

  /**
   * constructor
   *
   * @param DALMP\Database $db instance
   * @param string $sessions_ref global variable to be stored as reference
   */
  public function __construct(\DALMP\Database $DB, $sessions_ref = 'UID') {
    $this->DB = $DB;
    $this->dalmp_sessions_ref = defined('DALMP_SESSIONS_REF') ? DALMP_SESSIONS_REF : $sessions_ref;

    if (defined('DALMP_SESSIONS_TABLE')) {
      $this->dalmp_sessions_table = DALMP_SESSIONS_TABLE;
    }
  }

  public function close() {
    return True;
  }

  public function destroy($session_id) {
    $sql = 'DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE sid=?';
    return $this->DB->PExecute($sql, $sid);
  }

  public function gc($maxlifetime) {
    $sql = 'DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE expiry < UNIX_TIMESTAMP()';
    $this->DB->Execute($sql);
    $sql = 'OPTIMIZE TABLE ' . $this->dalmp_sessions_table;
    $this->DB->Execute($sql);
    return True;
  }

  public function open($save_path, $name) {
    return True;
  }

  public function read($session_id) {
    return ($rs = $this->DB->PGetOne('SELECT data FROM ' . $this->dalmp_sessions_table . ' WHERE sid=? AND expiry >=?', $sid, time())) ? $rs : '';
  }

  public function write($session_id, $session_data) {
    $ref = (isset($GLOBALS[$this->sessions_ref]) && !empty($GLOBALS[$this->sessions_ref])) ? $GLOBALS[$this->sessions_ref] : NULL;
    $expiry = time() + ini_get('session.gc_maxlifetime');
    $sql = "REPLACE INTO $this->dalmp_sessions_table (sid, expiry, data, ref) VALUES(?,?,?,?)";
    return $this->DB->PExecute($sql, $session_id, $expiry, $session_data, $ref);
  }

  /**
   * getSessionsRefs
   *
   * @param int $expiry
   * @return array of sessions containing any reference
   */
  public function getSessionsRefs($expired_sessions = False) {
    $refs = array();

    $db_refs = ($expired_sessions) ? $this->DB->GetAll("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table WHERE expiry > UNIX_TIMESTAMP() AND ref NOT NULL") : $this->DB->GetAll("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table WHERE ref NOT NULL");

    if ($db_refs) {
      foreach ($db_refs as $value) {
        $refs[$value['sid']] = array($value['ref'] => $value['expiry']);
      }
    }

    return $refs;
  }

  /**
   * getSessionRef
   *
   * @param string $ref
   * @return array of session containing a specific reference
   */
  public function getSessionRef($ref) {
    $refs = array();

    $db_refs = $this->DB->PGetall('SELECT sid, ref, expiry FROM dalmp_sessions WHERE ref=?', $ref);

    if ($db_refs) {
      foreach ($db_refs as $value) {
        $refs[$value['sid']] = array($value['ref'] => $value['expiry']);
      }
    }

    return $refs;
  }

  /**
   * delSessionRef - delete sessions containing a specific reference
   *
   * @param string $ref
   * @return boolean
   */
  public function delSessionRef($ref) {
    return $this->DB->PExecute('DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE ref=?', $ref);
  }

}
