<?php namespace test\assert;

class Identical extends Condition {
  protected $value;

  public function __construct($value) {
    $this->value= $value;
  }

  public function matches($value) {
    return $this->value === $value;
  }

  public function describe($value, $positive) {
    return sprintf(
      '%s %s %s', 
      self::stringOf($value),
      $positive ? 'is' : 'is not',
      self::stringOf($this->value)
    );
  }
}
