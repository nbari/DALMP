<?php

/**
 * MPLT - Measure Page Load Time
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @license BSD License
 */
class MPLT
{
    private $decimals;
    private $time_start;
    private $time_end;
    private $marks = array();

    /**
     * constructor
     *
     * @param int $decimals
     */
    public function __construct($decimals = 4)
    {
        $this->time_start = microtime(true);
        $this->decimals = $decimals;
    }

    /**
     * stop
     */
    public function Stop()
    {
        $this->time_end = microtime(true);
    }

    /**
     * setMark
     *
     * @param string $name
     */
    public function setMark($name = null)
    {
        $mark = microtime(true) - $this->time_start;
        $last_mark = end($this->marks)[0];
        $diff = $mark - $last_mark;
        $mark = number_format($mark, $this->decimals);
        $diff = number_format($diff, $this->decimals);
        if ($name && $name != 'total') {
            $this->marks[$name] = array($mark, $diff);
        } else {
            $this->marks[] = array($mark, $diff);
        }
    }

    /**
     * getMark
     *
     * @param  string $name
     * @return array
     */
    public function getMark($name = null)
    {
        return $name ? $this->marks[$name] : reset($this->marks);
    }

    /**
     * getMarks
     *
     * @return array
     */
    public function getMarks()
    {
        return $this->marks;
    }

    /**
     * printMarks
     */
    public function printMarks()
    {
        if ($this->marks) {
            $pad = $this->decimals * 2;
            $max_length = max(array_map('strlen', array_keys($this->marks)));

            if ($pad < $max_length) {
                $pad = $max_length + 2;
            }

            echo str_pad('mark', $pad), str_pad('time', $pad), 'elapsed-time', $this->isCli(1);
            foreach ($this->marks as $mark => $values) {
                echo str_pad($mark, $pad), str_pad($values[0], $pad), $values[1], $this->isCli(1);
            }
        } else {
            echo 'no defined marks', $this->isCli(1);
        }
    }

    /**
     * getPageLoadTime
     *
     * @param bool $marks
     */
    public function getPageLoadTime($marks = false)
    {
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

    /**
     * getMemoryUsage
     *
     * @param bool $convert "Human-readable" output.
     */
    public function getMemoryUsage($convert = false)
    {
        return $convert ? $this->convert(memory_get_usage(true)) : memory_get_usage(true);
    }

    /**
     * convert
     *
     * @param int $size
     */
    public function convert($size)
    {
        $unit=array('B','KB','MB','GB','TB','PB');

        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * isCli()
     *
     * @param  boolean $eol
     * @return boolean or PHP_EOL, <br/>
     */
    public function isCli($eol = null)
    {
        ($cli = (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']))) && $cli = $eol ? PHP_EOL : true;

        return $cli ?: ($eol ? '<br/>' : false);
    }

}
