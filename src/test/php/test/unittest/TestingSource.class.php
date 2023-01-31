<?php namespace test\unittest;

use test\execution\Group;
use test\source\Source;

class TestingSource extends Source {
  private $groups;

  public function __construct(Group... $groups) {
    $this->groups= $groups;
  }

  /** @return iterable */
  public function groups() { return $this->groups; }
}