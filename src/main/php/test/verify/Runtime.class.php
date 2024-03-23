<?php namespace test\verify;

use test\assert\{Assertion, Verify, Matches, RequiredVersion};
use test\execution\Context;

class Runtime implements Verification {
  private $os, $php, $extensions;

  /**
   * Runtime prerequisite
   *
   * @param  ?string $os Operating system name
   * @param  ?string $php PHP version constraint
   * @param  array<string> $extensions PHP extensions to check for
   */
  public function __construct(?string $os= null, ?string $php= null, array $extensions= []) {
    $this->os= $os;
    $this->php= $php;
    $this->extensions= $extensions;
  }

  /**
   * Yields assertions to verify runtime OS / PHP
   *
   * @param  Context $context
   * @return iterable
   */
  public function assertions(Context $context) {
    null === $this->os || yield new Assertion(PHP_OS_FAMILY, new Matches('/'.$this->os.'/i'));
    null === $this->php || yield new Assertion(PHP_VERSION, new RequiredVersion('PHP', $this->php));

    foreach ($this->extensions as $extension) {
      yield new Assertion(
        extension_loaded($extension),
        new Verify('PHP extension "'.$extension.'" is loaded')
      );
    }
  }
}