<?php namespace test\execution;

use lang\Value;
use test\source\Source;
use util\{Comparison, Objects};

/** @test test.unittest.TestsTest */
class Tests implements Value {
  use Comparison;

  private $sources;

  /** Creates a tests instance with given sources */
  public function __construct(Source... $sources) {
    $this->sources= $sources;
  }

  /** @return int */
  public function size() { return sizeof($this->sources); }

  /** Adds a source */
  public function add(Source $source): self {
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
    if (sizeof($this->sources) < 2) return nameof($this).Objects::stringOf($this->sources);

    $s= nameof($this)."@[\n";
    foreach ($this->sources as $source) {
      $s.= '  '.Objects::stringOf($source, '  ')."\n";
    }
    return $s.']';
  }
}