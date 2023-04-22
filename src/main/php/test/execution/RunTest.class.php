<?php namespace test\execution;

use lang\Runnable;
use test\Outcome;
use util\profiling\Timer;

class RunTest implements Runnable {
  public $case, $arguments;

  /**
   * Runs a given test case with the supplied arguments
   *
   * @param  TestCase $case
   * @param  array<mixed> $arguments
   */
  public function __construct($case, $arguments) {
    $this->case= $case;
    $this->arguments= $arguments;
  }

  /** @return string */
  public function name() { return $this->case->name(); }

  /**
   * Runs this test case and returns its outcome. If given a timer, the
   * outcome will have the elapsed time attached to it.
   */
  public function run(Timer $timer= null): Outcome {
    if (null === $timer) return $this->case->run($this->arguments);

    $timer->start();
    $outcome= $this->case->run($this->arguments);
    $timer->stop();

    return $outcome->timed($timer->elapsedTime());
  }
}