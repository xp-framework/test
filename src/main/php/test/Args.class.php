<?php namespace test;

/**
 * Selects command line arguments
 */
class Args implements Provider {
  private $select;

  /**
   * Creates an args annotation
   *
   * @param  mixed... $select
   */
  public function __construct(...$select) {
    $this->select= $select;
  }

  /**
   * Returns values
   *
   * @param  Context $context
   * @return iterable
   */
  public function values($context) {
    foreach ($this->select as $select) {
      yield $context->arguments[$select];
    }
  }
}