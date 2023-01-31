<?php namespace test\unittest;

use test\execution\{Tests, Group};
use test\{Assert, Test};

class TestsTest {

  #[Test]
  public function can_create() {
    new Tests();
  }

  #[Test]
  public function empty_by_default() {
    Assert::equals(0, (new Tests())->size());
  }

  #[Test]
  public function source_passed_to_constructur() {
    Assert::equals(1, (new Tests(new TestingSource(new TestingGroup('test'))))->size());
  }

  #[Test]
  public function add() {
    Assert::equals(1, (new Tests())->add(new TestingSource(new TestingGroup('test')))->size());
  }

  #[Test]
  public function sources_passed_to_constructur() {
    $fixture= new Tests(
      new TestingSource(new TestingGroup('from-first')),
      new TestingSource(new TestingGroup('from-second'))
    );
    Assert::equals(2, $fixture->size());
  }

  #[Test]
  public function yields_groups() {
    $fixture= new Tests(
      new TestingSource(new TestingGroup('from-first')),
      new TestingSource(new TestingGroup('from-second'))
    );

    Assert::that($fixture->groups())
      ->mappedBy(function($g) { return $g->name(); })
      ->isEqualTo(['from-first', 'from-second'])
    ;
  }
}