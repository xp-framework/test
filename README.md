Tests
=====

[![Build status on GitHub](https://github.com/xp-framework/test/workflows/Tests/badge.svg)](https://github.com/xp-framework/test/actions)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.svg)](http://php.net/)
[![Supports PHP 8.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-8_0plus.svg)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-framework/test/version.png)](https://packagist.org/packages/xp-framework/test)

Unit and integration tests for the XP Framework

Writing a test
--------------
Tests reside inside a class and are annotated with the `Test` attribute.

```php
use test\{Assert, Test};

class CalculatorTest {

  #[Test]
  public function addition() {
    Assert::equals(2, (new Calculator())->add(1, 1));
  }
}
```

To run the test, use the `test` subcommand:

```sh
$ xp test CalculatorTest.class.php
> [PASS] CalculatorTest
  ✓ addition

Tests:       1 succeeded, 0 skipped, 0 failed
Memory used: 1556.36 kB (1610.49 kB peak)
Time taken:  0.001 seconds
```

Assertions
----------
The following shorthand methods exist on the `Assert` class:

* `equals(mixed $expected, mixed $actual)` - check two values are equal. Uses the `util.Objects::equal()` method internally, which allows overwriting object comparison.
* `notEquals(mixed $expected, mixed $actual)` - opposite of above
* `true(mixed $actual)` - check a given value is equal to the *true* boolean
* `false(mixed $actual)`  - check a given value is equal to the *false* boolean
* `null(mixed $actual)` - check a given value is *null*
* `instance(string|lang.Type $expected, mixed $actual)` - check a given value is an instance of the given type.

Value-driven tests
------------------
To keep test code short and concise, tests may be value-driven. Values can be provided either directly inline:

```php
use test\{Assert, Test, Values};

class CalculatorTest {

  #[Test, Values([0, 0], [1, 1], [-1, 1])]
  public function addition($a, $b) {
    Assert::equals($a + $b, (new Calculator())->add($a, $b));
  }
}
```

...or by referencing a provider method as follows:

```php
use test\{Assert, Test, Values};

class CalculatorTest {

  private function provider(): iterable {
    yield [0, 0];
    yield [1, 1];
    yield [-1, 1];
  }

  #[Test, Values('provider')]
  public function addition($a, $b) {
    Assert::equals($a + $b, (new Calculator())->add($a, $b));
  }
}
```
