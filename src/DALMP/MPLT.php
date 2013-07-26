<?php
namespace DALMP;

/**
 * MPLT - Measure Page Load Time
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class MPLT {

  private $decimals;
  private $time_start;
  private $time_end;
  private $marks = array();

  function __construct($decimals = 3) {
    $this->time_start = microtime(True);
    $this->decimals = $decimals;
  }

  public function Stop() {
    $this->time_end = microtime(true);
  }

  public function setMark($name = NULL) {
    $mark = number_format(microtime(True) - $this->time_start, $this->decimals);
    if ($name && $name != 'total') {
      $this->marks[$name] = $mark;
    } else {
      $this->marks[] = $mark;
    }
  }

  public function getMark($name = NULL) {
    return $name ? $this->marks[$name] : reset($this->marks);
  }

  public function getMarks() {
    return $this->marks;
  }

  public function getPageLoadTime($marks = False) {
    if (empty($this->time_end)) {
      $this->Stop();
    }

    $lt = number_format($this->time_end - $this->time_start, $this->decimals);

    if ($marks) {
      $this->marks['total'] = $lt;
      return $this->marks;
    } else {
      return $lt;
    }
  }

  public function getMemoryUsage($convert = False) {
    return $convert ? $this->convert(memory_get_usage(True)) : memory_get_usage(True);
  }

  public function convert($size) {
    $unit=array('B','KB','MB','GB','TB','PB');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
  }

}
