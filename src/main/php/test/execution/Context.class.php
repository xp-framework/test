<?php namespace test\execution;

use lang\reflection\Type;

class Context {
  public $type, $arguments;
  public $instance= null;

  public function __construct(Type $type, array $arguments= []) {
    $this->type= $type;
    $this->arguments= $arguments;
  }
}