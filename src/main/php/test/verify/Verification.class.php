<?php namespace test\verify;

interface Verification {

  /**
   * Return assertions for a given context type
   *
   * @param  ?string $context
   * @return iterable
   */
  public function assertions($context);
}