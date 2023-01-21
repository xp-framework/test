<?php namespace xp\test;

use lang\Runtime;
use test\source\{FromClass, FromDirectory, FromFile, FromPackage};
use test\{Tests, Metrics, FailAll};
use util\Objects;
use util\cmd\Console;
use util\profiling\Timer;

/**
 * Runs tests
 *
 * - Run all tests inside the given directory
 *   ```sh
 *   $ xp test src/test/php
 *   ```
 * - Run test classes inside a given package
 *   ```sh
 *   $ xp test com.example.unittest.**
 *   ```
 * - Run a single test class
 *   ```sh
 *   $ xp test com.example.unittest.VerifyItWorks
 *   ```
 * - Run a single test method, here `verify()`.
 *   ```sh
 *   $ xp test com.example.unittest.VerifyItWorks::verify
 *   ```
 * - Run a single test file
 *   ```sh
 *   $ xp test Test.class.php
 *   ```
 * - Run indefinitely, watching the current directory for changes:
 *   ```sh
 *   $ xp -watch . test src/test/php
 *   ```
 */
class Runner {

  public static function main($args) {
    static $progress= ['⣾', '⣽', '⣻', '⢿', '⡿', '⣟', '⣯', '⣷'];
    static $summary= [
      'success' => "\033[42;1;37m PASS \033[0m",
      'failure' => "\033[41;1;37m FAIL \033[0m",
      'stopped' => "\033[47;1;30m STOP \033[0m",
      'skipped' => "\033[43;1;37m SKIP \033[0m",
    ];
    static $indicators= [
      'success' => "\033[32m✓\033[0m",
      'failure' => "\033[31m⨯\033[0m",
      'skipped' => "\033[33m⦾\033[0m",
    ];

    $timer= new Timer();
    $tests= new Tests();
    $metrics= new Metrics();
    for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
      if (is_dir($args[$i])) {
        $tests->add(new FromDirectory($args[$i]));
      } else if (is_file($args[$i])) {
        $tests->add(new FromFile($args[$i]));
      } else if (0 === substr_compare($args[$i], '.**', -3, 3)) {
        $tests->add(new FromPackage(substr($args[$i], 0, -3), true));
      } else if (0 === substr_compare($args[$i], '.*', -2, 2)) {
        $tests->add(new FromPackage(substr($args[$i], 0, -2), false));
      } else if (false !== ($p= strpos($args[$i], '::'))) {
        $tests->add(new FromClass(substr($args[$i], 0, $p), substr($args[$i], $p + 2)));
      } else {
        $tests->add(new FromClass($args[$i]));
      }
    }

    $failures= [];
    foreach ($tests->groups() as $group) {
      Console::writef("\r> \033[44;1;37m RUN… \033[0m \033[37m%s\033[0m", $group->name());

      // Check group prerequisites
      foreach ($group->prerequisites() as $prerequisite) {
        if (!$prerequisite->verify()) {
          $metrics->count['skipped']++;
          Console::writeLinef(
            "\r> %s \033[37m%s\033[1;32;3m // %s\033[0m\n",
            $summary['skipped'],
            $group->name(),
            $prerequisite->requirement(false)
          );
          continue 2;
        }
      }

      // Run tests in this group...
      $i= 0;
      $grouped= [];
      $before= $metrics->count['failure'];
      try {
        foreach ($group->tests() as $test) {
          Console::writef("\r%s", $progress[$i++] ?? $progress[$i= 0]);

          $timer->start();
          $outcome= $test->run();
          $timer->stop();

          $grouped[]= $metrics->record($outcome, $timer->elapsedTime());
        }
  
        $status= $metrics->count['failure'] > $before ? 'failure' : 'success';
        Console::writeLinef("\r> %s \033[37m%s\033[0m", $summary[$status], $group->name());
      } catch (FailAll $f) {
        $failures[$f->origin]= $f->getCause();
        $metrics->count['failure']++;
        Console::writeLinef(
          "\r> %s \033[37m%s\033[1;32;3m // %s\033[0m",
          $summary['stopped'],
          $group->name(),
          $f->getMessage()
        );
      }

      // ...report test case summary
      foreach ($grouped as $outcome) {
        $kind= $outcome->kind();
        Console::write('  ', $indicators[$kind], ' ', str_replace("\n", "\n    ", $outcome->test));
        switch ($kind) {
          case 'success': Console::writeLine(); break;
          case 'skipped': Console::writeLinef("\033[1;32;3m // Skip: %s\033[0m", $outcome->reason); break;
          case 'failure': {
            Console::writeLinef("\033[1;32;3m // Fail: %s\033[0m", $outcome->cause->getMessage());
            $failures[$group->name().'::'.$outcome->test]= $outcome->cause;
            break;
          }
        }
      }
      Console::writeLine();
    }

    // ...finally, output all failures
    foreach ($failures as $location => $exception) {
      Console::writeLinef("\033[31m⨯ %s\033[0m\n  %s\n", $location, Objects::stringOf($exception, '  '));
    }

    // Print out summary of test run
    $rt= Runtime::getInstance();
    Console::writeLinef(
      "\033[37mTest cases:\033[0m  \033[32m%d succeeded\033[0m, %d skipped, %d failed",
      $metrics->count['success'],
      $metrics->count['skipped'],
      $metrics->count['failure']
    );
    Console::writeLinef(
      "\033[37mMemory used:\033[0m %.2f kB (%.2f kB peak)",
      $rt->memoryUsage() / 1000,
      $rt->peakMemoryUsage() / 1000
    );
    Console::writeLinef(
      "\033[37mTime taken:\033[0m  %.3f seconds",
      $metrics->elapsed
    );

    return $metrics->count['failure'] ? 1 : 0;
  }
}