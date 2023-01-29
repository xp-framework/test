<?php namespace test;

use test\execution\Context;

interface Provider {

  /**
   * Returns values
   *
   * @param  Context $context
   * @return iterable
   */
  public function values(Context $context);

}