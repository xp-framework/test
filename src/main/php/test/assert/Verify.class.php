<?php namespace test\assert;

class Verify extends Condition {
  private $description;

  public function __construct(?string $description= null) {
    $this->description= $description;
  }

  public function matches($value) {
    return (bool)$value;
  }

  public function describe($value, $positive) {
    return sprintf(
      '%s %s',
      $positive ? 'Verified' : 'Failed verifying',
      $this->description ?? self::stringOf($value)
    );
  }
}