<?php namespace xp\test;

use util\cmd\Console;

/**
 * Console output, the default output mechanism, using console colors
 * and a progress bar while running a test group.
 */
class ConsoleOutput extends Output {
  const PROGRESS= ['⣾', '⣽', '⣻', '⢿', '⡿', '⣟', '⣯', '⣷'];
  const GROUPS= [
    'success' => "\033[42;1;37m PASS \033[0m",
    'failure' => "\033[41;1;37m FAIL \033[0m",
    'stopped' => "\033[47;1;30m STOP \033[0m",
    'skipped' => "\033[43;1;37m SKIP \033[0m",
  ];
  const CASES= [
    'success' => "\033[32m✓\033[0m",
    'failure' => "\033[31m⨯\033[0m",
    'skipped' => "\033[33m⦾\033[0m",
  ];
  const COUNTS= [
    'success' => "\033[32m%d succeeded\033[0m",
    'failure' => "\033[31m%d failed\033[0m",
    'skipped' => "\033[33m%d skipped\033[0m",
  ];

  /**
   * Enter a group
   *
   * @param  test.execution.TestClass $group
   * @return void
   */
  public function enter($group) {
    Console::writef("\r> \033[44;1;37m RUN… \033[0m \033[37m%s\033[0m", $group->name());
  }

  /**
   * Running a given test
   * 
   * @param  test.execution.TestClass $group
   * @param  test.execution.TestCase $test
   * @param  int $n
   * @return void
   */
  public function running($group, $test, $n) {
    Console::writef("\r%s", self::PROGRESS[$n % 8]); // sizeof(self::PROGRESS)
  }

  /**
   * Report test case summary. Used by `pass()` and `fail()`.
   *
   * @param  test.Outcome[] $results
   * @return void
   */
  private function summarize($results) {
    foreach ($results as $outcome) {
      $kind= $outcome->kind();
      Console::write('  ', self::CASES[$kind], ' ', str_replace("\n", "\n    ", $outcome->test));
      switch ($kind) {
        case 'success': Console::writeLine(); break;
        case 'skipped': {
          Console::writeLinef("\033[1;32;3m // Skip%s\033[0m", $outcome->reason ? ": {$outcome->reason}" : '');
          break;
        }
        case 'failure': {
          Console::writeLinef("\033[1;32;3m // Fail: %s\033[0m", $outcome->reason);
          break;
        }
      }
    }
    Console::writeLine();
  }

  /**
   * Pass an entire group
   *
   * @param  test.execution.TestClass $group
   * @param  test.Outcome[] $results
   * @return void
   */
  public function pass($group, $results) {
    Console::writeLinef("\r> %s \033[37m%s\033[0m", self::GROUPS['success'], $group->name());
    $this->summarize($results);
  }

  /**
   * Fail an entire group
   *
   * @param  test.execution.TestClass $group
   * @param  test.Outcome[] $results
   * @return void
   */
  public function fail($group, $results) {
    Console::writeLinef("\r> %s \033[37m%s\033[0m", self::GROUPS['failure'], $group->name());
    $this->summarize($results);
  }

  /**
   * Skip an entire group
   *
   * @param  test.execution.TestClass $group
   * @param  string $reason
   * @return void
   */
  public function skip($group, $reason) {
    Console::writeLinef(
      "\r> %s \033[37m%s\033[1;32;3m // %s\033[0m\n",
      self::GROUPS['skipped'],
      $group->name(),
      $reson
    );
  }

  /**
   * Stop an entire group
   *
   * @param  test.execution.TestClass $group
   * @param  string $reason
   * @return void
   */
  public function stop($group, $reason) {
    Console::writeLinef(
      "\r> %s \033[37m%s\033[1;32;3m // %s\033[0m",
      self::GROUPS['stopped'],
      $group->name(),
      $reason
    );
  }

  /**
   * Print out summary of test run
   *
   * @param  test.execution.Metrics $metrices
   * @param  float $overall
   * @param  [:test.Outcome] $failures
   * @return void
   */
  public function summary($metrics, $overall, $failures) {
    foreach ($failures as $location => $failure) {
      Console::writeLinef(
        "\033[31m⨯ %s\033[0m\n  \033[37;1m%s\033[0m\n%s\n",
        $location,
        $failure->reason,
        $failure->trace('    ')
      );
    }

    $summary= [];
    foreach ($metrics->count as $metric => $count) {
      $count && $summary[]= sprintf(self::COUNTS[$metric], $count);
    }

    Console::writeLinef(
      "\033[37mTest cases:\033[0m  %s",
      implode(', ', $summary)
    );
    Console::writeLinef(
      "\033[37mMemory used:\033[0m %.2f kB (%.2f kB peak)",
      $metrics->memoryUsed / 1000,
      $metrics->peakMemoryUsed / 1000
    );
    Console::writeLinef(
      "\033[37mTime taken:\033[0m  %.3f seconds (%.3f seconds overall)",
      $metrics->elapsed,
      $overall
    );
  }
}