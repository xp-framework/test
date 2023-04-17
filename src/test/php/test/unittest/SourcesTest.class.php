<?php namespace test\unittest;

use test\source\Sources;
use test\{Assert, Test};

class SourcesTest {

  #[Test]
  public function can_create() {
    new Sources();
  }

  #[Test]
  public function empty_by_default() {
    Assert::equals(0, (new Sources())->size());
  }

  #[Test]
  public function source_passed_to_constructur() {
    Assert::equals(1, (new Sources(new TestingSource(new TestingGroup('test'))))->size());
  }

  #[Test]
  public function add() {
    Assert::equals(1, (new Sources())->add(new TestingSource(new TestingGroup('test')))->size());
  }

  #[Test]
  public function sources_passed_to_constructur() {
    $fixture= new Sources(
      new TestingSource(new TestingGroup('from-first')),
      new TestingSource(new TestingGroup('from-second'))
    );
    Assert::equals(2, $fixture->size());
  }

  #[Test]
  public function yields_groups() {
    $fixture= new Sources(
      new TestingSource(new TestingGroup('from-first')),
      new TestingSource(new TestingGroup('from-second'))
    );

    Assert::that($fixture->groups())
      ->mappedBy(function($g) { return $g->name(); })
      ->isEqualTo(['from-first', 'from-second'])
    ;
  }

  #[Test]
  public function empty_string_representation() {
    Assert::equals('test.source.Sources[]', (new Sources())->toString());
  }

  #[Test]
  public function string_representation_with_one() {
    $sources= new Sources(new TestingSource(new TestingGroup('test-1')));

    Assert::equals(
      'test.source.Sources[test.unittest.TestingSource]',
      $sources->toString()
    );
  }

  #[Test]
  public function string_representation_with_many() {
    $sources= new Sources(
      new TestingSource(new TestingGroup('test-1')),
      new TestingSource(new TestingGroup('test-2')),
      new TestingSource(new TestingGroup('test-3'))
    );

    Assert::equals(
      "test.source.Sources@[\n".
      "  test.unittest.TestingSource\n".
      "  test.unittest.TestingSource\n".
      "  test.unittest.TestingSource\n".
      "]",
      $sources->toString()
    );
  }
}