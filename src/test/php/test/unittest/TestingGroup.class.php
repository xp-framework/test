<?php namespace test\unittest;

use test\execution\Group;

class TestingGroup extends Group {
  private $name, $tests;

  public function __construct(string $name, array $tests= []) {
    $this->name= $name;
    $this->tests= $tests;
  }

  /** @return string */
  public function name() { return $this->name; }

  /** @return iterable */
  public function tests($arguments= []) { return $this->tests; }
}