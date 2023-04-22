<?php namespace test\unittest;

use test\execution\TestClass;
use test\verify\Runtime;
use test\{Assert, Test};

#[Runtime(php: '>=7.0.0', extensions: ['core'])]
class TestClassTest {
  
  #[Test]
  public function can_create() {
    new TestClass($this);
  }

  #[Test]
  public function name() {
    Assert::equals(nameof($this), (new TestClass($this))->name());
  }

  #[Test]
  public function prerequisites() {
    $prerequisites= iterator_to_array((new TestClass($this))->prerequisites());

    Assert::instance('test.assert.Assertion[]', $prerequisites);
    Assert::equals(1, sizeof($prerequisites));
  }
}