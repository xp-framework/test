<?php namespace test;

use lang\reflection\{CannotInstantiate, InvocationFailed, Type};
use lang\{Reflection, Throwable, XPClass};
use test\verify\Verification;

class TestClass extends Group {
  private $type, $selection;

  /**
   * Creates an instance for a given class
   *
   * @param  string|object|XPClass|Type|ReflectionClass $arg
   * @param ?string $selection
   */
  public function __construct($arg, $selection= null) {
    $this->type= $arg instanceof Type ? $arg : Reflection::type($arg);
    $this->selection= $selection;
  }

  /** @return string */
  public function name() { return $this->type->name(); }

  /** @return iterable */
  public function prerequisites() {
    foreach ($this->type->annotations()->all(Verification::class) as $verify) {
      yield from $verify->newInstance()->assertions($this->type);
    }
  }

  /** @return iterable */
  public function tests($arguments= []) {
    $context= new Context($this->type, $arguments);
    try {
      $pass= [];
      foreach ($this->type->annotations()->all(Provider::class) as $provider) {
        foreach ($provider->newInstance()->values($context) as $value) {
          $pass[]= $value;
        }
      }
      $context->instance= $this->type->newInstance(...$pass);
    } catch (InvocationFailed $e) {
      throw new GroupFailed($e->target()->compoundName(), $e->getCause());
    } catch (CannotInstantiate $e) {
      throw new GroupFailed($e->type()->name(), $e->getCause());
    } catch (Throwable $e) {
      throw new GroupFailed('providers', $e);
    }

    // Enumerate methods
    $before= $after= $cases= [];
    foreach ($this->type->methods() as $method) {
      $annotations= $method->annotations();

      if ($annotations->provides(Before::class)) {
        $before[]= $method;
      } else if ($annotations->provides(After::class)) {
        $after[]= $method;
      } else if (
        $annotations->provides(Test::class) &&
        (null === $this->selection || fnmatch($this->selection, $method->name()))
      ) {

        $case= new RunTest($method->name(), $method->closure($context->instance));

        // Check prerequisites
        foreach ($annotations->all(Verification::class) as $verify) {
          foreach ($verify->newInstance()->assertions($context) as $prerequisite) {
            $case->verify($prerequisite);
          }
        }

        // Check expected exceptions
        if ($expect= $annotations->type(Expect::class)) {
          $case->expecting(Reflection::type($expect->argument('class') ?? $expect->argument(0)));
        }

        // For each provider, create test case variations from the values it provides
        $variations= 0;
        foreach ($annotations->all(Provider::class) as $provider) {
          foreach ($provider->newInstance()->values($context) as $arguments) {
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
      $method->invoke($context->instance, [], $context->type);
    }

    yield from $cases;

    foreach ($after as $method) {
      $method->invoke($context->instance, [], $context->type);
    }
  }
}