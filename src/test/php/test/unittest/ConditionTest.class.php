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
  private function failures($condition, $context) {
    foreach ($condition->assertions(self::class) as $assertion) {
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
    Assert::that(new Condition('true'))
      ->mappedBy(function($c) { return $this->failures($c, null); })
      ->isNull()
    ;
  }

  #[Test]
  public function failure_includes_assertion_expression() {
    Assert::that(new Condition('function_exists("false")'))
      ->mappedBy(function($c) { return $this->failures($c, null); })
      ->isEqualTo('failed verifying function_exists("false")')
    ;
  }

  #[Test]
  public function failure_can_access_context_scope() {
    Assert::that(new Condition('self::verify()'))
      ->mappedBy(function($c) { return $this->failures($c, self::class); })
      ->isEqualTo('failed verifying self::verify()')
    ;
  }
}