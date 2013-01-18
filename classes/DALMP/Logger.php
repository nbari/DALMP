<?php

/**
 * DALMP_Logger - Logger
 *
 * git clone git://github.com/nbari/DALMP.git
 * @see http://dalmp.googlecode.com
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 2.1
 */

class DALMP_Logger {

  /**
   * write debug output to file
   *
   * @access private
   * @var boolean
   */
  private $log2file;

  /**
   * holds the time where debug begins
   *
   * @access private
   * @var mixed
   */
  private $time_start;

  /**
   * decimals used for printing the time
   *
   * @access private
   * @var int
   */
  private $decimals = 4;

  /**
   * Contents the log
   *
   * @access private
   * @var array
   */
  private $log = array();

  public function __construct($log2file = false) {
    $this->log2file = $log2file;
    $this->time_start = microtime(true);
  }

  public function log() {
    $args = func_get_args();
    $key = array_shift($args);
    $method = is_array(reset($args)) ? json_encode(array_shift($args)) : array_shift($args);
    $log = empty($args) ? (empty($method) ? "[$key]" : "[$key - $method]") : "[$key - $method] -> " . json_encode($args);
    $etime = number_format(microtime(true) - $this->time_start, $this->decimals);
    $this->log[][$etime] = $log;
  }

  public function getLog() {
    if ($this->log2file) {
      $debugFile = defined('DALMP_DEBUG_FILE') ? DALMP_DEBUG_FILE : DALMP_DIR . '/dalmp.log';
      if (!file_exists($debugFile)) {
        @mkdir(dirname($debugFile), 0700, true);
      }
      if ($this->log2file > 1) {
        $debugFile .= '-' . microtime(true);
      }
      $fh = fopen($debugFile, 'a+');
      $start = str_repeat('-', 80) . PHP_EOL;
      fwrite($fh, 'START ' . @date('c') . PHP_EOL);
      fwrite($fh, $start);
    } elseif (DALMP::isCli()) {
      echo str_repeat('-', 80) . PHP_EOL;
      $hr = null;
    } else {
      echo '<div style="margin: 10px; font-size: 12px; font-family: monospace,serif; text-align: left; border-color: #FF7C0A; background-color: #FFF; color: #000; border-style: solid; border-width: 1px;">';
      $hr = '<hr style="border-top: 0px; border-left: 0px; border-right: 0px: border-bottom: 1px dashed #006ED2">';
    }

    $indent = strlen(count($this->log));
    foreach ($this->log as $key => $logs) {
      $spaces = str_repeat(' ', $indent - strlen($key));
      foreach ($logs as $etime => $log) {
        if ($this->log2file) {
          fwrite($fh, "$spaces$key - $etime - " . stripslashes($log) . PHP_EOL);
        } else {
          echo "$hr$spaces$key - $etime - " . stripslashes($log) . DALMP::isCli(1);
        }
      }
    }

    if ($this->log2file) {
      fwrite($fh, $start);
      fwrite($fh, 'END ' . @date('c') . ' - [Memory usage: ' . memory_get_usage(true) . ', ' . memory_get_peak_usage(true) . ']' . PHP_EOL);
      fwrite($fh, $start);
      fclose($fh);
    } elseif (DALMP::isCli()) {
      echo str_repeat('-', 80) . PHP_EOL;
    } else {
      echo '</div>';
    }
  }

}
