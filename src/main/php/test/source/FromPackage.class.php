<?php namespace test\source;

use lang\reflection\Package;
use test\execution\TestClass;

class FromPackage {
  private $package, $recursive;

  /**
   * Loads tests from a given package
   *
   * @param string|Package $arg
   * @param bool $recursive
   */
  public function __construct($arg, $recursive= false) {
    $this->package= $arg instanceof Package ? $arg : new Package($arg);
    $this->recursive= $recursive;
  }

  /**
   * Yields types in a given package, recursing into children if requested.
   *
   * @param  Package $package
   * @return iterable
   */
  private function typesIn($package) {
    if ($this->recursive) {
      foreach ($package->children() as $child) {
        yield from $this->typesIn($child);
      }
    }
    yield from $package->types();
  }

  /** @return iterable */
  public function groups() {
    foreach ($this->typesIn($this->package) as $type) {
      if ($type->instantiable() && strstr($type->name(), 'Test')) yield new TestClass($type);
    }
  }
}