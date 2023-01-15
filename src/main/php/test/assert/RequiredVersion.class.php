<?php namespace test\assert;

use lang\FormatException;
use util\Objects;

/**
 * Test component for a given version
 *
 * | Specifier         | Meaning |
 * | ----------------- | ------- |
 * | `1.0.0`, `1.0`    | Exact version match required. |
 * | `>1.2`, `>=1.2.3` | A greater than / great than or equal to constraint. |
 * | `<1.2`, `<=1.2.3` | A less than / less than or equal to constraint. |
 * | `^1.2`            | The next significant release, meaning `>=1.2,<2.0`, so any 1.x version is OK. |
 *
 * @see   https://getcomposer.org/doc/articles/versions.md
 * @test  test.unittest.RequiredVersionTest
 */
class RequiredVersion extends Condition {
  const PATTERN= '/^([0-9]+)(\.([0-9]+))(\.([0-9]+))?(.+)?$/';

  protected $component, $comparison;

  /**
   * Creates a new required version condition
   *
   * @param  string $component
   * @param  string $range
   * @throws FormatException if range is not parseable
   */
  public function __construct($component, $range) {
    $this->component= $component;

    if ('^' === $range[0]) {
      $next= '0' === $range[1]
        ? '0.'.(($range[3] ?? 0) + 1).substr($range, 4)
        : ($range[1] + 1).'.0.0'
      ;
      $this->comparison= [[$this->normalize($range, 1), '>='], [$this->normalize($next), '<']];
    } else if ('>' === $range[0]) {
      $this->comparison= ['=' === $range[1]
        ? [$this->normalize($range, 2), '>=']
        : [$this->normalize($range, 1), '>']
      ];
    } else if ('<' === $range[0]) {
      $this->comparison= ['=' === $range[1]
        ? [$this->normalize($range, 2), '<=']
        : [$this->normalize($range, 1), '<']
      ];
    } else {
      $this->comparison= [[$this->normalize($range), '=']];
    }
  }

  private function normalize($version, $offset= 0) {
    if (preg_match(self::PATTERN, $offset ? substr($version, $offset) : $version, $matches)) {
      return sprintf(
        '%d.%d.%d%s',
        $matches[1],
        $matches[3] ?? 0,
        $matches[5] ?? 0,
        $matches[6] ?? ''
      );
    }
    throw new FormatException('Cannot normalize "'.$version.'"');
  }

  public function matches($value) {
    foreach ($this->comparison as $compare) {
      if (!version_compare($this->normalize($value), ...$compare)) return false;
    }
    return true;
  }

  public function describe($value, $positive) {
    return sprintf(
      '%s %s %s %s',
      $this->component,
      self::stringOf($value),
      $positive ? 'meets version requirement' : 'does not meet version requirement',
      Objects::stringOf($this->comparison)
    );
  }
}