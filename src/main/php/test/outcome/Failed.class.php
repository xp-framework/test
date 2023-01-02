<?php namespace test\outcome;

use lang\Throwable;
use test\Outcome;

class Failed extends Outcome {
  public $cause;

  public function __construct($test, Throwable $cause) {
    parent::__construct($test);
    $this->cause= $cause;
  }

  public function kind() { return 'failure'; }
}