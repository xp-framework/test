<?php namespace test;

interface Prerequisite {

  /**
   * Describes requirement
   *
   * @param  bool $positive
   * @return string
   */
  public function requirement($positive= true);

  /** @return bool */
  public function verify();

}