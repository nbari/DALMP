<?php
namespace DALMP\Sessions;

class SQLite implements \SessionHandlerInterface {

  /**
   * REF - field used for storing references
   * @access private
   * @var mixed
   */
  private $sessions_ref;

  /**
   * SQLite instance
   *
   * @access protected
   * @var SQLite3
   */
  protected $sdb;

  /**
   * constructor
   *
   * @param string $filename Path to the SQLite database
   * @param string $encryption_key
   */
  public function __construct($filename = False, $sessions_ref = 'UID', $encryption_key = False) {
    if (!$filename) {
      if (!is_writable('/tmp')) {
        if (!is_dir('/tmp') && !mkdir('/tmp', 0700, True)) {
          throw new \Exception('/tmp  not accessible');
        }
      }
      $filename = '/tmp/dalmp_sessions.db';
    }

    $this->sdb = new \SQLite3($filename);
    $this->sdb->busyTimeout(2000);

    if ($encryption_key) {
      $this->sdb->exec("PRAGMA key='{$encryption_key}'");
    }

    $this->sdb->exec('PRAGMA synchronous=OFF; PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY');
    $this->sdb->exec('CREATE TABLE IF NOT EXISTS dalmp_sessions (sid VARCHAR NOT NULL, expiry INTEGER NOT NULL, data TEXT, ref TEXT, PRIMARY KEY(sid)); CREATE INDEX IF NOT EXISTS "dalmp_index" ON dalmp_sessions ("sid" DESC, "expiry" DESC, "ref" DESC)');
    $this->sessions_ref = $sessions_ref;
  }

  public function close() {
    $this->sdb->busyTimeout(0);
    $this->sdb->close();
    return True;
  }

  public function destroy($session_id) {
    $stmt = $this->sdb->prepare('DELETE FROM dalmp_sessions WHERE sid=:sid');
    $stmt->bindValue(':sid', $session_id, SQLITE3_TEXT);
    return $stmt->execute() ? True : False;
  }

  public function gc($maxlifetime) {
    $stmt = $this->sdb->prepare('DELETE FROM dalmp_sessions WHERE expiry < :expiry');
    $stmt->bindValue(':expiry', time(), SQLITE3_INTEGER);
    $stmt->execute();
    return $this->sdb->exec('VACUUM');
  }

  public function open($save_path, $name) {
    return True;
  }

  public function read($session_id) {
    $stmt = $this->sdb->prepare('SELECT data FROM dalmp_sessions WHERE sid=:sid AND expiry >=:expiry');
    $stmt->bindValue(':sid', $session_id, SQLITE3_TEXT);
    $stmt->bindValue(':expiry', time(), SQLITE3_INTEGER);
    if ($query = $stmt->execute()) {
      $rs = $query->fetchArray(SQLITE3_ASSOC);
      return $rs['data'];
    } else {
      return False;
    }
  }

  public function write($session_id, $session_data) {
    $ref = (isset($GLOBALS[$this->sessions_ref]) && !empty($GLOBALS[$this->sessions_ref])) ? $GLOBALS[$this->sessions_ref] : NULL;
    $expiry = time() + ini_get('session.gc_maxlifetime');
    $stmt = $this->sdb->prepare('INSERT OR REPLACE INTO dalmp_sessions (sid, expiry, data, ref) VALUES (:sid, :expiry, :data, :ref)');
    $stmt->bindValue(':sid', $session_id, SQLITE3_TEXT);
    $stmt->bindValue(':expiry', $expiry, SQLITE3_INTEGER);
    $stmt->bindValue(':data', $session_data, SQLITE3_TEXT);
    $stmt->bindValue(':ref', $ref, SQLITE3_TEXT);
    return $stmt->execute() ? True : False;
  }

  /**
   * getSessionsRefs - get all sessions containing references
   *
   * @param int $expiry
   * @return array of sessions
   */
  public function getSessionsRefs($expired_sessions = False) {
    $refs = array();
    $rs = ($expired_sessions) ? $this->sdb->query("SELECT sid, ref, expiry FROM dalmp_sessions WHERE expiry > strftime('%s','now')") : $this->sdb->query('SELECT sid, ref, expiry FROM dalmp_sessions');
    while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
      $refs[$row['sid']] = array($row['ref'] => $row['expiry']);
    }
    return $refs;
  }

  /**
   * getSessionRef - get sessions containing a specific reference
   *
   * @param string $ref
   * @return array sessions
   */
  public function getSessionRef($ref) {
    $refs = array();
    $stmt = $this->sdb->prepare('SELECT sid, ref, expiry FROM dalmp_sessions WHERE ref=:ref');
    $stmt->bindValue(':ref', $ref, SQLITE3_TEXT);
    if ($rs = $stmt->execute()) {
      while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
        $refs[$row['sid']] = array($row['ref'] => $row['expiry']);
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
    $stmt = $this->sdb->prepare('DELETE FROM dalmp_sessions WHERE ref=:ref');
    $stmt->bindValue(':ref', $ref, SQLITE3_TEXT);
    return $stmt->execute() ? True : False;
  }

}
