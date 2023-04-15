<?php namespace xp\test;

use lang\{Runtime, XPClass};
use test\execution\{GroupFailed, Metrics};
use test\source\{Sources, FromClass, FromDirectory, FromFile, FromPackage};
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
    static $groups= [
      'success' => "\033[42;1;37m PASS \033[0m",
      'failure' => "\033[41;1;37m FAIL \033[0m",
      'stopped' => "\033[47;1;30m STOP \033[0m",
      'skipped' => "\033[43;1;37m SKIP \033[0m",
    ];
    static $cases= [
      'success' => "\033[32m✓\033[0m",
      'failure' => "\033[31m⨯\033[0m",
      'skipped' => "\033[33m⦾\033[0m",
    ];
    static $counts= [
      'success' => "\033[32m%d succeeded\033[0m",
      'failure' => "\033[31m%d failed\033[0m",
      'skipped' => "\033[33m%d skipped\033[0m",
    ];

    $timer= new Timer();
    $overall= new Timer();
    $sources= new Sources();
    $metrics= new Metrics();
    $pass= [];
    for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
      if ('--' === $args[$i]) {
        $pass= array_slice($args, $i + 1);
        break;
      } else if (0 === strncmp($args[$i], '--', 2)) {
        $pass= array_slice($args, $i);
        break;
      } else if (is_dir($args[$i])) {
        $sources->add(new FromDirectory($args[$i]));
      } else if (is_file($args[$i])) {
        $sources->add(new FromFile($args[$i]));
      } else if (0 === substr_compare($args[$i], '.**', -3, 3)) {
        $sources->add(new FromPackage(substr($args[$i], 0, -3), true));
      } else if (0 === substr_compare($args[$i], '.*', -2, 2)) {
        $sources->add(new FromPackage(substr($args[$i], 0, -2), false));
      } else if (false !== ($p= strpos($args[$i], '::'))) {
        $sources->add(new FromClass(substr($args[$i], 0, $p), substr($args[$i], $p + 2)));
      } else {
        $sources->add(new FromClass($args[$i]));
      }
    }

    $overall->start();
    $failures= [];
    foreach ($sources->groups() as $group) {
      Console::writef("\r> \033[44;1;37m RUN… \033[0m \033[37m%s\033[0m", $group->name());

      // Check group prerequisites
      foreach ($group->prerequisites() as $prerequisite) {
        if (!$prerequisite->verify()) {
          $metrics->count['skipped']++;
          Console::writeLinef(
            "\r> %s \033[37m%s\033[1;32;3m // %s\033[0m\n",
            $groups['skipped'],
            $group->name(),
            $prerequisite->requirement(false)
          );
          continue 2;
        }
      }

      // Run tests in this group...
      $grouped= [];
      $before= $metrics->count['failure'];
      try {
        $run= 0;
        $s= sizeof($progress);
        foreach ($group->tests($pass) as $test) {
          Console::writef("\r%s", $progress[$run % $s]);

          $timer->start();
          $outcome= $test->run();
          $timer->stop();

          $grouped[]= $metrics->record($outcome, $timer->elapsedTime());
          $run++;
        }

        if ($run) {
          $status= $metrics->count['failure'] > $before ? 'failure' : 'success';
          Console::writeLinef("\r> %s \033[37m%s\033[0m", $groups[$status], $group->name());
        } else {
          $metrics->count['skipped']++;
          Console::writeLinef(
            "\r> %s \033[37m%s\033[1;32;3m // %s\033[0m",
            $groups['skipped'],
            $group->name(),
            'No test cases declared in this group'
          );
        }
      } catch (GroupFailed $f) {
        $failures[$f->origin]= $f->failure();
        $metrics->count['failure']++;
        Console::writeLinef(
          "\r> %s \033[37m%s\033[1;32;3m // %s\033[0m",
          $groups['stopped'],
          $group->name(),
          $f->getMessage()
        );
      }

      // ...report test case summary
      foreach ($grouped as $outcome) {
        $kind= $outcome->kind();
        Console::write('  ', $cases[$kind], ' ', str_replace("\n", "\n    ", $outcome->test));
        switch ($kind) {
          case 'success': Console::writeLine(); break;
          case 'skipped': {
            Console::writeLinef("\033[1;32;3m // Skip%s\033[0m", $outcome->reason ? ": {$outcome->reason}" : '');
            break;
          }
          case 'failure': {
            Console::writeLinef("\033[1;32;3m // Fail: %s\033[0m", $outcome->reason);
            $failures[$group->name().'::'.$outcome->test]= $outcome;
            break;
          }
        }
      }
      Console::writeLine();
    }
    $overall->stop();

    // Check if any tests were run
    if ($metrics->empty()) {
      Console::writeLine("\033[33m@", (new XPClass(self::class))->getClassLoader(), "\033[0m");
      Console::writeLine("\033[41;1;37m ERROR \033[0;1;37m No tests run\033[0m\n");
      Console::writeLine('Supplied sources: ', $sources);
      return 2;
    }

    // ...finally, output all failures
    foreach ($failures as $location => $failure) {
      Console::writeLinef(
        "\033[31m⨯ %s\033[0m\n  \033[37;1m%s\033[0m\n%s\n",
        $location,
        $failure->reason,
        $failure->trace('    ')
      );
    }

    // Print out summary of test run
    $summary= [];
    foreach (['success', 'skipped', 'failure'] as $metric) {
      if ($metrics->count[$metric]) {
        $summary[]= sprintf($counts[$metric], $metrics->count[$metric]);
      }
    }

    $rt= Runtime::getInstance();
    Console::writeLinef(
      "\033[37mTest cases:\033[0m  %s",
      implode(', ', $summary)
    );
    Console::writeLinef(
      "\033[37mMemory used:\033[0m %.2f kB (%.2f kB peak)",
      $rt->memoryUsage() / 1000,
      $rt->peakMemoryUsage() / 1000
    );
    Console::writeLinef(
      "\033[37mTime taken:\033[0m  %.3f seconds (%.3f seconds overall)",
      $metrics->elapsed,
      $overall->elapsedTime()
    );

    return $metrics->count['failure'] ? 1 : 0;
  }
}