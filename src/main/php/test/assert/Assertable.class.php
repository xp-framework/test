<?php namespace test\assert;

use Traversable;
use lang\Type;
use test\AssertionFailed;

/** @test test.unittest.AssertableTest */
class Assertable {
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
   * Map the value encapsulated in this fluent interface using a mapping
   * function. Works for scalars as well as arrays and any traversable
   * data structure. The given function recieves the value and returns
   * the mapped value as a new `Assertable`.
   */
  public function map(callable $mapper): self {
    if (is_array($this->value) || $this->value instanceof Traversable) {
      $self= new self([]);
      foreach ($this->value as $key => $element) {
        $self->value[$key]= $mapper($element);
      }
      return $self;
    } else {
      return new self($mapper($this->value));
    }
  }

  /**
   * Assert this value is equal to the given expected value
   * 
   * @param  mixed $expected
   * @return self
   */
  public function isEqualTo($expected) {
    return $this->is(new Equals($expected));
  }

  /**
   * Assert this value is not equal to the given expected value
   * 
   * @param  mixed $expected
   * @return self
   */
  public function isNotEqualTo($expected) {
    return $this->isNot(new Equals($expected));
  }

  /**
   * Assert this value is null
   * 
   * @return self
   */
  public function isNull() {
    return $this->is(self::$NULL);
  }

  /**
   * Assert this value is true
   * 
   * @return self
   */
  public function isTrue() {
    return $this->is(self::$TRUE);
  }

  /**
   * Assert this value is false
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