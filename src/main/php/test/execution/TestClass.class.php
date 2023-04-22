<?php namespace test\execution;

use lang\reflection\{CannotInstantiate, InvocationFailed, Type};
use lang\{Reflection, Throwable, XPClass};
use test\outcome\{Failed, Skipped};
use test\verify\Verification;
use test\{After, Before, Expect, Ignore, Provider, Test};

/** @test test.unittest.TestClassTest */
class TestClass extends Group {
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

  /** @return ?string */
  public function declaringFile() { return $this->context->type->class()->reflect()->getFileName(); }

  /** @return iterable */
  public function prerequisites() {
    foreach ($this->context->annotations(Verification::class) as $verify) {
      yield from $verify->newInstance()->assertions($this->context);
    }
  }

  /** @return iterable */
  public function tests($arguments= []) {
    $this->context->pass($arguments);
    try {
      $pass= [];
      foreach ($this->context->annotations(Provider::class) as $provider) {
        foreach ($provider->newInstance()->values($this->context) as $value) {
          $pass[]= $value;
        }
      }
      $this->context->instance= $this->context->type->newInstance(...$pass);
    } catch (InvocationFailed $e) {
      throw new GroupFailed($e->target()->compoundName(), $e->getCause());
    } catch (CannotInstantiate $e) {
      throw new GroupFailed($e->type()->name(), $e->getCause() ?? $e);
    } catch (Throwable $e) {
      throw new GroupFailed($this->context->type->name().'::<providers>', $e);
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
        try {
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
            $execute[]= new Provided($case, $provider->values($this->context));
          }

          $provider || $execute[]= new Once($case);
        } catch (Throwable $t) {
          $execute[]= new Returning($case, new Failed(
            $case->name(),
            $t->getMessage(),
            $t->getCause() ?? $t
          ));
        }
      }
    }

    // Run all @Before methods, then yield the test cases, then finalize
    // with the methods annotated with @After
    try {
      foreach ($before as $method) {
        $method->invoke($this->context->instance, [], $this->context->type);
      }
    } catch (InvocationFailed $e) {
      throw new GroupFailed($e->target()->compoundName(), $e->getCause());
    }

    foreach ($execute as $run) {
      foreach ($run->case->prerequisites() as $prerequisite) {
        if ($prerequisite->verify()) continue;
        yield new Returning($case, new Skipped($run->case->name(), $prerequisite->requirement(false)));
        continue 2;
      }

      yield from $run->targets();
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