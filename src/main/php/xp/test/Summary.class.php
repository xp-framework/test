<?php namespace xp\test;

use util\cmd\Console;

trait Summary {
  const COUNTS= [
    'success' => "\033[32m%d succeeded\033[0m",
    'failure' => "\033[31m%d failed\033[0m",
    'skipped' => "\033[33m%d skipped\033[0m",
  ];

  /** Prints all failures including reason and stack trace */
  private function failures($failures) {
    foreach ($failures as $location => $failure) {
      Console::writeLinef(
        "\033[31m⨯ %s\033[0m\n  \033[37;1m%s\033[0m\n%s\n",
        $location,
        $failure->reason,
        $failure->trace('    ')
      );
    }
  }

  /** Summarizes metrics */
  private function metrics($metrics, $overall) {
    $summary= [];
    foreach ($metrics->count as $metric => $count) {
      $count && $summary[]= sprintf(self::COUNTS[$metric], $count);
    }

    Console::writeLinef(
      "\033[37mTest cases:\033[0m  %s",
      implode(', ', $summary)
    );
    Console::writeLinef(
      "\033[37mMemory used:\033[0m %.2f kB (%.2f kB peak)",
      $metrics->memoryUsed / 1000,
      $metrics->peakMemoryUsed / 1000
    );
    Console::writeLinef(
      "\033[37mTime taken:\033[0m  %.3f seconds (%.3f seconds overall)",
      $metrics->elapsed,
      $overall
    );
  }
}