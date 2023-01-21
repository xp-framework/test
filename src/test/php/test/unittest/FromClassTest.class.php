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

  #[Test]
  public function no_selection_by_default() {
    $fixture= new FromClass(self::class);
    Assert::null($fixture->selection());
  }

  #[Test, Values(['can_create', 'can*'])]
  public function selection($pattern) {
    $fixture= new FromClass(self::class, $pattern);
    Assert::equals($pattern, $fixture->selection());
  }

  #[Test, Values(['can_create', 'can*'])]
  public function selecting($pattern) {
    $fixture= new FromClass(self::class, $pattern);
    Assert::that($fixture)
      ->mappedBy(function($f) { return $f->groups(); })
      ->mappedBy(function($g) { return $g->tests(); })
      ->mappedBy(function($t) { return $t->name(); })
      ->isEqualTo(['can_create'])
    ;
  }
}