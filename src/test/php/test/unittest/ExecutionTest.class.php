<?php namespace test\unittest;

use lang\IllegalStateException;
use test\execution\TestClass;
use test\outcome\{Succeeded, Failed, Skipped};
use test\verify\Runtime;
use test\{Assert, Expect, Ignore, Test};

class ExecutionTest {

  /** Executes tests and returns the outcomes */
  private function execute($test): array {
    $r= [];
    foreach ((new TestClass($test))->tests() as $test) {
      $outcome= $test->run();
      $r[$outcome->test]= get_class($outcome);
    }
    return $r;
  }

  #[Test]
  public function without_tests() {
    Assert::equals([], $this->execute(new class() { }));
  }

  #[Test]
  public function succeeding_test() {
    Assert::equals(['fixture' => Succeeded::class], $this->execute(new class() {

      #[Test]
      public function fixture() {
        Assert::true(true);
      }
    }));
  }

  #[Test]
  public function empty_test_also_succeeds() {
    Assert::equals(['fixture' => Succeeded::class], $this->execute(new class() {

      #[Test]
      public function fixture() {
        // NOOP
      }
    }));
  }

  #[Test]
  public function failing_test() {
    Assert::equals(['fixture' => Failed::class], $this->execute(new class() {

      #[Test]
      public function fixture() {
        Assert::true(false);
      }
    }));
  }

  #[Test]
  public function tests_raising_exceptions_fail() {
    Assert::equals(['fixture' => Failed::class], $this->execute(new class() {

      #[Test]
      public function fixture() {
        throw new IllegalStateException('Failure');
      }
    }));
  }

  #[Test]
  public function tests_raising_expected_exceptions_succeed() {
    Assert::equals(['fixture' => Succeeded::class], $this->execute(new class() {

      #[Test, Expect(IllegalStateException::class)]
      public function fixture() {
        throw new IllegalStateException('Failure');
      }
    }));
  }

  #[Test]
  public function tests_raising_warnings_fail() {
    Assert::equals(['fixture' => Failed::class], $this->execute(new class() {

      #[Test]
      public function fixture() {
        trigger_error('Test');
      }
    }));
    \xp::gc();
  }

  #[Test]
  public function skipped_test() {
    Assert::equals(['fixture' => Skipped::class], $this->execute(new class() {

      #[Test, Ignore]
      public function fixture() {
        throw new IllegalStateException('Unreachable');
      }
    }));
  }

  #[Test]
  public function unmatched_prerequisites_skip_test() {
    Assert::equals(['fixture' => Skipped::class], $this->execute(new class() {

      #[Test, Runtime(php: '<0.1.0')]
      public function fixture() {
        throw new IllegalStateException('Unreachable');
      }
    }));
  }

  #[Test]
  public function incorrect_annotation_use_fails_test() {
    Assert::equals(['fixture' => Failed::class], $this->execute(new class() {

      #[Test, Expect(['@incorrect'])]
      public function fixture() {
        // Would otherwise succeed
      }
    }));
  }
}