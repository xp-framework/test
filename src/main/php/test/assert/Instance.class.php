<?php namespace test\assert;

use lang\Type;

class Instance extends Condition {
  protected $type;

  /** @param string|Type $type */
  public function __construct($type) {
    $this->type= $type instanceof Type ? $type : Type::forName($type);
  }

  public function matches($value) {
    return $this->type->isInstance($value);
  }

  public function describe($value, $positive) {
    return sprintf(
      '%s %s %s', 
      self::stringOf($value),
      $positive ? 'is an instance of' : 'is not an instance of',
      $this->type->toString()
    );
  }
}
