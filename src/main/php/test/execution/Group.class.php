<?php namespace test\execution;

abstract class Group {

  /**
   * Returns this group's name
   *
   * @return string
   */
  public abstract function name();

  /**
   * Yields prerequisites for the tests in this group. Defaults to no
   * prerequisites, overwrite in subclasses!
   *
   * @return iterable
   */
  public function prerequisites() { return []; }

  /**
   * Yields tests in this group
   *
   * @param  array<string> $arguments
   * @return iterable
   * @throws GroupFailed
   */
  public abstract function tests($arguments= []);

}