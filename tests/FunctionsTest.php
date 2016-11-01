<?php
/**
 * Created by PhpStorm.
 * User: hong
 * Date: 11/1/16
 * Time: 11:38 AM
 */

namespace Tests\PhBench;


use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    protected $defaultIdentify = 'start';
    protected $sleepingTime = 1;

    public function setUp()
    {
        \PhBench\start_benchmark($this->defaultIdentify);

        sleep($this->sleepingTime);

        \PhBench\stop_benchmark($this->defaultIdentify);
    }

    public function testIsSameBenchmark()
    {
        $bench = \PhBench\benchmark();
        $bench1 = \PhBench\benchmark();

        $this->assertSame($bench, $bench1);
    }

    public function testGetElapsedTime()
    {
        $this->assertGreaterThan($this->sleepingTime, \PhBench\get_elapsed_time($this->defaultIdentify));
    }

    public function testGetCallCount()
    {
        $this->assertGreaterThanOrEqual(1, \PhBench\get_call_count($this->defaultIdentify));
    }

    public function testGetIdentifyList()
    {
        $this->assertContains($this->defaultIdentify, \PhBench\get_benchmark_identifies());
    }

    public function testStartThenStopImmediately()
    {
        $identify = 'startOnce';

        \PhBench\start_benchmark($identify);
        \PhBench\stop_benchmark($identify);

        $this->assertContains($identify, \PhBench\get_benchmark_identifies());
    }


    public function testStartAndStopWithSleepAFewSeconds()
    {
        $identify = 'runAFewTime';

        \PhBench\start_benchmark($identify);
        \PhBench\stop_benchmark($identify);

        $this->assertContains($identify, \PhBench\get_benchmark_identifies());
    }

    public function testResetOneBenchmark()
    {
        $this->assertGreaterThan(0, \PhBench\get_elapsed_time($this->defaultIdentify));
        \PhBench\reset_benchmarks($this->defaultIdentify);
        $this->assertSame(0, \PhBench\get_elapsed_time($this->defaultIdentify));
    }

    public function testResetMultiBenchmarks()
    {
        $identifyOne = 'start_one';
        $identifyTwo = 'start_two';
        $identifyThree = 'start_three';

        \PhBench\start_benchmark($identifyOne);
        \PhBench\start_benchmark($identifyTwo);
        \PhBench\start_benchmark($identifyThree);
        sleep($this->sleepingTime);
        \PhBench\stop_benchmark($identifyOne);
        \PhBench\stop_benchmark($identifyTwo);
        \PhBench\stop_benchmark($identifyThree);

        $this->assertGreaterThanOrEqual($this->sleepingTime, \PhBench\get_elapsed_time($identifyOne));
        $this->assertGreaterThanOrEqual($this->sleepingTime, \PhBench\get_elapsed_time($identifyTwo));
        $this->assertGreaterThanOrEqual($this->sleepingTime, \PhBench\get_elapsed_time($identifyThree));

        \PhBench\reset_benchmarks([$identifyOne, $identifyTwo]);

        $this->assertEquals(0, \PhBench\get_elapsed_time($identifyOne));
        $this->assertEquals(0, \PhBench\get_elapsed_time($identifyTwo));
        $this->assertGreaterThanOrEqual($this->sleepingTime, \PhBench\get_elapsed_time($identifyThree));
    }

    public function testResetAllBenchmarks()
    {
        $identifyOne = 'start_one';
        $identifyTwo = 'start_two';
        $identifyThree = 'start_three';

        \PhBench\start_benchmark($identifyOne);
        \PhBench\start_benchmark($identifyTwo);
        \PhBench\start_benchmark($identifyThree);
        sleep($this->sleepingTime);
        \PhBench\stop_benchmark($identifyOne);
        \PhBench\stop_benchmark($identifyTwo);
        \PhBench\stop_benchmark($identifyThree);

        $this->assertGreaterThanOrEqual($this->sleepingTime, \PhBench\get_elapsed_time($identifyOne));
        $this->assertGreaterThanOrEqual($this->sleepingTime, \PhBench\get_elapsed_time($identifyTwo));
        $this->assertGreaterThanOrEqual($this->sleepingTime, \PhBench\get_elapsed_time($identifyThree));

        \PhBench\reset_benchmarks();

        $this->assertEquals(0, \PhBench\get_elapsed_time($identifyOne));
        $this->assertEquals(0, \PhBench\get_elapsed_time($identifyTwo));
        $this->assertEquals(0, \PhBench\get_elapsed_time($identifyThree));
    }

}
