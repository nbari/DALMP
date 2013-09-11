<?php
namespace DALMP\Queue;

/**
 * QueueInterface
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
interface QueueInterface {

  /**
   * enqueue
   *
   * @param string $value
   * @return boolean
   */
  public function enqueue($value);

  /**
   * dequeue
   *
   * @param int $limit returns {$limit} entries on the queue
   * @return array
   */
  public function dequeue($limit = null);

  /**
   * X execute/call custom methods
   *
   * @return queue object
   */
  public function X();

}
