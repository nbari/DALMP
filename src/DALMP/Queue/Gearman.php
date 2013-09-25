<?php
namespace DALMP\Queue;

/**
 * Gearman
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class Gearman implements QueueInterface {

  /**
   * GearmanClient instance
   *
   * @var GearmanClient
   */
  private $gmclient;

  /**
   * queue name
   *
   * @var string
   */
  private $queue_name;

  /**
   * Constructor
   *
   * @param string $filename
   * @param string $queue
   * @param string $enc_key
   */
  public function __construct($queue_name = 'dalmp_queue', $host = '127.0.0.1', $port = 4730) {
    $this->gmclient = new \GearmanClient();
    $this->gmclient->addServer($host, $port);
    $this->queue_name = $queue_name;
  }

  /**
   * enqueue
   *
   * @param string $value
   * @return boolean
   */
  public function enqueue($value) {
    if ($this->gmclient->ping('ping')) {
      $job_handle = $gmclient->doBackground($this->queue_name, json_encode($value), md5($value));
      return ($this->gmclient->returnCode() != GEARMAN_SUCCESS) ? false : true;
    } else {
      return false;
    }
  }

  /**
   * dequeue
   *
   * @param string $key
   */
  public function dequeue($limit = false) {
    $worker = new \GermanWorker();
    $worker->addServer($this->host, $this->port);
    $worker->addFunction($this->queue_name, ?????)
  }

  /**
   * delete element from queue
   *
   * @param string $value
   */
  public function delete($key) {
  }

  /**
   *
   * X execute/call custom methods
   *
   * @return queue object
   */
  public function X() {
    return $this->gmclient;
  }

}
