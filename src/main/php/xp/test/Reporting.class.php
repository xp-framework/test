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

  public function start($sources) {
    foreach ($this->delegates as $delegate) {
      $delegate->start($sources);
    }
  }

  public function enter($group) {
    foreach ($this->delegates as $delegate) {
      $delegate->enter($group);
    }
  }

  public function running($group, $test, $n) {
    foreach ($this->delegates as $delegate) {
      $delegate->running($group, $test, $n);
    }
  }

  public function finished($group, $test, $outcome, $elapsed) {
    foreach ($this->delegates as $delegate) {
      $delegate->finished($group, $test, $outcome, $elapsed);
    }
  }

  public function pass($group, $results) {
    foreach ($this->delegates as $delegate) {
      $delegate->pass($group, $results);
    }
  }

  public function fail($group, $results) {
    foreach ($this->delegates as $delegate) {
      $delegate->fail($group, $results);
    }
  }

  public function skip($group, $reason) {
    foreach ($this->delegates as $delegate) {
      $delegate->skip($group, $reason);
    }
  }

  public function stop($group, $reason) {
    foreach ($this->delegates as $delegate) {
      $delegate->stop($group, $reason);
    }
  }

  public function summary($metrics, $overall, $failures) {
    foreach ($this->delegates as $delegate) {
      $delegate->summary($metrics, $overall, $failures);
    }
  }
}