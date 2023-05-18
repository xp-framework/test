<?php namespace test;

use lang\Type;
use test\assert\{Assertable, Equals, Matches, Instance};

abstract class Assert {

  /**
   * Assertion DSL
   *
   * @param  mixed $value
   * @return Assertable
   */
  public static function that($value) {
    return new Assertable($value);
  }

  /**
   * Equals shorthand
   * 
   * @param  mixed $expected
   * @param  mixed $actual
   * @return void
   */
  public static function equals($expected, $actual) {
    (new Assertable($actual))->is(new Equals($expected));
  }

  /**
   * Not equals shorthand
   * 
   * @param  mixed $expected
   * @param  mixed $actual
   * @return void
   */
  public static function notEquals($expected, $actual) {
    (new Assertable($actual))->isNot(new Equals($expected));
  }

  /**
   * Equal to `true` shorthand
   * 
   * @param  mixed $actual
   * @return void
   */
  public static function true($actual) {
    (new Assertable($actual))->is(Assertable::$TRUE);
  }

  /**
   * Equal to `false` shorthand
   * 
   * @param  mixed $actual
   * @return void
   */
  public static function false($actual) {
    (new Assertable($actual))->is(Assertable::$FALSE);
  }

  /**
   * Equal to `null` shorthand
   * 
   * @param  mixed $actual
   * @return void
   */
  public static function null($actual) {
    (new Assertable($actual))->is(Assertable::$NULL);
  }

  /**
   * Instance shorthand
   * 
   * @param  string|Type $expected
   * @param  mixed $actual
   * @return void
   */
  public static function instance($expected, $actual) {
    (new Assertable($actual))->is(new Instance($expected));
  }

  /**
   * Matches shorthand
   *
   * @param  string $pattern
   * @param  mixed $actual
   * @return void
   */
  public static function matches($pattern, $actual) {
    (new Assertable($actual))->is(new Matches($pattern));
  }

  /**
   * Throws shorthand
   *
   * @param  string|Type $expected
   * @param  callable $actual
   * @return void
   */
  public static function throws($expected, $actual) {
    (new Assertable($actual))->throws($expected);
  }
}