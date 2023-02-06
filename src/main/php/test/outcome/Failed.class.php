<?php namespace test\outcome;

use lang\Throwable;
use test\Outcome;

class Failed extends Outcome {
  public $reason, $cause;

  public function __construct($test, $reason, Throwable $cause= null) {
    parent::__construct($test);
    $this->reason= $reason;
    $this->cause= $cause;
  }

  public function kind() { return 'failure'; }

  /** @return string */
  public function trace($indent= '') {
    if (null === $this->cause) return "{$indent}No exception raised";

    $s= '';
    foreach ($this->cause->getStackTrace() as $trace) {
      $s.= $indent.ltrim($trace->toString());
    }
    return rtrim($s);
  }
}