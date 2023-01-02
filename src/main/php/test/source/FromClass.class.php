<?php namespace test\source;

use ReflectionClass;
use lang\reflection\Type;
use lang\{Reflection, XPClass};
use test\TestClass;

class FromClass {
  private $type;

  /** @param string|object|XPClass|Type|ReflectionClass $arg */
  public function __construct($arg) {
    $this->type= Reflection::type($arg);
  }

  /** Returns the class given */
  public function type(): Type { return $this->type; }

  /** @return iterable */
  public function groups() {
    yield new TestClass($this->type);
  }
}