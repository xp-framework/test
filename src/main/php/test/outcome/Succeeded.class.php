<?php namespace test\outcome;

use test\Outcome;

class Succeeded extends Outcome {

  /** @return string */
  public function kind() { return 'success'; }
}