<?php namespace test\verify;

interface Verification {

  /**
   * Return assertions for a given context
   *
   * @param  Context $context
   * @return iterable
   */
  public function assertions($context);
}