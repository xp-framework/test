<?php namespace test;

use lang\Throwable;

/**
 * Expected exceptions
 * 
 * - Any message: `Expect(IllegalArgumentException::class)`
 * - Exact message: `Expect(IllegalArgumentException::class, 'Test')`
 * - Message matching pattern: `Expect(IllegalArgumentException::class, '/Test/')`
 *
 * @test  test.unittest.ExpectTest
 */
class Expect {
  private $class, $message;

  /** Creates a new `Expect` annotation */
  public function __construct(string $class, ?string $message= null) {
    $this->class= $class;
    $this->message= $message;
  }

  /** Check whether this expectation is met by the given throwable */
  public function metBy(Throwable $t): bool {
    $instance= $t instanceof $this->class;

    if (null === $this->message) {
      return $instance;
    } else if ('/' === ($this->message[0] ?? '')) {
      return $instance && preg_match($this->message, $t->getMessage());
    } else {
      return $instance && $this->message === $t->getMessage();
    }
  }

  /** Returns pattern for this expectation */
  public function pattern(): string {
    $pattern= strtr($this->class, '\\', '.');
    if (null === $this->message) {
      return $pattern;
    } else if ('/' === ($this->message[0] ?? '')) {
      return "{$pattern}({$this->message})";
    } else {
      return "{$pattern}('{$this->message}')";
    }
  }

  /** Returns pattern for a given exception */
  public static function patternOf(Throwable $t): string {
    return nameof($t)."('{$t->getMessage()}')";
  }
}