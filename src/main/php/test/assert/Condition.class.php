<?php namespace test\assert;

use util\Objects;

abstract class Condition {

  /**
   * Test whether this condition matches a given value
   *
   * @param  var $value
   * @return bool
   */
  public abstract function matches($value);

  /**
   * Creates a string representation of any given value.
   *
   * @param  var $value
   * @return string
   */
  public static function stringOf($value) {
    return null === $value ? 'null' : Objects::stringOf($value);
  }

  /**
   * Describe this condition using a given value
   *
   * @param  var $value
   * @param  bool $positive Whether to use positive ("matches") or negative ("does not match")
   * @return string
   */
  public function describe($value, $positive) {
    return self::stringOf($value).' '.($positive ? 'matches' : 'does not match');
  }
}