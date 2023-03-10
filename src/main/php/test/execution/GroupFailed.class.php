<?php namespace test\execution;

use lang\{Throwable, XPException};
use test\outcome\Failed;

/** Indicates a failure on the test group - e.g., during instantiation */
class GroupFailed extends XPException {
  public $origin;

  /** Create a new instance given an origin and a cause */
  public function __construct(string $origin, Throwable $cause) {
    parent::__construct('Exception from '.$origin, $cause);
    $this->origin= $origin;
  }

  /** @return Failed */
  public function failure() {
    return new Failed($this->origin, 'Unexpected '.lcfirst($this->cause->compoundMessage()), $this->cause);
  }
}