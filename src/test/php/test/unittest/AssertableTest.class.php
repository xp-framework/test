<?php namespace test\unittest;

use test\assert\Assertable;
use test\{Assert, AssertionFailed, Expect, Test, Values};

class AssertableTest {

  #[Test]
  public function can_create() {
    new Assertable($this);
  }

  #[Test]
  public function returns_self_on_success() {
    Assert::instance(Assertable::class, (new Assertable($this))->isEqualTo($this));
  }

  #[Test, Expect(AssertionFailed::class)]
  public function throws_assertion_failed_on_error() {
    (new Assertable($this))->isEqualTo(null);
  }
}