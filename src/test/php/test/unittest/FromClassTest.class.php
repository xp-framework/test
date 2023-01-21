<?php namespace test\unittest;

use lang\{XPClass, Reflection};
use test\source\FromClass;
use test\{Assert, Test, Values, TestClass};

class FromClassTest {

  /** @return iterable */
  private function arguments() {
    yield [$this];
    yield [self::class];
    yield [new XPClass(self::class)];
  }

  #[Test]
  public function can_create() {
    new FromClass(self::class);
  }

  #[Test, Values(from: 'arguments')]
  public function type($argument) {
    Assert::equals(Reflection::type(self::class), (new FromClass($argument))->type());
  }

  #[Test]
  public function groups_yields_this() {
    $fixture= new FromClass(self::class);
    Assert::equals([new TestClass(self::class)], iterator_to_array($fixture->groups()));
  }
}