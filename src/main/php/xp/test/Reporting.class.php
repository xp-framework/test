<?php namespace xp\test;

class Reporting extends Report {
  private $delegates= [];

  /** Returns whether reporting has been delegated */
  public function delegated(): bool { return !empty($this->delegates); }

  /** Adds a delegate */
  public function add(parent $delegate): self {
    $this->delegates[]= $delegate;
    return $this;
  }

  /** Called when the test run starts */
  public function start($sources) {
    foreach ($this->delegates as $delegate) {
      $delegate->start($sources);
    }
  }

  /** Called when entering a group */
  public function enter($group) {
    foreach ($this->delegates as $delegate) {
      $delegate->enter($group);
    }
  }

  /** Running a given test */
  public function running($group, $test, $n) {
    foreach ($this->delegates as $delegate) {
      $delegate->running($group, $test, $n);
    }
  }

  /** Finished running a given test */
  public function finished($group, $test, $outcome) {
    foreach ($this->delegates as $delegate) {
      $delegate->finished($group, $test, $outcome);
    }
  }

  /** Pass an entire group */
  public function pass($group, $results) {
    foreach ($this->delegates as $delegate) {
      $delegate->pass($group, $results);
    }
  }

  /** Fail an entire group */
  public function fail($group, $results) {
    foreach ($this->delegates as $delegate) {
      $delegate->fail($group, $results);
    }
  }

  /** Skip an entire group */
  public function skip($group, $reason) {
    foreach ($this->delegates as $delegate) {
      $delegate->skip($group, $reason);
    }
  }

  /** Stop an entire group */
  public function stop($group, $reason) {
    foreach ($this->delegates as $delegate) {
      $delegate->stop($group, $reason);
    }
  }

  /** Print out summary of test run */
  public function summary($metrics, $overall, $failures) {
    foreach ($this->delegates as $delegate) {
      $delegate->summary($metrics, $overall, $failures);
    }
  }
}