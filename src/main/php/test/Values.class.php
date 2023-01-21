<?php namespace test;

use lang\reflection\Type;

/**
 * Values
 * 
 * - Referencing a provider method: `Values(from: 'provider')`
 * - Compact form for one-arg methods: `Values([1, 2, 3])`
 * - Passing multiple arguments: `Values([['a', 'b'], ['c', 'd']])`
 */
class Values implements Provider {
  private $list, $from;

  /**
   * Creates a values annotation
   *
   * @param  iterable $list
   * @param  ?string $from
   */
  public function __construct($list= [], $from= null) {
    $this->list= $list;
    $this->from= $from;
  }

  /**
   * Returns values
   *
   * @param  Type $type
   * @param  ?object $instance
   * @return iterable
   */
  public function values($type, $instance= null) {
    return null === $this->from
      ? $this->list
      : $type->method($this->from)->invoke($instance, [], $type)
    ;
  }
}