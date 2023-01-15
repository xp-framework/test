<?php namespace test\verify;

use Closure;
use test\Prerequisite;
use test\assert\{Assertion, Verify};

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

  /**
   * Return assertions for a given context type
   *
   * @param  ?string $context
   * @return iterable
   */
  public function assertions($context) {
    if ($this->assert) {
      $expression= eval("return function() { return assert({$this->assert}); };");
      $result= $expression->bindTo(null, $context)->__invoke();
    } else if ($this->expression instanceof Closure) {
      $result= $this->expression->bindTo(null, $context)->__invoke();
    } else {
      $result= $this->expression;
    }

    yield new Assertion($result, new Verify($this->assert));
  }
}