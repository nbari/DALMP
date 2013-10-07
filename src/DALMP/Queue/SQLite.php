<?php
namespace DALMP\Queue;

/**
 * SQLite
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class SQLite implements QueueInterface
{
  /**
   * filename - Path to the SQLite database, or :memory: to use in-memory
   * database.
   *
   * @var string
   */
  private $filename;

  /**
   * queue name
   *
   * @var string
   */
  private $queue_name;

  /**
   * enc_key
   *
   * @var string
   */
  private $enc_key = false;

  /**
   * Constructor
   *
   * @param string $filename
   * @param string $queue
   * @param string $enc_key
   */
  public function __construct($filename, $queue_name = 'default', $enc_key = null)
  {
    $sdb = new \SQLite3($filename);
    $sdb->busyTimeout(2000);

    $this->filename = $filename;
    $this->queue_name = $queue_name;

    if ($enc_key) {
      if ($this->sdb->exec(sprintf("PRAGMA key='%s'", $enc_key))) {
        $this->enc_key = $enc_key;
      }
    }

    $sdb->exec('PRAGMA synchronous=OFF; PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY');
    $sdb->exec('CREATE TABLE IF NOT EXISTS queues (id INTEGER PRIMARY KEY, queue VARCHAR (64) NOT null, data TEXT, cdate DATE)');
    $sdb->busyTimeout(0);
    $sdb->close();
  }

  /**
   * enqueue
   *
   * @param string $value
   * @return boolean
   */
  public function enqueue($value)
  {
    $sdb = new \SQLite3($this->filename);
    $sdb->busyTimeout(2000);
    if ($this->enc_key) {
      $sdb->exec(sprintf("PRAGMA key='%s'", $enc_key));
    }

    $stmt = $sdb->prepare('INSERT INTO queues VALUES (null, ?, ?, ?)');
    $stmt->bindValue(1, $this->queue_name, SQLITE3_TEXT);
    $stmt->bindValue(2, base64_encode($value), SQLITE3_TEXT);
    $stmt->bindValue(3, @date('Y-m-d H:i:s'), SQLITE3_BLOB);

    if (!$stmt->execute()) {
      throw new \ErrorException(sprintf('Could not save: [ %s ] on queue [ %s ] in [ %s ]', $value, $this->queue_name, $this->filename));
    }

    $sdb->busyTimeout(0);
    $sdb->close();

    return true;
  }

  /**
   * dequeue
   *
   * @param string $key
   */
  public function dequeue($limit = false)
  {
    $sdb = new \SQLite3($this->filename);
    $sdb->busyTimeout(2000);
    if ($this->enc_key) {
      $sdb->exec(sprintf("PRAGMA key='%s'", $enc_key));
    }

    if ($limit) {
      $stmt = $sdb->prepare('SELECT * FROM queues WHERE queue = ? LIMIT ?');
      $stmt->bindValue(1, $this->queue_name, SQLITE3_TEXT);
      $stmt->bindValue(2, $limit, SQLITE3_INTEGER);
    } else {
      $stmt = $sdb->prepare('SELECT * FROM queues WHERE queue = ?');
      $stmt->bindValue(1, $this->queue_name, SQLITE3_TEXT);
    }

    $rs = $stmt->execute();

    $rows = array();

    if ($rs) {
      while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
        $rows[$row['id']] = array('id' => $row['id'], 'queue' => $row['queue'], 'data' => $row['data'], 'cdate' => $row['cdate']);
      }
    }

    return $rows;
  }

  /**
   * delete element from queue
   *
   * @param string $value
   */
  public function delete($key)
  {
    $sdb = new \SQLite3($this->filename);
    $sdb->busyTimeout(2000);
    if ($this->enc_key) {
      $sdb->exec(sprintf("PRAGMA key='%s'", $enc_key));
    }

    $stmt = $sdb->prepare('DELETE FROM queues WHERE queue = ? AND id = ?');
    $stmt->bindValue(1, $this->queue_name, SQLITE3_TEXT);
    $stmt->bindValue(2, $key, SQLITE3_INTEGER);

    $sdb->busyTimeout(0);

    return (bool) $stmt->execute();
  }

  /**
   *
   * X execute/call custom methods
   *
   * @return queue object
   */
  public function X() {}

}
