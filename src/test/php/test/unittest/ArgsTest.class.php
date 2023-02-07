<?php namespace test\unittest;

use lang\{Reflection, IllegalArgumentException};
use test\execution\Context;
use test\{Args, Assert, Expect, Test, Values};

class ArgsTest {

  #[Test]
  public function can_create() {
    new Args();
  }

  #[Test, Values([[[]], [['test']], [['one', 'two']]])]
  public function select_all($arguments) {
    Assert::equals(
      $arguments,
      iterator_to_array((new Args())->values(new Context(Reflection::type(self::class), $arguments)))
    );
  }

  #[Test, Values([[['test']], [['one', 'two']]])]
  public function select_first($arguments) {
    Assert::equals(
      [$arguments[0]],
      iterator_to_array((new Args(0))->values(new Context(Reflection::type(self::class), $arguments)))
    );
  }

  #[Test, Values([[['--dsn=test']], [['positional', '--dsn=test']]])]
  public function select_named($arguments) {
    Assert::equals(
      ['test'],
      iterator_to_array((new Args('dsn'))->values(new Context(Reflection::type(self::class), $arguments)))
    );
  }

  #[Test]
  public function missing_positional_argument() {
    Assert::equals(
      [null],
      iterator_to_array((new Args(0))->values(new Context(Reflection::type(self::class), [])))
    );
  }

  #[Test]
  public function missing_named_argument() {
    Assert::equals(
      [null],
      iterator_to_array((new Args('dsn'))->values(new Context(Reflection::type(self::class), [])))
    );
  }
}