<?php namespace test\execution;

class Provided {
  public $case, $provider;

  /**
   * Creates a new instance
   *
   * @param TestCase $case
   * @param iterable $provider
   */
  public function __construct($case, $provider) {
    $this->case= $case;
    $this->provider= $provider;
  }

  /** @return iterable */
  public function targets() {
    foreach ($this->provider as $arguments) {
      yield new RunTest($this->case, is_array($arguments) ? $arguments : [$arguments]);
    }
  }
}
