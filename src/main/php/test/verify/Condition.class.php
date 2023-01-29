<?php namespace test\verify;

use Closure;
use test\assert\{Assertion, Verify};
use test\execution\Context;

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
   * @param  Context $context
   * @return iterable
   */
  public function assertions(Context $context) {
    $assertion= $this->assert instanceof Closure
      ? $this->assert
      : eval("return function() { return {$this->assert}; };")
    ;

    yield new Assertion(
      $assertion->bindTo(null, $context->type->literal())->__invoke(),
      new Verify($this->assert)
    );
  }
}