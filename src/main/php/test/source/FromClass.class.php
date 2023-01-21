<?php namespace test\source;

use ReflectionClass;
use lang\reflection\Type;
use lang\{Reflection, XPClass};
use test\TestClass;

class FromClass {
  private $type, $selection;

  /**
   * Creates a class source
   *
   * @param string|object|XPClass|Type|ReflectionClass $arg
   * @param ?string $selection
   */
  public function __construct($arg, $selection= null) {
    $this->type= Reflection::type($arg);
    $this->selection= $selection;
  }

  /** Returns the class given */
  public function type(): Type { return $this->type; }

  /** @return iterable */
  public function groups() {
    yield new TestClass($this->type, $this->selection);
  }
}