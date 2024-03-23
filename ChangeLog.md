 Tests - ChangeLog
==================

## ?.?.? / ????-??-??

## 2.0.0 / 2024-03-23

* Made this library compatible with XP 12:
  - Dropped support for PHP < 7.4
  - Adopted nullable type syntax, array unpacking
  (@thekid)

## 1.5.2 / 2023-05-25

* Fixed *Call to undefined method Returning::name()* - @thekid

## 1.5.1 / 2023-05-18

* Fixed support for cleaning up errors with `xp::gc()` inside tests
  (@thekid)

## 1.5.0 / 2023-05-18

* Merged PR #22: Make warnings raised during test execution fail these
  tests, adding back a previously unported feature.
  (@thekid)
* Merged PR #23: Implement `Assert::matches()`, which checks the given
  string value matches a regular expression.
  (@thekid)

## 1.4.0 / 2023-04-23

* Merged PR #21: Add JSON report, producing test reports in JSON format
  compatible with Mocha, see https://mochajs.org/#json. This can be used
  to integrate with https://github.com/marketplace/actions/test-reporter,
  see https://github.com/xp-framework/test/actions/runs/4777443690 at the
  very bottom of the page.
  (@thekid)
* Merged PR #20: Extract output into a dedicated classes, reports.
  - The command line option `-r` select report to use
  - The default report is called *Grouped*
  - A minimalistic report called *Dots* prints a `.` for each test
  - Multiple reports can be used
  - Report arguments can be passed via `-r [Report],[Arg1],[Arg2]`
  (@thekid)

## 1.3.1 / 2023-04-15

* Merged PR #19: Catch exceptions from test setup and make tests fail,
  fixing issue #16
  (@thekid)

## 1.3.0 / 2023-04-15

* Allowed omitting the reason in `Ignore` annotations. See issue #16
  (@thekid)
* Fixed issue #17: Argument 2 ($cause) must be of type lang\Throwable,
  null given
  (@thekid)

## 1.2.0 / 2023-02-17

* Merged PR #15: Add assertion helpers for thrown exceptions - @thekid

## 1.1.0 / 2023-02-11

* Merged PR #14: Declare `Test`, `After` and `Before` annotations in order
  to prevent repeated class loading queries, increasing performance
  (@thekid)
* Added type-hints to `Expect` annotation so that incorrect usage surfaces
  early along instead of causing confusing errors somewhere downstream
  (@thekid)

## 1.0.0 / 2023-02-09

This first major release serves implements the most common testing
usecases, able to replace the *xp-framework/unittest* library. From
here on, projects can start to migrate without having to follow the
frequent version jumps in a 0.x release.

* Merged PR #12: Fix errors occuring during setup not being correctly
  reported (e.g. when exceptions are raised inside `#[Before]`).
  (@thekid)
* Merged PR #11: Only verify parameterized test cases' prerequisites
  once, resulting in a small performance improvement. See issue #10
  (@thekid)
* Merged PR #9: Implement checking for expected exceptions' messages
  (@thekid)
* Merged PR #7: Implement passing command line arguments via `#[Args]`
  (@thekid)
* Added overall time to result metrics, supplementing the currently
  reported time spent executing the test cases only
  (@thekid)
* Fixed issue #6: Handle setup errors, showing a `STOP` marker and the
  exception causing it.
  (@thekid)
* Merged PR #5: Pass `[class.Name]::[pattern]` to execute only test cases
  matching the given pattern
  (@thekid)
* Merged PR #4: Generalize values annotation into provider handling
  (@thekid)
* Renamed `map()` -> `mappedBy()` to be consisten with rest of naming
  used inside the *Assertable* fluent interface
  (@thekid)
* Merged PR #3: Add map() to transform the value before comparison
  (@thekid)
* Merged PR #1: Implement test prerequisites - @thekid
