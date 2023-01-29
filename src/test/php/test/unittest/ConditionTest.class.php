<?php namespace test\unittest;

use lang\Reflection;
use test\verify\Condition;
use test\{Assert, Context, Test, Values};

class ConditionTest {

  /**
   * Returns failures from verifying a condition, if any - NULL otherwise.
   *
   * @param  Condition $condition
   * @return ?string
   */
  private function failures($condition) {
    foreach ($condition->assertions(new Context(Reflection::type(self::class))) as $assertion) {
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
      ->mappedBy([$this, 'failures'])
      ->isNull()
    ;
  }

  #[Test]
  public function failure_includes_assertion_expression() {
    Assert::that(new Condition('function_exists("false")'))
      ->mappedBy([$this, 'failures'])
      ->isEqualTo('Failed verifying function_exists("false")')
    ;
  }

  #[Test]
  public function failure_can_access_context_scope() {
    Assert::that(new Condition('self::verify()'))
      ->mappedBy([$this, 'failures'])
      ->isEqualTo('Failed verifying self::verify()')
    ;
  }
}