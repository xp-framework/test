<?php namespace test\unittest;

use lang\IllegalArgumentException;
use test\assert\Assertable;
use test\{Assert, AssertionFailed, Expect, Test, Values};

class AssertableTest {

  private function values($value) {
    return $value * 2;
  }

  /** @return iterable */
  private function doubling() {
    yield [[$this, 'values']];
    yield [function($v) { return $v * 2; }];
  }

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
  public function map_string_using_1_required_parameter_only() {
    Assert::equals(
      new Assertable('Test'),
      (new Assertable(' Test '))->mappedBy('trim')
    );
  }

  #[Test, Values(from: 'doubling')]
  public function map_scalar($mapper) {
    Assert::equals(new Assertable(2), (new Assertable(1))->mappedBy($mapper));
  }

  #[Test, Values(from: 'doubling')]
  public function map_array($mapper) {
    Assert::equals(new Assertable([2, 4]), (new Assertable([1, 2]))->mappedBy($mapper));
  }

  #[Test, Values(from: 'doubling')]
  public function map_preserves_keys($mapper) {
    Assert::equals(
      new Assertable(['a' => 2, 'b' => 4]),
      (new Assertable(['a' => 1, 'b' => 2]))->mappedBy($mapper)
    );
  }

  #[Test, Values(from: 'doubling')]
  public function map_traversable($mapper) {
    $f= function() {
      yield 1;
      yield 2;
    };
    Assert::equals(new Assertable([2, 4]), (new Assertable($f()))->mappedBy($mapper));
  }

  #[Test]
  public function map_can_transform_keys_via_yield() {
    Assert::equals(
      new Assertable([1 => 'a', 2 => 'b']),
      (new Assertable(['a' => 1, 'b' => 2]))->mappedBy(function($v, $k) { yield $v => $k; })
    );
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function illegal_callback() {
    (new Assertable(1))->mappedBy('not-a-function');
  }
}