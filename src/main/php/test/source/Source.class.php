<?php namespace test\source;

use lang\Value;
use util\{Comparison, Objects};

abstract class Source implements Value {
  use Comparison;

  /** @return string */
  public function toString() { return nameof($this); }
}