<?php namespace test\assert;

class Assertion {
  public $value, $condition;

  public function __construct($value, $condition) {
    $this->value= $value;
    $this->condition= $condition;
  }

  public function requirement($positive= true) {
    return $this->condition->describe($this->value, $positive);
  }

  public function verify() {
    return $this->condition->matches($this->value);
  }
}