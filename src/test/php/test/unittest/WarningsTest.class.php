<?php namespace test\unittest;

use test\assert\Matches;
use test\execution\TestCase;
use test\outcome\{Succeeded, Failed};
use test\{Assert, Test};

class WarningsTest {

  /** Executes test function */
  private function execute($function) {
    return (new TestCase('test', $function))->run();
  }

  #[Test]
  public function without_warnings() {
    Assert::instance(Succeeded::class, $this->execute(function() { }));
  }

  #[Test]
  public function trigger_error() {
    $r= $this->execute(function() { trigger_error('Test'); });
    Assert::equals('E_USER_NOTICE: Test', $r->cause->getStackTrace()[0]->message);
  }

  #[Test]
  public function trigger_deprecation_error() {
    $r= $this->execute(function() { trigger_error('Test', E_USER_DEPRECATED); });
    Assert::equals('E_USER_DEPRECATED: Test', $r->cause->getStackTrace()[0]->message);
  }

  #[Test]
  public function fopen_nonexistant_file() {
    $r= $this->execute(function() { fopen('$', 'r'); });

    Assert::that($r->cause->getStackTrace()[0]->message)
      ->is(new Matches('/E_WARNING: fopen.+: No such file or directory/i'))
    ;
  }

  #[Test]
  public function multiple_warnings() {
    $r= $this->execute(function() { trigger_error('One'); trigger_error('Two'); });
    Assert::equals('E_USER_NOTICE: One', $r->cause->getStackTrace()[0]->message);
    Assert::equals('E_USER_NOTICE: Two', $r->cause->getStackTrace()[1]->message);
  }
}