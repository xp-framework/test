<?php namespace test\assert;

class Matches extends Condition {
  protected $pattern;

  public function __construct($pattern) {
    $this->pattern= $pattern;
  }

  public function matches($value) {
    return preg_match($this->pattern, $value);
  }

  public function describe($value, $positive) {
    return sprintf(
      '%s %s %s', 
      self::stringOf($value),
      $positive ? 'matches' : 'does not match',
      $this->pattern
    );
  }
}