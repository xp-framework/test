<?php namespace test;

use lang\reflection\Type;

interface Provider {

  /**
   * Returns values
   *
   * @param  Type $type
   * @param  ?object $instance
   * @return iterable
   */
  public function values($type, $instance= null);

}