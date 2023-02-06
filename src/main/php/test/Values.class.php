<?php namespace test;

use lang\reflection\Type;
use test\execution\Context;

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
   * @param  Context $context
   * @return iterable
   */
  public function values(Context $context) {
    $it= null === $this->from
      ? $this->list
      : $context->type->method($this->from)->invoke($context->instance, [], $context->type)
    ;
    foreach ($it as $arguments) {
      yield is_array($arguments) ? $arguments : [$arguments];
    }
  }
}