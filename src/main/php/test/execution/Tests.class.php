<?php namespace test\execution;

use lang\Value;
use test\source\Source;
use util\{Comparison, Objects};

class Tests implements Value {
  use Comparison;

  private $sources;

  public function __construct(Source... $sources) {
    $this->sources= $sources;
  }

  public function add(Source $source) {
    $this->sources[]= $source;
    return $this;
  }

  /** @return iterable */
  public function groups() {
    foreach ($this->sources as $source) {
      yield from $source->groups();
    }
  }

  /** @return string */
  public function toString() {
    $s= nameof($this)."@[\n";
    foreach ($this->sources as $source) {
      $s.= '  '.Objects::stringOf($source, '  ')."\n";
    }
    return $s.']';
  }
}