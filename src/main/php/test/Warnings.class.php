<?php namespace test;

use lang\{Throwable, StackTraceElement};

class Warnings extends Throwable {
  private static $LEVELS= [
    E_ERROR             => 'E_ERROR',
    E_WARNING           => 'E_WARNING',
    E_PARSE             => 'E_PARSE',
    E_NOTICE            => 'E_NOTICE',
    E_CORE_ERROR        => 'E_CORE_ERROR',
    E_CORE_WARNING      => 'E_CORE_WARNING',
    E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
    E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
    E_USER_ERROR        => 'E_USER_ERROR',
    E_USER_WARNING      => 'E_USER_WARNING',
    E_USER_NOTICE       => 'E_USER_NOTICE',
    E_STRICT            => 'E_STRICT',
    E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
    E_DEPRECATED        => 'E_DEPRECATED',
    E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
  ];

  /** Creates a new warnings instance */
  public function __construct(array $warnings) {
    $message= '';
    foreach ($warnings as $warning) {
      $message.= ', '.$warning[1];
      $this->trace[]= new StackTraceElement(
        $warning[2],
        null,
        '@error',
        $warning[3],
        [],
        (self::$LEVELS[$warning[0]] ?? 'E_UNKNOWN('.$warning[0].')').': '.$warning[1]
      );
    }
    parent::__construct(substr($message, 2));
  }
}