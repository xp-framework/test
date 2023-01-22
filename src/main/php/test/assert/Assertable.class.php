<?php namespace test\assert;

use Closure, Throwable, Traversable, ReflectionFunction, ReflectionMethod;
use lang\{Type, IllegalArgumentException};
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
   *
   * @param  array|string|function(mixed): mixed|function(mixed, int|string): mixed $mapper
   */
  public function mappedBy($mapper): self {
    try {
      if (is_array($mapper)) {
        $r= new ReflectionMethod(...$mapper);
        $mapper= $r->getClosure($mapper[0]);
      } else {
        $r= new ReflectionFunction($mapper);
        $mapper instanceof Closure || $mapper= $r->getClosure();
      }

      // Do not pass keys to callables with only one required parameter.
      // This enables to use map with functions such as trim(), which
      // would otherwise choke on the array keys.
      if (1 === $r->getNumberOfRequiredParameters()) {
        $mapper= function($value, $key) use($mapper) { return $mapper($value); };
      }
    } catch (Throwable $e) {
      throw new IllegalArgumentException($e->getMessage(), $e);
    }

    if (is_array($this->value) || $this->value instanceof Traversable) {
      $self= new self([]);
      foreach ($this->value as $key => $element) {
        $m= $mapper($element, $key);
        if ($m instanceof Traversable) {
          $self->value+= iterator_to_array($m);
        } else {
          $self->value[$key]= $m;
        }
      }
      return $self;
    } else {
      $m= $mapper($this->value, null);
      return new self($m instanceof Traversable ? iterator_to_array($m) : $m);
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