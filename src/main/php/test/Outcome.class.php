<?php namespace test;

abstract class Outcome {
  public $test;
  public $elapsed= null;

  public function __construct($test) {
    $this->test= $test;
  }

  /** Sets elapsed time to the given value */
  public function timed(float $elapsed): self {
    $this->elapsed= $elapsed;
    return $this;
  }

  public abstract function kind();
}