<?php namespace test;

use Throwable as Any;
use lang\reflection\Type;
use lang\{Throwable, Runnable};
use test\outcome\{Succeeded, Failed};
use util\Objects;

class RunTest implements Runnable {
  private $name, $run;
  private $expecting= null;
  private $arguments= [];

  public function __construct(string $name, callable $run) {
    $this->name= $name;
    $this->run= $run;
  }

  /**
   * Sets an expected exception type
   *
   * @param Type $type
   * @param ?string $message
   * @return self
   */
  public function expecting(Type $type, $message= null) {
    $this->expecting= [$type, $message];
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
    \xp::gc();

    try {
      ($this->run)(...$this->arguments);
      return new Succeeded($this->name);
    } catch (Any $e) {
      $t= Throwable::wrap($e);
      if (list($type, $message)= $this->expecting) {
        if ($type->isInstance($t)) return new Succeeded($this->name);
      }
      return new Failed($this->name, $t);
    }
  }
}