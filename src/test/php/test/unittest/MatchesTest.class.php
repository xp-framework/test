<?php namespace test\unittest;

use lang\FormatException;
use test\assert\Matches;
use test\{Assert, Expect, Test, Values};

class MatchesTest {

  #[Test, Values(['/Test/', '/test/i', '/^[tT]es.+/'])]
  public function matches_test($pattern) {
    Assert::true((new Matches($pattern))->matches('Test'));
  }

  #[Test, Values(['/A/', '/test/'])]
  public function does_not_match($pattern) {
    Assert::false((new Matches($pattern))->matches('Test'));
  }

  #[Test, Values([null, false, true, 1, -1.5, [[]]])]
  public function matches_only_strings($value) {
    Assert::false((new Matches('/Test/'))->matches($value));
  }

  #[Test]
  public function generally_does_not_match_objects() {
    Assert::false((new Matches('/Test/'))->matches($this));
  }

  #[Test]
  public function matches_stringable() {
    Assert::true((new Matches('/Test/'))->matches(new class() {
      public function __toString() { return 'Test'; }
    }));
  }

  #[Test, Expect(class: FormatException::class, message: 'Using not.a.regex')]
  public function invalid_pattern() {
    (new Matches('not.a.regex'))->matches('Test');
  }

  #[Test]
  public function shorthand() {
    Assert::matches('/Test.*/', 'Testing');
  }
}