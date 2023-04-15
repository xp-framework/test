<?php namespace test\unittest;

use lang\IllegalStateException;
use test\execution\TestClass;
use test\outcome\{Succeeded, Failed, Skipped};
use test\{Assert, Ignore, Test};

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
  public function failing_test() {
    Assert::equals(['fixture' => Failed::class], $this->execute(new class() {

      #[Test]
      public function fixture() {
        Assert::true(false);
      }
    }));
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
}