<?php namespace xp\test;

use lang\Runtime;
use test\source\{FromClass, FromDirectory, FromFile, FromPackage};
use test\{Tests, Metrics};
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
    ];
    static $indicators= [
      'success' => "\033[32m✓\033[0m",
      'failure' => "\033[31m⨯\033[0m",
      'skipped' => "\033[36m⦾\033[0m",
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
      } else {
        $tests->add(new FromClass($args[$i]));
      }
    }

    foreach ($tests->groups() as $group) {
      Console::writef("\r> \033[44;1;37m RUN… \033[0m \033[37m%s\033[0m", $group->name());

      // Run group...
      $i= 0;
      $grouped= [];
      $status= 'success';
      foreach ($group->tests() as $test) {
        Console::writef("\r%s", $progress[$i++] ?? $progress[$i= 0]);

        $timer->start();
        $outcome= $test->run();
        $timer->stop();

        if ('failure' === $outcome->kind()) $status= 'failure';
        $grouped[]= $metrics->record($outcome, $timer->elapsedTime());
      }

      // ...then report results
      Console::writeLinef("\r> %s \033[37m%s\033[0m", $summary[$status], $group->name());
      foreach ($grouped as $outcome) {
        $kind= $outcome->kind();
        Console::writeLine('  ', $indicators[$kind], ' ', str_replace("\n", "\n    ", $outcome->test));

        if ('failure' === $kind) {
          Console::writeLine('  ', Objects::stringOf($outcome->cause, '  '));
        }
      }
      Console::writeLine();
    }
    $timer->stop();

    // Print out summary of test run
    $rt= Runtime::getInstance();
    Console::writeLinef(
      "\033[37mTests:\033[0m       \033[32m%d succeeded\033[0m, %d skipped, %d failed",
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

    return 'success' === $status ? 0 : 1;
  }
}