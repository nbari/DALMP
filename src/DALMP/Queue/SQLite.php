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
class SQLite implements QueueInterface {

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
  public function __construct($filename, $queue_name = 'default', $enc_key = null) {

    $sdb = new \SQLite3($filename);
    $sdb->busyTimeout(2000);

    $this->filename = $filename;

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
  public function enqueue($value) {
    $sdb = new \SQLite3($this->filename);
    $sdb->busyTimeout(2000);
    if ($this->enc_key) {
      $this->sdb->exec(sprintf("PRAGMA key='%s'", $enc_key));
    }

    $sql = sprintf("INSERT INTO queues VALUES (null, '%s', '%s', '%s')", $this->queue_name, base64_encode($value), @date('Y-m-d H:i:s'));

    if ($this->sdb->exec($sql)) {
      throw new \ErrorException("could not save {$value} - {$this->queue_name} on {$this->filename}");
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
  public function dequeue($limit = null) {
    $sdb = new \SQLite3($this->filename);
    $sdb->busyTimeout(2000);
    if ($this->enc_key) {
      $this->sdb->exec(sprintf("PRAGMA key='%s'", $enc_key));
    }

    $sql = sprintf("SELECT * FROM queues WHERE queue='%s'", $this->queue_name);

    $rs = $sdb->query($sql);

    if ($rs) {
      if ($print) {
        while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
          echo $row['id'] , '|' , $row['queue'] , '|' , base64_decode($row['data']) , '|' , $row['cdate'] , $this->isCli(1);
        }
      } else {
        return $rs;
      }
    } else {
      return array();
    }
  }

  public function delete($value) {
  }

  /**
   *
   * X execute/call custom methods
   *
   * @return queue object
   */
  public function X() {}

}
