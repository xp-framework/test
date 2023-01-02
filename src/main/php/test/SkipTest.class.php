<?php namespace test;

use lang\Runnable;
use test\outcome\Skipped;

class SkipTest implements Runnable {
  private $outcome;

  public function __construct(string $name, string $reason) {
    $this->outcome= new Skipped($name, $reason);
  }

  public function run(): Outcome {
    return $this->outcome;
  }
}