<?php namespace test\unittest;

use lang\Value;
use test\assert\Equals;
use test\{Assert, Test, Values};

class EqualsTest {

  /** @return iterable */
  private function values() {
    yield [true];
    yield [false];
    yield [0];
    yield [-1];
    yield [1];
    yield [1.5];
    yield [-0.5];
    yield [''];
    yield ['Test'];
    yield [[]];
    yield [[1, 2, 3]];
    yield [['key' => 'value']];
    yield [$this];
    yield [new class() implements Value {
      public function hashCode() { return md5(self::class); }
      public function toString() { return self::class; }
      public function compareTo($value) { return $value instanceof self ? $this <=> $value : 1; }
    }];
  }

  #[Test, Values('values')]
  public function equals_itself($value) {
    Assert::true((new Equals($value))->matches($value));
  }

  #[Test]
  public function array_order_is_relevant() {
    Assert::false((new Equals([1, 2, 3]))->matches([3, 2, 1]));
  }

  #[Test]
  public function hash_order_is_not_relevant() {
    Assert::true((new Equals(['a' => 1, 'b' => 2]))->matches(['b' => 2, 'a' => 1]));
  }
}