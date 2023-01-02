<?php namespace test\outcome;

use test\Outcome;

class Succeeded extends Outcome {

  public function kind() { return 'success'; }
}