<?php namespace test\verify;

use test\Prerequisite;
use test\assert\{Assertion, Matches, RequiredVersion};

class Runtime implements Prerequisite {
  private $os, $php;

  public function __construct($os= null, $php= null) {
    $this->os= $os;
    $this->php= $php;
  }

  /**
   * Yields assertions to verify runtime OS / PHP
   *
   * @return iterable
   */
  public function assertions() {
    null === $this->os || yield new Assertion(PHP_OS, new Matches('/'.$this->os.'/i'));
    null === $this->php || yield new Assertion(PHP_VERSION, new RequiredVersion('PHP', $this->php));
  }
}