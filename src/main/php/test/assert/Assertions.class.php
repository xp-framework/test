<?php namespace test\assert;

use lang\Type;
use test\AssertionFailed;

class Assertions {
  public static $TRUE, $FALSE, $NULL;
  private $value;

  static function __static() {

    // Speed up special cases
    self::$TRUE= new Identical(true);
    self::$FALSE= new Identical(false);
    self::$NULL= new Identical(null);
  }

  /** @param mixed $value */
  public function __construct($value) {
    $this->value= $value;
  }

  public function is(Condition $condition): self {
    if (!$condition->matches($this->value)) {
      throw new AssertionFailed($condition->describe($this->value, true));
    }
    return $this;
  }

  public function isNot(Condition $condition): self {
    if ($condition->matches($this->value)) {
      throw new AssertionFailed($condition->describe($this->value, false));
    }
    return $this;
  }

  /**
   * Test for equality
   * 
   * @param  mixed $expected
   * @return self
   */
  public function isEqualTo($expected) {
    return $this->is(new Equals($expected));
  }

  /**
   * Assert a given value is not equal to this value
   * 
   * @param  mixed $expected
   * @return self
   */
  public function isNotEqualTo($expected) {
    return $this->isNot(new Equals($expected));
  }

  /**
   * Assert a this value is null
   * 
   * @return self
   */
  public function isNull() {
    return $this->is(self::$NULL);
  }

  /**
   * Assert a this value is true
   * 
   * @return self
   */
  public function isTrue() {
    return $this->is(self::$TRUE);
  }

  /**
   * Assert a this value is false
   * 
   * @return self
   */
  public function isFalse() {
    return $this->is(self::$FALSE);
  }

  /**
   * Assert a given value is an instance of a given type
   *
   * @param  string|Type $type
   * @return self
   */
  public function isInstanceOf($type) {
    return $this->is(new Instance($type));
  }
}