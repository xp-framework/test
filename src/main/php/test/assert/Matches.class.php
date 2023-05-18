<?php namespace test\assert;

use lang\FormatException;

/** @test test.unittest.MatchesTest */
class Matches extends Condition {
  protected $pattern;

  public function __construct(string $pattern) {
    $this->pattern= $pattern;
  }

  public function matches($value) {
    if (is_string($value) || is_object($value) && method_exists($value, '__toString')) {
      if (false === ($r= preg_match($this->pattern, $value))) {
        throw new FormatException('Using '.$this->pattern);
      }
      return $r > 0;
    }
    return false;
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