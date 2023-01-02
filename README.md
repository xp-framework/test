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
    Assert::equals(2, 1 + 1);
  }
}
```

To run the test, use the `test` subcommand:

```sh
$ xp test CalculatorTest.class.php
>  PASS  CalculatorTest
  ✓ addition

Tests:       1 succeeded, 0 skipped, 0 failed
Memory used: 1556.36 kB (1610.49 kB peak)
Time taken:  0.001 seconds
```
