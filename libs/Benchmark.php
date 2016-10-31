<?php
/**
 * Created by PhpStorm.
 * User: hong
 * Date: 16-10-31
 * Time: 下午10:15
 */

namespace PhBench;


class Benchmark
{
    /**
     * @var array
     */
    protected  $results = [];
    /**
     * @var array
     */
    protected  $startTimes = [];
    /**
     * @var array
     */
    protected  $startCount = [];
    /**
     * @var array
     */
    protected  $temporary = [];


    /**
     * @param      $identifier
     * @param bool $temporary_benchmark
     */
    public function start($identifier, $temporary_benchmark = false)
    {
        $this->startTimes[$identifier] = $this->getMicroTime();

        if (isset($this->startCount[$identifier])) {
            $this->startCount[$identifier] = $this->startCount[$identifier] + 1;
        } else {
            $this->startCount[$identifier] = 1;
        }

        if ($temporary_benchmark == true) {
            $this->temporary[$identifier] = true;
        }
    }

    /**
     * @param $identifier
     *
     * @return mixed
     */
    public function getCallCount($identifier)
    {
        return $this->startCount[$identifier];
    }

    /**
     * @param $identifier
     *
     * @return float|null
     */
    public function stop($identifier)
    {
        if (isset($this->startTimes[$identifier])) {
            $elapsed_time = $this->getMicroTime() - $this->startTimes[$identifier];

            if (isset($this->results[$identifier])) {
                $this->results[$identifier] += $elapsed_time;
            } else {
                $this->results[$identifier] = $elapsed_time;
            }

            return $elapsed_time;
        }

        return null;
    }

    /**
     * @param $identifier
     *
     * @return int|mixed
     */
    public function getElapsedTime($identifier)
    {
        if (isset($this->results[$identifier])) {
            return $this->results[$identifier];
        }

        return 0;
    }

    /**
     * @param $identifier
     */
    public function reset($identifier)
    {
        if (isset($this->results[$identifier])) {
            $this->results[$identifier] = 0;
        }
    }

    /**
     * @param array $retainBenchmarks
     *
*@return mixed
     */
    public function resetAll($retainBenchmarks = array())
    {
        // If no benchmarks should be retained
        if (count($retainBenchmarks) == 0) {
            $this->results = array();
            return true;
        }

        // Else reset all benchmarks BUT the retain_benachmarks
        @reset($this->results);
        while (list($identifier) = @each($this->results)) {
            if (!in_array($identifier, $retainBenchmarks)) {
                $this->results[$identifier] = 0;
            }
        }

        return true;
    }

    /**
     * @param string $linebreak
     */
    public function printAll($linebreak = '<br />')
    {
        @reset($this->results);
        while (list($identifier, $elapsed_time) = @each($this->results)) {
            if (!isset($this->temporary[$identifier])) {
                echo $identifier.': '.$elapsed_time.' sec'.$linebreak;
            }
        }
    }

    /**
     * Returns all registered benchmark-results.
     * @return array associative Array. The keys are the benchmark-identifiers, the values the benchmark-times.
     */
    public function getAll()
    {
        $benchmarks = array();

        @reset($this->results);
        while (list($identifier, $elapsed_time) = @each($this->results)) {
            if (!isset($this->temporary[$identifier])) {
                $benchmarks[$identifier] = $elapsed_time;
            }
        }

        return $benchmarks;
    }

    /**
     * Returns the current time in seconds and milliseconds.
     *
     * @return float
     */
    protected function getMicroTime()
    {
        return microtime(true);
    }

    /**
     * @return array
     */
    public function getTemporary()
    {
        return $this->temporary;
    }
}
