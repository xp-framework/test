<?php namespace test\unittest;

use test\verify\Condition;
use test\{Assert, Test, Values};

class ConditionTest {

  /**
   * Returns failures from verifying a condition, if any - NULL otherwise.
   *
   * @param  Condition $condition
   * @param  ?string $context
   * @return ?string
   */
  private function failures($condition, $context= null) {
    foreach ($condition->assertions($context) as $assertion) {
      if (!$assertion->verify()) return $assertion->requirement(false);
    }
    return null;
  }

  /** @return bool */
  private static function verify() { return false; }

  #[Test]
  public function can_create() {
    new Condition('true');
  }

  #[Test]
  public function success() {
    Assert::null($this->failures(new Condition('function_exists("strlen")')));
  }

  #[Test]
  public function failure_includes_assertion_expression() {
    Assert::equals(
      'failed verifying function_exists("false")',
      $this->failures(new Condition('function_exists("false")'), self::class)
    );
  }

  #[Test]
  public function failure_can_access_context_scope() {
    Assert::equals(
      'failed verifying self::verify()',
      $this->failures(new Condition('self::verify()'), self::class)
    );
  }
}