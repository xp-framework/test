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
   * @param  string|object|XPClass|Type|ReflectionClass $arg
   * @param  ?string $selection
   */
  public function __construct($arg, ?string $selection= null) {
    $this->type= Reflection::type($arg);
    $this->selection= $selection;
  }

  /** Returns the class given */
  public function type(): Type { return $this->type; }

  /** Returns the selection, if any */
  public function selection(): ?string { return $this->selection; }

  /** @return iterable */
  public function groups() {
    yield new TestClass($this->type, $this->selection);
  }

  /** @return string */
  public function toString() {
    return nameof($this).'<'.$this->type->name().($this->selection ? '::'.$this->selection : '').'>';
  }
}