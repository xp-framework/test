<?php namespace test\execution;

class Once {
  public $case;

  /** @param TestCase $case */
  public function __construct($case) { $this->case= $case; }

  /** @return iterable */
  public function targets() { return [new RunTest($this->case, [])]; }
}
