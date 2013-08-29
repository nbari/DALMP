<?php

/**
 * MPLT - Measure Page Load Time
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @license BSD License
 */
class MPLT {

  private $decimals;
  private $time_start;
  private $time_end;
  private $marks = array();

  function __construct($decimals = 3) {
    $this->time_start = microtime(true);
    $this->decimals = $decimals;
  }

  public function Stop() {
    $this->time_end = microtime(true);
  }

  public function setMark($name = null) {
    $mark = number_format(microtime(true) - $this->time_start, $this->decimals);
    if ($name && $name != 'total') {
      $this->marks[$name] = $mark;
    } else {
      $this->marks[] = $mark;
    }
  }

  public function getMark($name = null) {
    return $name ? $this->marks[$name] : reset($this->marks);
  }

  public function getMarks() {
    return $this->marks;
  }

  public function getPageLoadTime($marks = false) {
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

  public function getMemoryUsage($convert = false) {
    return $convert ? $this->convert(memory_get_usage(true)) : memory_get_usage(true);
  }

  public function convert($size) {
    $unit=array('B','KB','MB','GB','TB','PB');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
  }

}
