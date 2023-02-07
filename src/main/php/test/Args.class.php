<?php namespace test;

use lang\IllegalArgumentException;
use test\execution\Context;

/**
 * Selects command line arguments
 *
 * - Select all arguments: `Args`
 * - Select by position: `Args(0)`
 * - Select named *--dsn=...*: `Args('dsn')`
 * - Select with default: `Args(['dsn' => 'localhost'])`
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

  private function missing($argument) {
    throw new IllegalArgumentException("Missing argument {$argument}");
  }

  /**
   * Selects a specific argument
   *
   * @param  Context $context
   * @param  int|string $argument
   * @param  mixed... $default
   * @throws IllegalArgumentException
   */
  private function argument($context, $argument, ...$default) {
    if (is_int($argument)) {
      return $context->arguments[$argument] ?? ($default
        ? $default[0]
        : $this->missing("#{$argument}")
      );
    } else {
      $prefix= "--{$argument}=";
      $l= strlen($prefix);
      foreach ($context->arguments as $argument) {
        if (0 === strncmp($argument, $prefix, $l)) return substr($argument, $l);
      }
      return $default ? $default[0] : $this->missing("--{$argument}");
    }
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
      if (is_array($select)) {
        yield $this->argument($context, key($select), current($select));
      } else {
        yield $this->argument($context, $select);
      }
    }
  }
}