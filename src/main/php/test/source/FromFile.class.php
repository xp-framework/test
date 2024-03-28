<?php namespace test\source;

use io\{File, Path};
use lang\reflection\Type;
use lang\{ClassLoader, Reflection, IllegalArgumentException};
use test\execution\TestClass;

class FromFile extends Source {
  private $path, $type;

  /** @param File|Path|string $arg */
  public function __construct($arg) {
    if ($arg instanceof File) {
      $this->path= $arg->getURI();
    } else {
      $this->path= realpath($arg);
    }

    if ($loader= ClassLoader::getDefault()->findUri($this->path)) {
      $this->type= Reflection::type($loader->loadUri($this->path));
      return;
    }

    throw new IllegalArgumentException($this->path.' is not in class path');
  }

  /** Returns the type discovered from the file */
  public function type(): Type { return $this->type; }

  /** @return iterable */
  public function groups() {
    yield new TestClass($this->type);
  }

  /** @return string */
  public function toString() {
    return nameof($this).'<'.$this->path.'>';
  }
}