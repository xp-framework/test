<?php namespace test;

use test\execution\Context;

/**
 * Selects command line arguments
 *
 * - Select all arguments: `Args`
 * - Select by position: `Args(0)`
 * - Select named *--dsn=...*: `Args('dsn')`
 *
 * @test  test.unittest.ArgsTest
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
  public function values(Context $context) {

    // Select all arguments
    if (empty($this->select)) {
      yield from $context->arguments;
      return;
    }

    // Select specific arguments, either by position or by --name=...
    foreach ($this->select as $select) {
      if (is_int($select)) {
        yield $context->arguments[$select] ?? null;
      } else {
        $prefix= "--{$select}=";
        $l= strlen($prefix);
        foreach ($context->arguments as $argument) {
          if (0 === strncmp($argument, $prefix, $l)) {
            yield substr($argument, $l);
            continue 2;
          }
        }
        yield null;
      }
    }
  }
}