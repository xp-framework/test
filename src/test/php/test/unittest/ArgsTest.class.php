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
      [...(new Args())->values(new Context(Reflection::type(self::class), $arguments))]
    );
  }

  #[Test, Values([[['test']], [['one', 'two']]])]
  public function select_first($arguments) {
    Assert::equals(
      [$arguments[0]],
      [...(new Args(0))->values(new Context(Reflection::type(self::class), $arguments))]
    );
  }

  #[Test, Values([[['--dsn=test']], [['positional', '--dsn=test']]])]
  public function select_named($arguments) {
    Assert::equals(
      ['test'],
      [...(new Args('dsn'))->values(new Context(Reflection::type(self::class), $arguments))]
    );
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function missing_positional_argument() {
    (new Args(0))->values(new Context(Reflection::type(self::class), []))->next();
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function missing_named_argument() {
    (new Args('dsn'))->values(new Context(Reflection::type(self::class), []))->next();
  }

  #[Test, Values([null, 'localhost'])]
  public function optional_positional_argument($default) {
    Assert::equals(
      [$default],
      [...(new Args([0 => $default]))->values(new Context(Reflection::type(self::class), []))]
    );
  }

  #[Test, Values([null, 'localhost'])]
  public function optional_named_argument($default) {
    Assert::equals(
      [$default],
      [...(new Args(['dsn' => $default]))->values(new Context(Reflection::type(self::class), []))]
    );
  }
}