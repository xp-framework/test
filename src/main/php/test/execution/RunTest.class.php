<?php namespace test\execution;

use Throwable as Any;
use lang\reflection\Type;
use lang\{Throwable, Runnable};
use test\outcome\{Succeeded, Skipped, Failed};
use test\{AssertionFailed, Expect, Outcome, Prerequisite};
use util\Objects;

class RunTest implements Runnable {
  private $name, $run;
  private $prerequisites= [];
  private $expectation= null;
  private $arguments= [];

  public function __construct(string $name, callable $run) {
    $this->name= $name;
    $this->run= $run;
  }

  /** @return string */
  public function name() { return $this->name; }

  /**
   * Sets an expected exception type
   *
   * @param  Prerequisite $prerequisite
   * @return self
   */
  public function verify(Prerequisite $prerequisite) {
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
   * Passes arguments. Suffixes this test case's name with a comma-separted list
   * of string representations of the given arguments enclosed in square brackets.
   *
   * @param  array|mixed $arguments
   * @return self
   */
  public function passing($arguments) {
    $this->arguments= is_array($arguments) ? $arguments : [$arguments];
    $this->name.= Objects::stringOf($this->arguments);
    return $this;
  }

  /** Runs this test case and returns its outcome */
  public function run(): Outcome {
    foreach ($this->prerequisites as $prerequisite) {
      if (!$prerequisite->verify()) return new Skipped($this->name, $prerequisite->requirement(false));
    }

    \xp::gc();
    try {
      ($this->run)(...$this->arguments);

      if (null === $this->expectation) {
        return new Succeeded($this->name);
      } else {
        return new Failed($this->name, 'Did not catch expected '.$this->expectation->pattern(), null);
      }
    } catch (Any $e) {
      $t= Throwable::wrap($e);

      if (null === $this->expectation) {
        $reason= $t instanceof AssertionFailed ? $t->getMessage() : 'Unexpected '.lcfirst($t->compoundMessage());
        return new Failed($this->name, $reason, $t);
      } else if (!$this->expectation->metBy($t)) {
        return new Failed($this->name, 'Did not catch expected '.$this->expectation->pattern(), $t);
      } else {
        return new Succeeded($this->name);
      }
    }
  }
}