<?php namespace test\assert;

class Truthy extends Condition {
  private $description;

  public function __construct($description= null) {
    $this->description= $description;
  }

  public function matches($value) {
    return (bool)$value;
  }

  public function describe($value, $positive) {
    return sprintf(
      '%s %s', 
      $this->description ?? self::stringOf($value),
      $positive ? 'is truthy' : 'is not truthy'
    );
  }
}