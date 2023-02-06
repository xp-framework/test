<?php namespace test\execution;

use lang\reflection\{CannotInstantiate, InvocationFailed, Type};
use lang\{Reflection, Throwable, XPClass};
use test\verify\Verification;
use test\{After, Before, Expect, Ignore, Provider, Test};

class TestClass extends Group {
  const ONCE= [[]];

  private $context, $selection;

  /**
   * Creates an instance for a given class
   *
   * @param  string|object|XPClass|Type|ReflectionClass $arg
   * @param ?string $selection
   */
  public function __construct($arg, $selection= null) {
    $this->context= new Context($arg instanceof Type ? $arg : Reflection::type($arg));
    $this->selection= $selection;
  }

  /** @return string */
  public function name() { return $this->context->type->name(); }

  /** @return iterable */
  public function prerequisites() {
    foreach ($this->context->type->annotations()->all(Verification::class) as $verify) {
      yield from $verify->newInstance()->assertions($this->context);
    }
  }

  /** @return iterable */
  public function tests($arguments= []) {
    $this->context->pass($arguments);
    try {
      $pass= [];
      foreach ($this->context->type->annotations()->all(Provider::class) as $provider) {
        foreach ($provider->newInstance()->values($this->context) as $value) {
          $pass[]= $value;
        }
      }
      $this->context->instance= $this->context->type->newInstance(...$pass);
    } catch (InvocationFailed $e) {
      throw new GroupFailed($e->target()->compoundName(), $e->getCause());
    } catch (CannotInstantiate $e) {
      throw new GroupFailed($e->type()->name(), $e->getCause());
    } catch (Throwable $e) {
      throw new GroupFailed('providers', $e);
    }

    // Enumerate methods
    $before= $after= $execute= [];
    foreach ($this->context->type->methods() as $method) {
      $annotations= $method->annotations();

      if ($annotations->provides(Before::class)) {
        $before[]= $method;
      } else if ($annotations->provides(After::class)) {
        $after[]= $method;
      } else if (
        $annotations->provides(Test::class) &&
        (null === $this->selection || fnmatch($this->selection, $method->name()))
      ) {
        $case= new TestCase($method->name(), $method->closure($this->context->instance));

        // Check prerequisites
        foreach ($annotations->all(Verification::class) as $verify) {
          foreach ($verify->newInstance()->assertions($this->context) as $prerequisite) {
            $case->verifying($prerequisite);
          }
        }

        // Check expected exceptions
        if ($expect= $annotations->type(Expect::class)) {
          $case->expecting($expect->newInstance());
        }

        // For each provider, create test case variations from the values it provides
        $provider= null;
        foreach ($annotations->all(Provider::class) as $i => $annotation) {
          $provider= $annotation->newInstance();
          $execute[]= [$case, $provider->values($this->context)];
        }

        $provider || $execute[]= [$case, self::ONCE];
      }
    }

    // Run all @Before methods, then yield the test cases, then finalize
    // with the methods annotated with @After
    foreach ($before as $method) {
      $method->invoke($this->context->instance, [], $this->context->type);
    }

    foreach ($execute as list($case, $iterations)) {
      foreach ($case->prerequisites() as $prerequisite) {
        if ($prerequisite->verify()) continue;
        yield new SkipTest($case->name(), $prerequisite->requirement(false));
        continue 2;
      }

      foreach ($iterations as $arguments) {
        yield new RunTest($case, $arguments);
      }
    }

    foreach ($after as $method) {
      try {
        $method->invoke($this->context->instance, [], $this->context->type);
      } catch (InvocationFailed $e) {
        $e->printStackTrace();
      }
    }
  }
}