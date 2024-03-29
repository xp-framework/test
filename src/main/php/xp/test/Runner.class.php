<?php namespace xp\test;

use lang\{Runtime, XPClass, Throwable, IllegalArgumentException};
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
 *
 * By default, the test run is reported to the console. The `-r` argument
 * (which can be supplied multiple times) followed by the implementing class
 * and optional arguments separated by commas, changes the reporting.
 */
class Runner {

  private static function report($arg) {
    $segments= explode(',', $arg);
    $name= array_shift($segments);

    $class= XPClass::forName(strpos($name, '.') ? $name : 'xp.test.'.$name);
    if ($class->isSubclassOf(Report::class)) return $class->newInstance(...$segments);

    throw new IllegalArgumentException('Class '.$class.' cannot be used as a report');
  }

  public static function main($args) {
    $timer= new Timer();
    $overall= new Timer();
    $sources= new Sources();
    $metrics= new Metrics();
    $reporting= new Reporting();

    $pass= [];
    try {
      for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
        if ('--' === $args[$i]) {
          $pass= array_slice($args, $i + 1);
          break;
        } else if ('-r' === $args[$i]) {
          $reporting->add(self::report($args[++$i]));
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
      $reporting->delegated() || $reporting->add(new Grouped());
    } catch (Throwable $t) {
      Console::writeLine("\033[33m@", (new XPClass(self::class))->getClassLoader(), "\033[0m");
      Console::writeLine("\033[41;1;37m ERROR \033[0;1;37m Invalid command line argument(s)\033[0m\n");
      Console::writeLine($t);
      return 2;
    }

    $failures= [];
    $overall->start();
    $reporting->start($sources);
    foreach ($sources->groups() as $group) {
      $reporting->enter($group);

      // Check group prerequisites
      foreach ($group->prerequisites() as $prerequisite) {
        if (!$prerequisite->verify()) {
          $metrics->count['skipped']++;
          $reporting->skip($group, $prerequisite->requirement(false));
          continue 2;
        }
      }

      // Run tests in this group...
      $results= [];
      $failed= false;
      try {
        $run= 0;
        foreach ($group->tests($pass) as $test) {
          $reporting->running($group, $test->case, $run);
          $outcome= $test->run($timer);
          $reporting->finished($group, $test->case, $outcome);

          $results[]= $metrics->record($outcome);
          $run++;

          if ('failure' === $outcome->kind()) {
            $failed= true;
            $failures[$group->name().'::'.$test->name()]= $outcome;
          }
        }

        if (0 === $run) {
          $metrics->count['skipped']++;
          $reporting->skip($group, 'No test cases declared in this group');
        } else if ($failed) {
          $reporting->fail($group, $results);
        } else {
          $reporting->pass($group, $results);
        }
      } catch (GroupFailed $f) {
        $failures[$f->origin]= $f->failure();
        $metrics->count['failure']++;
        $reporting->stop($group, $f->getMessage());
      }
    }
    $overall->stop();

    // Check if any tests were run
    if ($metrics->empty()) {
      Console::writeLine("\033[33m@", (new XPClass(self::class))->getClassLoader(), "\033[0m");
      Console::writeLine("\033[41;1;37m ERROR \033[0;1;37m No tests run\033[0m\n");
      Console::writeLine('Supplied sources: ', $sources);
      return 2;
    }

    // ...finally, output all failures and a summary
    $rt= Runtime::getInstance();
    $reporting->summary(
      $metrics->using($rt->memoryUsage(), $rt->peakMemoryUsage()),
      $overall->elapsedTime(),
      $failures
    );
    return $metrics->count['failure'] ? 1 : 0;
  }
}