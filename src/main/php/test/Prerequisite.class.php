<?php namespace test;

interface Prerequisite {

  /** @return iterable */
  public function assertions();
}