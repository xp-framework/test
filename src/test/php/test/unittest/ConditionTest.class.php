<?php namespace test\unittest;

use lang\Reflection;
use lang\reflection\Type;
use test\verify\Condition;
use test\{Assert, Test, Values};

class ConditionTest {

  /**
   * Returns failures from verifying a condition, if any - NULL otherwise.
   *
   * @param  Condition $condition
   * @param  ?Type $context
   * @return ?string
   */
  private function failures($condition, $context) {
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
    Assert::that(new Condition('true'))
      ->mappedBy(function($c) { return $this->failures($c, null); })
      ->isNull()
    ;
  }

  #[Test]
  public function failure_includes_assertion_expression() {
    Assert::that(new Condition('function_exists("false")'))
      ->mappedBy(function($c) { return $this->failures($c, null); })
      ->isEqualTo('Failed verifying function_exists("false")')
    ;
  }

  #[Test]
  public function failure_can_access_context_scope() {
    Assert::that(new Condition('self::verify()'))
      ->mappedBy(function($c) { return $this->failures($c, Reflection::type(self::class)); })
      ->isEqualTo('Failed verifying self::verify()')
    ;
  }
}