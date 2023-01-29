<?php namespace test;

use test\execution\Context;
use test\verify\Verification;

class Ignore implements Verification, Prerequisite {
  private $reason;

  /** @param ?string $reason */
  public function __construct($reason= null) { $this->reason= $reason; }

  /**
   * Describes requirement
   *
   * @param  bool $positive
   * @return string
   */
  public function requirement($positive= true) { return $this->reason; }

  /** @return bool */
  public function verify() { return false; }

  /**
   * Return assertions for a given context type
   *
   * @param  ?string $context
   * @return iterable
   */
  public function assertions(Context $context) {
    yield $this;
  }
}