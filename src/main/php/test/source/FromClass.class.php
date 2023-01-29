<?php namespace test\source;

use ReflectionClass;
use lang\reflection\Type;
use lang\{Reflection, XPClass};
use test\execution\TestClass;

class FromClass extends Source {
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

  /** @return ?string */
  public function selection() { return $this->selection; }

  /** @return iterable */
  public function groups() {
    yield new TestClass($this->type, $this->selection);
  }

  /** @return string */
  public function toString() {
    return nameof($this).'<'.$this->type->name().($this->selection ? '::'.$this->selection : '').'>';
  }
}