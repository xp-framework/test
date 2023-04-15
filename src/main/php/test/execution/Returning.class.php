<?php namespace test\execution;

use lang\Runnable;
use test\Outcome;

class Returning implements Runnable {
  public $case, $outcome;

  /**
   * Creates an instance which returns the specified outcome.
   *
   * @param TestCase $case
   * @param Outcome $outcome
   */
  public function __construct($case, $outcome) {
    $this->case= $case;
    $this->outcome= $outcome;
  }

  /** @return iterable */
  public function targets() { return [$this]; }

  /** Runs this instance */
  public function run(): Outcome { return $this->outcome; }
}
