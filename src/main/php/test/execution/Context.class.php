<?php namespace test\execution;

use lang\reflection\Type;

class Context {
  public $type, $arguments;
  public $instance= null;

  public function __construct(Type $type, array $arguments= []) {
    $this->type= $type;
    $this->arguments= $arguments;
  }

  /** @return void */
  public function pass(array $arguments) { $this->arguments= $arguments; }

  /**
   * Yields selected annotations for the context type and its parents
   *
   * @param  string $selected
   * @return iterable
   */
  public function annotations($selected) {
    $type= $this->type;
    do {
      yield from $type->annotations()->all($selected);
    } while ($type= $type->parent());
  }
}