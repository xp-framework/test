<?php namespace test\execution;

use lang\Runnable;
use test\Outcome;

class RunTest implements Runnable {
  private $case, $arguments;

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

  /** Runs this test case and returns its outcome */
  public function run(): Outcome {
    return $this->case->run($this->arguments);
  }
}