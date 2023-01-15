<?php namespace test\verify;

use Closure;
use test\Prerequisite;
use test\assert\{Assertion, Verify};

/**
 * Generic verification via `assert`.
 * 
 * - `Condition('self::processExecutionEnabled()')`
 * - `Condition(assert: 'function_exists("bcadd")')`
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
   * @param  ?string $context
   * @return iterable
   */
  public function assertions($context) {
    if ($this->assert instanceof Closure) {
      $result= $this->assert->bindTo(null, $context)->__invoke();
    } else {
      $f= eval("return function() { return {$this->assert}; };");
      $result= $f->bindTo(null, $context)->__invoke();
    }

    yield new Assertion($result, new Verify($this->assert));
  }
}