<?php namespace test\unittest;

use test\assert\Assertable;
use test\{Assert, AssertionFailed, Expect, Test};

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

  #[Test]
  public function map_scalar() {
    Assert::equals(
      new Assertable(2),
      (new Assertable(1))->map(function($v) { return $v * 2; })
    );
  }

  #[Test]
  public function map_array() {
    Assert::equals(
      new Assertable([2, 4]),
      (new Assertable([1, 2]))->map(function($v) { return $v * 2; })
    );
  }

  #[Test]
  public function map_preserves_keys() {
    Assert::equals(
      new Assertable(['a' => 2, 'b' => 4]),
      (new Assertable(['a' => 1, 'b' => 2]))->map(function($v) { return $v * 2; })
    );
  }

  #[Test]
  public function map_can_transform_keys_via_yield() {
    Assert::equals(
      new Assertable([1 => 'a', 2 => 'b']),
      (new Assertable(['a' => 1, 'b' => 2]))->map(function($v, $k) { yield $v => $k; })
    );
  }

  #[Test]
  public function map_traversable() {
    $f= function() {
      yield 1;
      yield 2;
    };
    Assert::equals(
      new Assertable([2, 4]),
      (new Assertable($f()))->map(function($v) { return $v * 2; })
    );
  }
}