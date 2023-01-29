<?php namespace test;

interface Provider {

  /**
   * Returns values
   *
   * @param  Context $context
   * @return iterable
   */
  public function values($context);

}