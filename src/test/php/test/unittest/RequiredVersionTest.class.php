<?php namespace test\unittest;

use test\assert\RequiredVersion;
use test\{Assert, Test, Values};

class RequiredVersionTest {

  #[Test]
  public function can_create() {
    new RequiredVersion('Test', '1.0');
  }

  #[Test, Values([['1.0.0', true], ['1.0.1', true], ['1.10.3', true], ['2.0.0', false], ['0.9.1', false]])]
  public function caret_syntax($version, $expected) {
    $fixture= new RequiredVersion('Test', '^1.0');
    Assert::equals($expected, $fixture->matches($version));
  }
}