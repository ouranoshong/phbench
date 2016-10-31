<?php
/**
 * Created by PhpStorm.
 * User: hong
 * Date: 16-10-31
 * Time: 下午10:14
 */
namespace PhBench;

/**
 * @return \PhBench\Benchmark
 */
function benchmark()
{
    static $bench;

    if (is_null($bench)) {
        $bench = new Benchmark();
    }

    return $bench;
}

function start_benchmark($identify, $temp = false)
{
    return benchmark()->start($identify, $temp);
}

function stop_benchmark($identify)
{
    return benchmark()->stop($identify);
}

function reset_benchmarks($identify = null)
{
    if (is_string($identify)) {
        return benchmark()->reset($identify);
    }

    $identifies = get_benchmark_identifies();

    if (count($identifies) <= 0) return false;

    if (is_array($identify) && count($identify) >= 1) {

        $retainIdentifies = array_diff(
            $identifies,
            array_intersect($identifies, $identify)
        );

        if (count($retainIdentifies) <= 0) return false;

        return benchmark()->resetAll($retainIdentifies);

    } else {

        return benchmark()->resetAll();
    }
}

function get_benchmark_identifies()
{
    return array_keys(benchmark()->getAll());
}

function get_elapsed_time($identify)
{
    return benchmark()->getElapsedTime($identify);
}
