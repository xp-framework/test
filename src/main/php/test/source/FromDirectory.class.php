<?php namespace test\source;

use io\{Folder, Path};
use lang\{ClassLoader, FileSystemClassLoader, IllegalArgumentException};
use test\execution\TestClass;

class FromDirectory extends Source {
  private $folder;

  /** @param Folder|Path|string $arg */
  public function __construct($arg) {
    if ($arg instanceof Folder) {
      $path= $arg->getURI();
    } else {
      $path= realpath($arg).DIRECTORY_SEPARATOR;
    }

    foreach (ClassLoader::getLoaders() as $cl) {
      if ($cl instanceof FileSystemClassLoader && 0 === strncmp($cl->path, $path, strlen($cl->path))) {
        $this->folder= new Folder($path);
        return;
      }
    }

    throw new IllegalArgumentException($path.' is not in class path');
  }

  /** Returns the folder classes will be searched within */
  public function folder(): Folder { return $this->folder; }

  /**
   * Yields test classes in a given folder
   *
   * @param  ClassLoader $cl
   * @param  Folder $folder
   * @return iterable
   */
  private function testClassesIn($cl, $folder) {
    foreach ($folder->entries() as $entry) {
      if ($entry->isFolder()) {
        yield from $this->testClassesIn($cl, $entry->asFolder());
      } else if (strstr($entry->name(), 'Test')) {
        $uri= $entry->asURI();
        if ($loader= $cl->findUri($uri)) {
          yield $loader->loadUri($uri);
        }
      }
    }
  }

  /** @return iterable */
  public function groups() {
    foreach ($this->testClassesIn(ClassLoader::getDefault(), $this->folder) as $class) {
      if ($class->isInterface() || $class->isTrait() || $class->getModifiers() & MODIFIER_ABSTRACT) continue;
      yield new TestClass($class);
    }
  }

  /** @return string */
  public function toString() {
    return nameof($this).'<'.$this->folder->getURI().'>';
  }
}