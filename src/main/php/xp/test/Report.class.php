<?php namespace xp\test;

abstract class Report {

  /**
   * Called when entering a group. The group ends with one of the following:
   *
   * - `pass()` - All of the tests in this group passed
   * - `fail()` - At least one of the tests failed
   * - `skip()` - The entire group was skipped
   * - `stop()` - The entire group errored
   *
   * @param  test.execution.TestClass $group
   * @return void
   */
  public function enter($group) { }

  /**
   * Running a given test
   * 
   * @param  test.execution.TestClass $group
   * @param  test.execution.TestCase $test
   * @param  int $n
   * @return void
   */
  public function running($group, $test, $n) { }

  /**
   * Finished running a given test
   * 
   * @param  test.execution.TestClass $group
   * @param  test.execution.TestCase $test
   * @param  test.Outcome $outcome
   * @return void
   */
  public function finished($group, $test, $outcome) { }

  /**
   * Pass an entire group
   *
   * @param  test.execution.TestClass $group
   * @param  test.Outcome[] $results
   * @return void
   */
  public function pass($group, $results) { }

  /**
   * Fail an entire group
   *
   * @param  test.execution.TestClass $group
   * @param  test.Outcome[] $results
   * @return void
   */
  public function fail($group, $results) { }

  /**
   * Skip an entire group
   *
   * @param  test.execution.TestClass $group
   * @param  string $reason
   * @return void
   */
  public function skip($group, $reason) { }

  /**
   * Stop an entire group
   *
   * @param  test.execution.TestClass $group
   * @param  string $reason
   * @return void
   */
  public function stop($group, $reason) { }

  /**
   * Print out summary of test run
   *
   * @param  test.execution.Metrics $metrices
   * @param  float $overall
   * @param  [:test.Outcome] $failures
   * @return void
   */
  public function summary($metrics, $overall, $failures) { }
}