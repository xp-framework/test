<?php namespace test;

use lang\reflection\Type;
use lang\{Reflection, XPClass, Throwable};
use test\verify\Runtime;

class TestClass {
  private $type;

  /**
   * Creates an instance for a given class
   *
   * @param  string|object|XPClass|Type|ReflectionClass $arg
   */
  public function __construct($arg) {
    $this->type= Reflection::type($arg);
  }

  /** @return string */
  public function name() { return $this->type->name(); }

  /** @return iterable */
  public function prerequisites() {
    foreach ($this->type->annotations()->all(Prerequisite::class) as $prerequisite) {
      yield from $prerequisite->newInstance()->assertions();
    }
  }

  /** @return iterable */
  public function tests() {
    $instance= $this->type->newInstance();

    // Enumerate methods, handling BC with xp-framework/unittest annotations
    $before= $after= $cases= [];
    foreach ($this->type->methods() as $method) {
      $annotations= $method->annotations();

      if ($annotations->provides(Before::class) || $annotations->provides('unittest.Before')) {
        $before[]= $method;
      } else if ($annotations->provides(After::class) || $annotations->provides('unittest.After')) {
        $after[]= $method;
      } else if ($annotations->provides(Test::class) || $annotations->provides('unittest.Test')) {

        // Check prerequisites, if any fail - mark test as skipped and continue with next
        foreach ($annotations->all(Prerequisite::class) as $prerequisite) {
          foreach ($prerequisite->newInstance()->assertions() as $assertion) {
            if (!$assertion->verify()) {
              $cases[]= new SkipTest($method->name(), $assertion->requirement(false));
              continue 3;
            }
          }
        }

        $case= new RunTest($method->name(), $method->closure($instance));

        // Check @Expect
        if ($expect= $annotations->type(Expect::class) ?? $annotations->type('unittest.Expect')) {
          $case->expecting(Reflection::type($expect->argument('class') ?? $expect->argument(0)));
        }

        // Check @Values, which may either be:
        //
        // * Referencing a provider method: `Values('provider')`
        // * Compact form for one-arg methods: `Values([1, 2, 3])`
        // * Passing multiple arguments: `Values([['a', 'b'], ['c', 'd']])`
        if ($values= $annotations->type(Values::class) ?? $annotations->type('unittest.Values')) {
          $args= $values->arguments();
          if (sizeof($args) > 1) {
            $provider= $args;
          } else if (is_array($args[0])) {
            $provider= $args[0];
          } else {
            $provider= $this->type->method($args[0])->invoke($instance, [], $instance);
          }

          foreach ($provider as $values) {
            $cases[]= (clone $case)->passing(is_array($values) ? $values : [$values]);
          }
        } else {
          $cases[]= $case;
        }
      }
    }

    // Run all @Before methods, then yield the test cases, then finalize
    // with the methods annotated with @After
    foreach ($before as $method) {
      $method->invoke($instance, [], $instance);
    }

    yield from $cases;

    foreach ($after as $method) {
      $method->invoke($instance, [], $instance);
    }
  }
}