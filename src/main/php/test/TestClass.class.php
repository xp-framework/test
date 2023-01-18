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
      yield from $prerequisite->newInstance()->assertions($this->type);
    }
  }

  /** @return iterable */
  public function tests() {
    $instance= $this->type->newInstance();

    // Enumerate methods
    $before= $after= $cases= [];
    foreach ($this->type->methods() as $method) {
      $annotations= $method->annotations();

      if ($annotations->provides(Before::class)) {
        $before[]= $method;
      } else if ($annotations->provides(After::class)) {
        $after[]= $method;
      } else if ($annotations->provides(Test::class)) {

        // Check prerequisites, if any fail - mark test as skipped and continue with next
        foreach ($annotations->all(Prerequisite::class) as $prerequisite) {
          foreach ($prerequisite->newInstance()->assertions($this->type) as $assertion) {
            if (!$assertion->verify()) {
              $cases[]= new SkipTest($method->name(), $assertion->requirement(false));
              continue 3;
            }
          }
        }

        $case= new RunTest($method->name(), $method->closure($instance));

        // Check expected exceptions
        if ($expect= $annotations->type(Expect::class)) {
          $case->expecting(Reflection::type($expect->argument('class') ?? $expect->argument(0)));
        }

        // For each provider, create test case variations from the values it provides
        $variations= 0;
        foreach ($annotations->all(Provider::class) as $provider) {
          foreach ($provider->newInstance()->values($this->type, $instance) as $arguments) {
            $cases[]= (clone $case)->passing($arguments);
            $variations++;
          }
        }

        $variations || $cases[]= $case;
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