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
    Assert::equals([new TestClass(self::class)], iterator_to_array((new FromClass(self::class))->groups()));
  }

  #[Test]
  public function no_selection_by_default() {
    Assert::null((new FromClass(self::class))->selection());
  }

  #[Test, Values(['can_create', 'can*'])]
  public function selection($pattern) {
    Assert::equals($pattern, (new FromClass(self::class, $pattern))->selection());
  }

  #[Test, Values(['can_create', 'can*'])]
  public function selecting($pattern) {
    Assert::that(new FromClass(self::class, $pattern))
      ->mappedBy(function($f) { return $f->groups(); })
      ->mappedBy(function($g) { return $g->tests(); })
      ->mappedBy(function($t) { return $t->name(); })
      ->isEqualTo(['can_create'])
    ;
  }
}