<?php namespace test;

class Metrics {
  public $count= ['success' => 0, 'failure' => 0, 'skipped' => 0];
  public $elapsed= 0.0;

  public function record(Outcome $outcome, float $elapsed): Outcome {
    $this->count[$outcome->kind()]++;
    $this->elapsed+= $elapsed;
    return $outcome;
  }
}