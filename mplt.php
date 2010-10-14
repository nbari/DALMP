<?php
/**
 * Measure Page Load Time
 */

class mplt {
	private $time_start;
	private $time_end;

	function __construct() {
		$this->time_start = microtime(true);
	}

	function Stop() {
		$this->time_end = microtime(true);
	}

	function getPageLoadTime($decimals = 3) {
		if (empty($this->time_end)) {
			$this->Stop();
		}
		return number_format($this->time_end - $this->time_start, $decimals);
	}
}
?>