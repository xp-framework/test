<?php namespace test\assert;

use lang\FormatException;

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

  protected $component, $range;

  /**
   * Creates a new required version condition
   *
   * @param  string $component
   * @param  string $range
   */
  public function __construct($component, $range) {
    $this->component= $component;
    $this->range= $range;
  }

  private function normalize($version, $offset= 0) {
    if (preg_match(self::PATTERN, $offset ? substr($version, $offset) : $version, $matches)) {
      return sprintf(
        '%d.%d.%d%s',
        $matches[1],
        isset($matches[3]) ? $matches[3] : 0,
        isset($matches[5]) ? $matches[5] : 0,
        isset($matches[6]) ? $matches[6] : ''
      );
    }
    throw new FormatException('Cannot normalize "'.$version.'"');
  }

  public function matches($value) {
    if ('^' === $this->range[0]) {
      $next= '0' === $this->range[1]
        ? '0.'.(($this->range[3] ?? 0) + 1).substr($this->range, 4)
        : ($this->range[1] + 1).substr($this->range, 2)
      ;
      return (
        version_compare($value, $this->normalize($this->range, 1), '>=') &&
        version_compare($value, $this->normalize($next), '<')
      );
    } else if ('>' === $this->range[0]) {
      return '=' === $this->range[1]
        ? version_compare($value, $this->normalize($this->range, 2), '>=')
        : version_compare($value, $this->normalize($this->range, 1), '>')
      ;
    } else if ('<' === $this->range[0]) {
      return '=' === $this->range[1]
        ? version_compare($value, $this->normalize($this->range, 2), '<=')
        : version_compare($value, $this->normalize($this->range, 1), '<')
      ;
    }

    // Otherwise, perform exact match
    return version_compare($value, $this->normalize($this->range), '=');
  }

  public function describe($value, $positive) {
    return sprintf(
      '%s %s %s %s',
      $this->component,
      self::stringOf($value),
      $positive ? 'meets version requirement' : 'does not meet version requirement',
      $this->range
    );
  }
}