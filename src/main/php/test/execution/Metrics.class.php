<?php namespace test\execution;

use test\Outcome;

class Metrics {
  public $count= ['success' => 0, 'failure' => 0, 'skipped' => 0];
  public $elapsed= 0.0;
  public $memoryUsed= 0;
  public $peakMemoryUsed= 0;

  public function record(Outcome $outcome): Outcome {
    $this->count[$outcome->kind()]++;
    $this->elapsed+= $outcome->elapsed;
    return $outcome;
  }

  public function using(int $usage, int $peakUsage): self {
    $this->memoryUsed= $usage;
    $this->peakMemoryUsed= $peakUsage;
    return $this;
  }

  /** Returns whether no tests were run */
  public function empty(): bool {
    return 0 === array_sum($this->count);
  }
}