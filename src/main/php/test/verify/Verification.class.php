<?php namespace test\verify;

use test\execution\Context;

interface Verification {

  /**
   * Return assertions for a given context
   *
   * @param  Context $context
   * @return iterable
   */
  public function assertions(Context $context);
}