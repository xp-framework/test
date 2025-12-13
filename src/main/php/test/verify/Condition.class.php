<?php namespace test\verify;

use Closure;
use test\assert\{Assertion, Verify};
use test\execution\Context;

/**
 * Generic verification via `assert`.
 * 
 * - `Condition('self::processExecutionEnabled()')`
 * - `Condition(assert: 'function_exists("bcadd")')`
 * - `Condition(assert: fn() => PHP_VERSION_ID >= 80500)`
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
    if ($this->assert instanceof Closure) {
      $string= '<function()>';
      $assertion= $this->assert;
    } else {
      $string= (string)$this->assert;
      $assertion= $context->type->evaluate("fn() => {$this->assert};");
    }

    yield new Assertion(
      $assertion->bindTo($context->instance, $context->type->literal())(),
      new Verify($string)
    );
  }
}