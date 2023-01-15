<?php namespace test\verify;

use Closure;
use test\Prerequisite;
use test\assert\{Assertion, Truthy};

/**
 * Generic verification via expressions or `assert`.
 *
 * @see   https://www.php.net/assert
 */
class Condition implements Prerequisite {
  private $expression, $assert;

  static function __static() {
    ini_set('zend.assertions', 1);
    ini_set('assert.exception', 0);
  }

  public function __construct($expression= null, $assert= null) {
    $this->expression= $expression;
    $this->assert= $assert;
  }

  /** @return iterable */
  public function assertions() {
    if ($this->assert) {
      $result= eval("return assert({$this->assert});");
    } else {
      $result= $this->expression instanceof Closure ? ($this->expression)() : $this->expression;
    }

    yield new Assertion($result, new Truthy($this->assert));
  }
}