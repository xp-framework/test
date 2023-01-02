<?php namespace test\unittest;

use lang\Value;
use test\assert\Instance;
use test\{Assert, Test};

class InstanceTest {

  #[Test]
  public function this_instanceof_self() {
    Assert::true((new Instance(self::class))->matches($this));
  }

  #[Test]
  public function this_is_not_a_value() {
    Assert::false((new Instance(Value::class))->matches($this));
  }

  #[Test]
  public function anoymous_subclass() {
    Assert::true((new Instance(Value::class))->matches(new class() implements Value {
      public function hashCode() { return md5(self::class); }
      public function toString() { return self::class; }
      public function compareTo($value) { return $value instanceof self ? $this <=> $value : 1; }
    }));
  }
}