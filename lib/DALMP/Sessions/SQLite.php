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
  public function __construct($filename = null, $sessions_ref = 'UID', $encryption_key = null) {
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
      $this->sdb->exec("PRAGMA key='" . $encryption_key . "'");
    }

    $this->sdb->exec('PRAGMA synchronous=OFF; PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY');
    $this->sdb->exec('CREATE TABLE IF NOT EXISTS dalmp_sessions (sid varchar(40) NOT NULL, expiry INTEGER NOT NULL, data text, ref text, PRIMARY KEY(sid)); CREATE INDEX IF NOT EXISTS "dalmp_index" ON dalmp_sessions ("sid" DESC, "expiry" DESC, "ref" DESC)');
    $this->sessions_ref = $sessions_ref;
  }

  public function close() {
    $this->sdb->busyTimeout(0);
    $this->sdb->close();
    return True;
  }

  public function destroy($session_id) {
    $sql = "DELETE FROM dalmp_sessions WHERE sid='$session_id'";
    return $this->sdb->exec($sql);
  }

  public function gc($maxlifetime) {
    $sql = "DELETE FROM dalmp_sessions WHERE expiry < " . time();
    $this->sdb->exec($sql);
    $this->sdb->exec('VACUUM');
    return True;
  }

  public function open($save_path, $name) {
    return True;
  }

  public function read($session_id) {
    return $this->sdb->querySingle("SELECT data FROM dalmp_sessions WHERE sid='$session_id' AND expiry >= ". time());
  }

  public function write($session_id, $session_data) {
    $ref = (isset($GLOBALS[$this->sessions_ref]) && !empty($GLOBALS[$this->sessions_ref])) ? $GLOBALS[$this->sessions_ref] : NULL;

    $timeout = ini_get('session.gc_maxlifetime');
    $expiry = time() + $timeout;

    $sql = "INSERT OR REPLACE INTO dalmp_sessions (sid, expiry, data, ref) VALUES ('$session_id',$expiry,'$session_data','$ref')";
    return $this->sdb->exec($sql);
  }

}
