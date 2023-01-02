<?php namespace test;

abstract class Outcome {
  public $test;

  public function __construct($test) {
    $this->test= $test;
  }

  public abstract function kind();
}