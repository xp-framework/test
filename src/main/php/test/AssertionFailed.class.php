<?php namespace test;

use lang\XPException;

class AssertionFailed extends XPException {

  public function __construct($assertion) {
    parent::__construct('Failed asserting that '.$assertion);
  }
}