<?php namespace test;

use lang\Type;
use test\assert\{Assertions, Equals, Instance};

abstract class Assert {

  /**
   * Assertion DSL
   *
   * @param  mixed $value
   * @return test.assert.Assertions
   */
  public static function that($value) {
    return new Assertions($value);
  }

  /**
   * Equals shorthand
   * 
   * @param  mixed $expected
   * @param  mixed $actual
   * @return void
   */
  public static function equals($expected, $actual) {
    (new Assertions($actual))->is(new Equals($expected));
  }

  /**
   * Not equals shorthand
   * 
   * @param  mixed $expected
   * @param  mixed $actual
   * @return void
   */
  public static function notEquals($expected, $actual) {
    (new Assertions($actual))->isNot(new Equals($expected));
  }

  /**
   * Equal to `true` shorthand
   * 
   * @param  mixed $actual
   * @return void
   */
  public static function true($actual) {
    (new Assertions($actual))->is(Assertions::$TRUE);
  }

  /**
   * Equal to `false` shorthand
   * 
   * @param  mixed $actual
   * @return void
   */
  public static function false($actual) {
    (new Assertions($actual))->is(Assertions::$FALSE);
  }

  /**
   * Equal to `null` shorthand
   * 
   * @param  mixed $actual
   * @return void
   */
  public static function null($actual) {
    (new Assertions($actual))->is(Assertions::$NULL);
  }

  /**
   * Instance shorthand
   * 
   * @param  string|Type $expected
   * @param  mixed $actual
   * @return void
   */
  public static function instance($expected, $actual) {
    (new Assertions($actual))->is(new Instance($expected));
  }
}