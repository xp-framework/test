<?php namespace test\unittest;

use io\{Folder, Path};
use lang\IllegalArgumentException;
use test\source\FromDirectory;
use test\{Assert, Expect, Test, Values};

class FromDirectoryTest {

  /** @return iterable */
  private function arguments() {
    yield ['.'];
    yield [new Folder('.')];
    yield [new Path('.')];
  }

  #[Test]
  public function can_create() {
    new FromDirectory('.');
  }

  #[Test, Expect(class: IllegalArgumentException::class)]
  public function throws_exception_when_given_argument_does_not_exist() {
    new FromDirectory('does-not-exist');
  }

  #[Test, Values(from: 'arguments')]
  public function folder($argument) {
    Assert::equals(new Folder('.'), (new FromDirectory($argument))->folder());
  }
}