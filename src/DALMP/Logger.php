<?php
namespace DALMP;

/**
 * Logger
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class Logger
{
  /**
   * write log to file
   *
   * @access private
   * @var boolean
   */
  private $log2file = false;

  /**
   * file to write log
   *
   * @access private
   * @var mixed
   */
  private $logfile;

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
  private $decimals = 5;

  /**
   * Contents the log
   *
   * @access private
   * @var array
   */
  private $log = array();

  /**
   * is_cli
   *
   * @access private
   * @var bool
   */
  private $is_cli = false;

  /**
   * constructor
   *
   * @param int $log2file if > 1 will create separate log files
   * @param string $logfile
   */
  public function __construct($log2file = false, $logfile = false)
  {
    if ($log2file) {
      if ($logfile) {
        if (!is_writable($logfile)) {
          if (!is_dir(dirname($logfile)) && !mkdir(dirname($logfile), 0700, true)) {
            throw new \Exception("Can't create log directory for: {$logfile}");
          }
        }
      }
      $this->log2file = (int) $log2file;
      $this->logfile = $logfile;
    }

    if (php_sapi_name() === 'cli') {
      $this->is_cli = true;
    }

    $this->time_start = microtime(true);
  }

  public function log()
  {
    $args = func_get_args();
    $key = array_shift($args);
    if (is_object($key)) {
      $key = json_encode(array(get_class($key), get_object_vars($key), get_class_methods($key)));
    }
    $method = is_array(reset($args)) ? json_encode(array_shift($args)) : array_shift($args);
    if (is_object($method)) {
      $method = json_encode(array(get_class($method), get_object_vars($method), get_class_methods($method)));
    }
    $log = empty($args) ? (empty($method) ? "[$key]" : "[$key - $method]") : "[$key - $method] -> " . json_encode($args);
    $etime = number_format(microtime(true) - $this->time_start, $this->decimals);
    $this->log[][$etime] = $log;
  }

  public function getLog()
  {
    if ($this->log2file) {
      if ($this->log2file > 1) {
        $this->logfile .= '-' . microtime(true);
      }
      $fh = fopen($this->logfile, 'a+');
      $start = str_repeat('-', 80) . PHP_EOL;
      if ($this->log2file > 1) {
        fwrite($fh, $start);
      }
      fwrite($fh, 'START ' . @date('c') . PHP_EOL);
      fwrite($fh, $start);
    } elseif ($this->is_cli) {
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
          echo "$hr$spaces$key - $etime - " . stripslashes($log) . ($this->is_cli ? PHP_EOL : '<br/>');
        }
      }
    }

    if ($this->log2file) {
      fwrite($fh, $start);
      fwrite($fh, 'END ' . @date('c') . ' - [Memory usage: ' . memory_get_usage(true) . ', ' . memory_get_peak_usage(true) . ']' . PHP_EOL);
      fwrite($fh, $start);
      fclose($fh);
    } elseif ($this->is_cli) {
      echo str_repeat('-', 80) . PHP_EOL;
    } else {
      echo '</div>';
    }
  }

}
