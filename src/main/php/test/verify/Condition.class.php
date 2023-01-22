<?php namespace test\verify;

use Closure;
use lang\reflection\Type;
use test\assert\{Assertion, Verify};

/**
 * Generic verification via `assert`.
 * 
 * - `Condition('self::processExecutionEnabled()')`
 * - `Condition(assert: 'function_exists("bcadd")')`
 *
 * @test  test.unittest.ConditionTest
 */
class Condition implements Verification {
  private $assert;

  /** @param string|function(): bool $assert */
  public function __construct($assert) {
    $this->assert= $assert;
  }

  /**
   * Return assertions for a given context type
   *
   * @param  ?Type $context
   * @return iterable
   */
  public function assertions($context) {
    if ($this->assert instanceof Closure) {
      $assertion= $this->assert->bindTo(null, $context ? $context->literal() : null);
    } else {
      $f= eval("return function() { return {$this->assert}; };");
      $assertion= $f->bindTo(null, $context ? $context->literal() : null);
    }

    yield new Assertion($assertion(), new Verify($this->assert));
  }
}