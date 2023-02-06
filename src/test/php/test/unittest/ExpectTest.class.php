<?php namespace test\unittest;

use lang\{IllegalArgumentException, IllegalStateException};
use test\source\Sources;
use test\{Assert, Expect, Test, Values};

class ExpectTest {

  #[Test]
  public function not_met() {
    $expectation= new Expect(IllegalArgumentException::class);
    Assert::false($expectation->metBy(new IllegalStateException('Test')));
  }

  #[Test]
  public function met() {
    $expectation= new Expect(IllegalArgumentException::class);
    Assert::true($expectation->metBy(new IllegalArgumentException('Test')));
  }

  #[Test, Values([['Test', true], ['Unmet', false]])]
  public function met_with_message($message, $outcome) {
    $expectation= new Expect(IllegalArgumentException::class, 'Test');
    Assert::equals($outcome, $expectation->metBy(new IllegalArgumentException($message)));
  }

  #[Test, Values([['Test', true], ['A test', true], ['Testing', true], ['Unmet', false]])]
  public function met_with_pattern($message, $outcome) {
    $expectation= new Expect(IllegalArgumentException::class, '/[Tt]est.*/');
    Assert::equals($outcome, $expectation->metBy(new IllegalArgumentException($message)));
  }
}