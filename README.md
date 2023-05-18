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
Tests reside inside a class suffixed by "Test" (*a test group*) and consist of methods annotated with the `Test` attribute (*the test cases*). The convention for test method naming is to use lowercase, words separated by underscores, though this is not strictly necessary.

```php
use test\{Assert, Test};

class CalculatorTest {

  #[Test]
  public function addition() {
    Assert::equals(2, (new Calculator())->add(1, 1));
  }
}
```

To run these tests, use the `test` subcommand:

```sh
$ xp test CalculatorTest.class.php
> [PASS] CalculatorTest
  ✓ addition

Tests cases: 1 succeeded, 0 skipped, 0 failed
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
* `matches(string $pattern, mixed $actual)` - verify the given value matches a given regular expression.
* `throws(string|lang.Type $expected, callable $actual)` - verify the given callable raises an exception.

Expected failures
-----------------
Using the `Expect` annotation, we can write tests that assert a given exception is raised:

```php
use test\{Assert, Expect, Test};

class CalculatorTest {

  #[Test, Expect(DivisionByZero::class)]
  public function division_by_zero() {
    (new Calculator())->divide(1, 0);
  }
}
```

To check the expected exceptions' messages, use the following:

* Any message: `Expect(DivisionByZero::class)`
* Exact message: `Expect(DivisionByZero::class, 'Division by zero')`
* Message matching regular expression: `Expect(DivisionByZero::class, '/Division by (0|zero)/i')`

Value-driven tests
------------------
To keep test code short and concise, tests may be value-driven. Values can be provided either directly inline:

```php
use test\{Assert, Test, Values};

class CalculatorTest {

  #[Test, Values([[0, 0], [1, 1], [-1, 1]])]
  public function addition($a, $b) {
    Assert::equals($a + $b, (new Calculator())->add($a, $b));
  }
}
```

...or by referencing a provider method as follows:

```php
use test\{Assert, Test, Values};

class CalculatorTest {

  private function operands(): iterable {
    yield [0, 0];
    yield [1, 1];
    yield [-1, 1];
  }

  #[Test, Values(from: 'operands')]
  public function addition($a, $b) {
    Assert::equals($a + $b, (new Calculator())->add($a, $b));
  }
}
```

Prerequisites
-------------
Test classes and methods may have prerequisites, which must successfully verify in order for the tests to run:

```php
use test\verify\Runtime;
use test\{Assert, Test};

#[Runtime(extensions: ['bcmath'])]
class CalculatorTest {

  #[Test]
  public function addition() {
    Assert::equals(3, (int)bcadd(1, 2));
  }
}
```

The following verifications are included:

* `Runtime(os: '^WIN', php: '^8.0', extensions: ['bcmath'])` - runtime verifications.
* `Condition(assert: 'function_exists("random_int")')` - verify given expression in the context of the test class.

Passing arguments to tests
--------------------------
Especially for integration tests, passing values like a connection string from the command line to the test class is important. Add the `Args` annotation to the class as follows:

```php
use test\Args;
use com\mongodb\MongoConnection;

#[Args('use')]
class IntegrationTest {
  private $conn;

  public function __construct(string $dsn) {
    $this->conn= new MongoConnection($dsn);
  }

  // ...shortened for brevity
}
```

...then pass the arguments as follows:

```bash
$ xp test IntegrationTest --use=mongodb://locahost
# ...
```

See also
--------
* [RFC #344: New testing library](https://github.com/xp-framework/rfc/issues/344)