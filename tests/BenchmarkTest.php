<?php
/**
 * Created by PhpStorm.
 * User: hong
 * Date: 11/1/16
 * Time: 9:20 AM
 */

namespace Tests\PhBench;


use PhBench\Benchmark;
use PHPUnit\Framework\TestCase;

class BenchmarkTest extends TestCase
{
    /**
     * @var Benchmark
     */
    protected $bench;

    /**
     * @var string
     */
    protected $defaultIdentify = 'defaultBench';

    private $sleepingTime = 1;

    public function setUp()
    {
        if (!$this->bench) {
            $this->bench = new Benchmark();
        }
        $this->bench->start($this->defaultIdentify);
        sleep($this->sleepingTime);
        $this->bench->stop($this->defaultIdentify);
    }

    public function tearDown()
    {
        unset($this->bench);
    }

    public function testBenchmarkStartCorrectly()
    {
        $Bench =  new Benchmark();
        $targetFlag = $Bench->start('startOnce');
        $this->assertNotEmpty($targetFlag);
    }

    public function testBenchmarkStopCorrectly()
    {
        $identity = 'benchFlag';
        $Bench = new Benchmark();
        $Bench->start($identity);
        sleep(2);
        $elapsedTime = $Bench->stop($identity);
        $this->assertGreaterThan(2, $elapsedTime);
    }

    public function testGetBenchmarkCallCount()
    {
        $this->assertSame(1, $this->bench->getCallCount($this->defaultIdentify));
    }

    public function testGetElapsedTime()
    {
        $this->assertGreaterThan($this->sleepingTime, $this->bench->getElapsedTime($this->defaultIdentify));
    }

    public function testGetAll()
    {
        $benchmarks = $this->bench->getAll();
        $this->assertArrayHasKey($this->defaultIdentify, $benchmarks);
        $this->assertGreaterThan($this->defaultIdentify, $benchmarks[$this->defaultIdentify]);
    }

    /**
     * @depends testGetElapsedTime
     */
    public function testPrintAll()
    {
        ob_start();
        $this->bench->printAll(PHP_EOL);
        $result = ob_get_clean();
        $elapsedTime = $this->bench->getElapsedTime($this->defaultIdentify);

        $this->assertSame($this->defaultIdentify.': '.$elapsedTime.' sec'.PHP_EOL, $result);
    }

    public function testBenchmarkResetAfterStopBenchmark()
    {
        $identify = 'bench1';
        $bench = new Benchmark();
        $bench->start($identify);
        sleep($this->sleepingTime);
        $bench->stop($identify);
        $this->assertGreaterThan($this->sleepingTime, $bench->getElapsedTime($identify));
        $bench->reset($identify);
        $this->assertSame(0, $bench->getElapsedTime($identify));
    }

    public function testBenchmarkResetAllWithoutRetainBenchmarks()
    {
        $identify = 'bench1';
        $identify2 = 'bench2';
        $bench = new Benchmark();
        $bench->start($identify);
        $bench->start($identify2);

        sleep($this->sleepingTime);

        $bench->stop($identify);
        $bench->stop($identify2);

        $this->assertGreaterThan($this->sleepingTime, $bench->getElapsedTime($identify));
        $this->assertGreaterThan($this->sleepingTime, $bench->getElapsedTime($identify2));

        $bench->resetAll();
        $this->assertSame(0, $bench->getElapsedTime($identify));
        $this->assertSame(0, $bench->getElapsedTime($identify2));
    }

    public function testBenchMarkResetAllWithRetainBenchmarks()
    {
        $identify = 'bench1';
        $identify2 = 'bench2';
        $identify3 = 'bench3';
        $bench = new Benchmark();
        $bench->start($identify);
        $bench->start($identify2);
        $bench->start($identify3);

        sleep($this->sleepingTime);

        $bench->stop($identify);
        $bench->stop($identify2);
        $bench->stop($identify3);

        $this->assertGreaterThan($this->sleepingTime, $bench->getElapsedTime($identify));
        $this->assertGreaterThan($this->sleepingTime, $bench->getElapsedTime($identify2));
        $this->assertGreaterThan($this->sleepingTime, $bench->getElapsedTime($identify3));

        $identify3ElapsedTime = $bench->getElapsedTime($identify3);

        $bench->resetAll([$identify3]);
        $this->assertSame(0, $bench->getElapsedTime($identify));
        $this->assertSame(0, $bench->getElapsedTime($identify2));
        $this->assertSame($identify3ElapsedTime, $bench->getElapsedTime($identify3));
    }

    public function testBenchmarkStartAgain()
    {
        $bench = new Benchmark();
        $bench->start($this->defaultIdentify);
        sleep($this->sleepingTime);
        $elapsedTime1 = $bench->stop($this->defaultIdentify);

        $this->assertSame(1, $bench->getCallCount($this->defaultIdentify));
        $this->assertGreaterThan($this->sleepingTime, $elapsedTime1);

        $bench->start($this->defaultIdentify);
        sleep($this->sleepingTime);
        $elapsedTime2 = $bench->stop($this->defaultIdentify);

        $this->assertGreaterThan($this->sleepingTime, $elapsedTime2);
        $this->assertGreaterThan(
            $this->sleepingTime + $this->sleepingTime,
            $bench->getElapsedTime($this->defaultIdentify)
        );
        $this->assertSame(2, $bench->getCallCount($this->defaultIdentify));
    }

    public function testBenchmarkStartTemporary()
    {
        $bench = new Benchmark();
        $bench->start($this->defaultIdentify, true);
        sleep($this->sleepingTime);
        $elapsedTime1 = $bench->stop($this->defaultIdentify);

        $this->assertAttributeEquals([$this->defaultIdentify => true ], 'temporary' , $bench);
        $this->assertGreaterThan($this->sleepingTime, $elapsedTime1);

        $benchmarks = $bench->getAll();

        $this->assertNotContains($this->defaultIdentify, $benchmarks);

    }

    public function testBenchmarkStopWithNonExistsIdentify()
    {
        $this->assertGreaterThan($this->sleepingTime, $this->bench->getElapsedTime($this->defaultIdentify));
        $this->assertNull($this->bench->stop('no_exists'));
        $this->assertGreaterThan($this->sleepingTime, $this->bench->getElapsedTime($this->defaultIdentify));
    }

    public function testBenchmarkGetElapsedTimeWithNonExistsIdentify()
    {
        $this->assertSame(0, $this->bench->getElapsedTime('no_exists'));
    }

    public function testBenchmarkGetCallCountWithNonExistsIdentify()
    {
        $this->assertSame(0, $this->bench->getCallCount('no_exists'));
    }

}
