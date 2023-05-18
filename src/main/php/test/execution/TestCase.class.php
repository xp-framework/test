<?php namespace test\execution;

use Throwable as Any;
use lang\reflection\Type;
use lang\{Throwable, Runnable};
use test\outcome\{Succeeded, Skipped, Failed};
use test\{AssertionFailed, Expect, Outcome, Prerequisite, Warnings};
use util\Objects;

class TestCase {
  private $name, $run;
  private $expectation= null;
  private $prerequisites= [];

  public function __construct(string $name, callable $run) {
    $this->name= $name;
    $this->run= $run;
  }

  /** @return string */
  public function name() { return $this->name; }

  /** @return ?Expect */
  public function expectation() { return $this->expectation; }

  /** @return array<Prerequisite> */
  public function prerequisites() { return $this->prerequisites; }

  /**
   * Verifies a given prerequisite
   *
   * @param  Prerequisite $prerequisite
   * @return self
   */
  public function verifying(Prerequisite $prerequisite) {
    $this->prerequisites[]= $prerequisite;
    return $this;
  }

  /**
   * Sets an expected exception
   *
   * @param  Expect $expectation
   * @return self
   */
  public function expecting(Expect $expectation) {
    $this->expectation= $expectation;
    return $this;
  }

  /**
   * Runs this test case and returns its outcome
   *
   * @param  array<mixed> $arguments
   * @return Outcome
   */
  public function run($arguments= []) {
    $warnings= [];
    \xp::gc();
    set_error_handler(function($kind, $message, $file, $line) use(&$warnings) {
      $warnings[]= [$kind, $message, $file, $line];
      __error($kind, $message, $file, $line);
    });
    try {
      if ($arguments) {
        $name= $this->name.Objects::stringOf($arguments);
        ($this->run)(...$arguments);
      } else {
        $name= $this->name;
        ($this->run)();
      }

      if ($this->expectation) {
        return new Failed($name, 'Did not catch expected '.$this->expectation->pattern(), null);
      } else if (\xp::$errors) {
        \xp::gc();
        return new Failed($name, 'Succeeded but raised '.sizeof($warnings).' warning(s)', new Warnings($warnings));
      } else {
        return new Succeeded($name);
      }
    } catch (Any $e) {
      $t= Throwable::wrap($e);

      if (null === $this->expectation) {
        $reason= $t instanceof AssertionFailed ? $t->getMessage() : 'Unexpected '.lcfirst($t->compoundMessage());
        return new Failed($name, $reason, $t);
      } else if (!$this->expectation->metBy($t)) {
        $message= sprintf(
          "Did not catch expected %s, %s was thrown instead",
          $this->expectation->pattern(),
          Expect::patternOf($t)
        );
        return new Failed($name, $message, $t);
      } else {
        return new Succeeded($name);
      }
    } finally {
      restore_error_handler();
    }
  }
}