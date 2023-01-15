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

  #[Test, Values([['0.3.0', true], ['0.3.1', true], ['0.4.0', false], ['1.0.0', false]])]
  public function caret_syntax_for_pre_releases($version, $expected) {
    $fixture= new RequiredVersion('Test', '^0.3');
    Assert::equals($expected, $fixture->matches($version));
  }

  #[Test, Values([['1.0.0', false], ['1.0.1', true], ['1.10.3', true], ['2.0.0', true], ['0.9.1', false]])]
  public function greater_1_0($version, $expected) {
    $fixture= new RequiredVersion('Test', '>1.0');
    Assert::equals($expected, $fixture->matches($version));
  }

  #[Test, Values([['1.0.0', true], ['1.0.1', true], ['1.10.3', true], ['2.0.0', true], ['0.9.1', false]])]
  public function greater_or_equal_1_0($version, $expected) {
    $fixture= new RequiredVersion('Test', '>=1.0');
    Assert::equals($expected, $fixture->matches($version));
  }

  #[Test, Values([['1.0.0', true], ['1.0.1', true], ['1.10.3', true], ['2.0.0', false], ['0.9.1', true]])]
  public function less_2_0($version, $expected) {
    $fixture= new RequiredVersion('Test', '<2.0');
    Assert::equals($expected, $fixture->matches($version));
  }

  #[Test, Values([['1.0.0', true], ['1.0.1', true], ['1.10.3', true], ['2.0.0', true], ['0.9.1', true]])]
  public function less_or_equal_2_0($version, $expected) {
    $fixture= new RequiredVersion('Test', '<=2.0');
    Assert::equals($expected, $fixture->matches($version));
  }

  #[Test, Values([['1.0.0', true], ['1.0.1', false], ['1.10.3', false], ['2.0.0', false], ['0.9.1', false]])]
  public function exact($version, $expected) {
    $fixture= new RequiredVersion('Test', '1.0.0');
    Assert::equals($expected, $fixture->matches($version));
  }
}