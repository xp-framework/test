<?php namespace test\assert;

class RequiredVersion extends Condition {
  protected $component, $range;

  public function __construct($component, $range) {
    $this->component= $component;
    $this->range= $range;
  }

  public function matches($value) {
    if ('^' === $this->range[0]) {
      return (
        version_compare($value, substr($this->range, 1), '>=') &&
        version_compare($value, ($this->range[1] + 1).substr($this->range, 2), '<')
      );
    }

    // TODO: Other operators

    return false;
  }

  public function describe($value, $positive) {
    return sprintf(
      '%s %s %s %s',
      $this->component,
      self::stringOf($value),
      $positive ? 'meets version requirement' : 'does not meet version requirement',
      $this->range
    );
  }
}