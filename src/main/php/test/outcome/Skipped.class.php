<?php namespace test\outcome;

use lang\Throwable;
use test\Outcome;

class Skipped extends Outcome {
  public $reason;

  public function __construct($test, $reason) {
    parent::__construct($test);
    $this->reason= $reason;
  }

  public function kind() { return 'skipped'; }
}