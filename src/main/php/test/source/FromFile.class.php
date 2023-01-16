<?php namespace test\source;

use io\{File, Path};
use lang\reflection\Type;
use lang\{ClassLoader, Reflection, IllegalArgumentException};
use test\TestClass;

class FromFile {
  private $type;

  /** @param File|Path|string $arg */
  public function __construct($arg) {
    if ($arg instanceof File) {
      $uri= $arg->getURI();
    } else {
      $uri= realpath($arg);
    }

    if ($loader= ClassLoader::getDefault()->findUri($uri)) {
      $this->type= Reflection::type($loader->loadUri($uri));
      return;
    }

    throw new IllegalArgumentException($arg.' is not in class path');
  }

  /** Returns the type discovered from the file */
  public function type(): Type { return $this->type; }

  /** @return iterable */
  public function groups() {
    yield new TestClass($this->type);
  }
}