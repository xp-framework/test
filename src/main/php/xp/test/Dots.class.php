<?php namespace xp\test;

use util\cmd\Console;

/**
 * Dot output adds a "." to the console for every finished test,
 * wrapping the lines at 72 characters.
 */
class Dots extends Report {
  use Summary;

  private $offset;

  /** Called when test run starts */
  public function start($sources) {
    Console::write('[');
    $this->offset= 1;
  }

  /** Called when a test finished */
  public function finished($group, $test, $outcome) {
    Console::write('.');

    if (++$this->offset > 72) {
      Console::writeLine();
      $this->offset= 0;
    }
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
    Console::writeLine(']');
    Console::writeLine();

    $this->failures($failures);
    $this->metrics($metrics, $overall);
  }
}