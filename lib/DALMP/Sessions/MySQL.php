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
  protected $db;

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
  public function __construct(\DALMP\Database $db, $sessions_ref = 'UID') {
    $this->db = $db;
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
    return $this->db->PExecute($sql, $sid);
  }

  public function gc($maxlifetime) {
    $sql = 'DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE expiry < UNIX_TIMESTAMP()';
    $this->storage->Execute($sql);
    $sql = 'OPTIMIZE TABLE ' . $this->dalmp_sessions_table;
    $this->storage->Execute($sql);
    return true;
  }

  public function open($save_path, $name) {
  }

  public function read($session_id) {
  }

  public function write($session_id, $session_data) {
  }

}
