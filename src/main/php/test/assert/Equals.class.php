<?php namespace test\assert;

use util\Objects;

class Equals extends Condition {
  protected $value;

  public function __construct($value) {
    $this->value= $value;
  }

  public function matches($value) {
    return Objects::equal($this->value, $value);
  }

  public function describe($value, $positive) {
    return sprintf(
      '%s %s %s', 
      self::stringOf($value),
      $positive ? 'is equal to' : 'is not equal to',
      self::stringOf($this->value)
    );
  }
}