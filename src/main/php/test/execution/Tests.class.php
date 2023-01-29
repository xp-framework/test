<?php namespace test\execution;

class Tests {
  private $tests;

  public function __construct(... $tests) {
    $this->tests= $tests;
  }

  public function add($test) {
    $this->tests[]= $test;
    return $this;
  }

  /** @return iterable */
  public function groups() {
    foreach ($this->tests as $test) {
      yield from $test->groups();
    }
  }
}