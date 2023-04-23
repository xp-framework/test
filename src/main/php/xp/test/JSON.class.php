<?php namespace xp\test;

use io\streams\{FileOutputStream, ConsoleOutputStream};
use util\{Date, TimeZone};

/**
 * JSON report using the same output as Mocha
 *
 * @see   https://mochajs.org/#json
 */
class JSON extends Report {
  const SLOW= 0.075;
  const DATES= 'Y-m-d\TH:i:s.v\Z';

  private $out, $start;
  private $results= [
    'success' => [],
    'failure' => [],
    'skipped' => [],
  ];

  /**
   * Creates a new JSON report. Omitting or supplying "-" as the output
   * will write to standard output. Otherwise, a file with the given name
   * is created.
   *
   * @param  string $output
   */
  public function __construct($output= '-') {
    $this->out= '-' === $output ? new ConsoleOutputStream(STDOUT) : new FileOutputStream($output);
  }

  /** Called when test run starts */
  public function start($sources) {
    $this->start= Date::now();
  }

  /** Called when a test finished */
  public function finished($group, $test, $outcome) {
    if ($outcome->elapsed > self::SLOW) {
      $speed= 'slow';
    } else if ($outcome->elapsed > self::SLOW / 2) {
      $speed= 'medium';
    } else {
      $speed= 'fast';
    }

    if ('failure' === $outcome->kind()) {
      $err= [
        'name'    => $outcome->cause ? nameof($outcome->cause) : 'No error',
        'message' => $outcome->reason,
        'stack'   => $outcome->trace(),
      ];
    } else {
      $err= (object)[];
    }

    $this->results[$outcome->kind()][]= [
      'title'        => $outcome->test,
      'fullTitle'    => $group->name().' '.$outcome->test,
      'file'         => $group->declaringFile(),
      'duration'     => (int)($outcome->elapsed * 1000),
      'currentRetry' => 0,
      'speed'        => $speed,
      'err'          => $err,
    ];
  }

  /** Called when the test run completed */
  public function summary($metrics, $overall, $failures) {
    $end= Date::now();
    $utc= TimeZone::getByName('UTC');
    $report= [
      'stats' => [
        'suites'   => 1,
        'tests'    => array_sum($metrics->count),
        'passes'   => $metrics->count['success'],
        'pending'  => $metrics->count['skipped'],
        'failures' => $metrics->count['failure'],
        'start'    => $utc->translate($this->start)->toString(self::DATES),
        'end'      => $utc->translate($end)->toString(self::DATES),
        'duration' => (int)($metrics->elapsed * 1000),
      ],
      'tests'    => [],
      'passes'   => $this->results['success'],
      'pending'  => $this->results['skipped'],
      'failures' => $this->results['failure'],
    ];

    // Reference all results inside the tests key
    foreach ($this->results as $kind => $results) {
      foreach ($results as $result) {
        $report['tests'][]= $result;
      }
    }

    $this->out->write(json_encode($report));
    $this->out->close();
  }
}