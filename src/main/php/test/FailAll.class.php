<?php namespace test;

use lang\XPException;

class FailAll extends XPException {
  public $origin;

  public function __construct($origin, $cause) {
    parent::__construct('Exception from '.$origin, $cause);
    $this->origin= $origin;
  }
}