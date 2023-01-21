<?php namespace test\verify;

use Closure;
use lang\reflection\Type;
use test\Prerequisite;
use test\assert\{Assertion, Verify};

/**
 * Generic verification via `assert`.
 * 
 * - `Condition('self::processExecutionEnabled()')`
 * - `Condition(assert: 'function_exists("bcadd")')`
 *
 * @test  test.unittest.ConditionTest
 */
class Condition implements Prerequisite {
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
      $result= $this->assert->bindTo(null, $context ? $context->literal() : null)->__invoke();
    } else {
      $f= eval("return function() { return {$this->assert}; };");
      $result= $f->bindTo(null, $context ? $context->literal() : null)->__invoke();
    }

    yield new Assertion($result, new Verify($this->assert));
  }
}