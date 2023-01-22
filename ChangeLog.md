 Tests - ChangeLog
=================

## ?.?.? / ????-??-??

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
