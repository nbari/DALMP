<?php
/**
 * Measure Page Load Time
 */

class mplt {
  private $decimals = 3;
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

  public function setMark($name=null) {
    $mark = number_format(microtime(true) - $this->time_start, $this->decimals);
	if($name) {
	  $this->marks[$name] = $mark;
	} else {
		$this->marks[] = $mark;
	}
  }

  public function getMark($name=null) {
    return $name ? $this->marks[$name] : reset($this->marks);
  }

  public function getMarks() {
	return $this->marks;
  }

  public function getPageLoadTime($marks=false) {
	if (empty($this->time_end)) {
	  $this->Stop();
	}
	$lt = number_format($this->time_end - $this->time_start, $this->decimals);
	if($marks){
	  $this->marks['total'] = $lt;
	  return $this->marks;
	} else {
	  return $lt;
	}
  }

}
?>