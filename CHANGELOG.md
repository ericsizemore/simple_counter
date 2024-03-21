# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

  * `Adapter\FlatfileAdapter` which is default, and currently only, available adapter.
  * `Adapter\FormatterTrait` which will be used by Adapters to handle formatting the count display.
  * `Configuration\FlatfileConfiguration` which is the default, and currently only, available Adapter configuration.
    * Used by `Adapter\FlatfileAdapter`
  * `Interface\CounterInterface` which defines methods that must be implemented in each adapter.
  * `Interface\ConfigurationInterface` which defines methods that must be implemented in each Adapter configuration.
  * `symonfy/options-resolver` dependency added to handle counter options.
  * `scripts/convertFiles.php` which can be used by those moving from version <5 of the library, to convert their `*.txt` counter/ips files to json.

### Changed

  * Now licensed under the MIT license.
  * Complete rewrite from the ground up. Breaking changes, and not backwards compatible with prior versions.
  * The files used in the `counter/logs/` directory are no longer plain *.txt files.
    * jSON is now being used to handle the log data.
  * New icons for the default image set (0-9 'png' images found in `counter/images/`).
    * Licensed under the [CC BY 4.0 DEED license](https://creativecommons.org/licenses/by/4.0/)
    * Designed by [StreamlineHQ](https://www.streamlinehq.com/freebies/typeface)
  * `Esi\SimpleCounter\Counter` is now just a wrapper for one of the `*Adapter` classes found in `src/Adapter/`.
  * Unit tests completely rewritten.

### Removed

  * Removed `0-9` `.gif` images in `counter/images/`. *See above, replaced with new icons*


## [5.0.1] - 2024-03-04

### CHANGED

  * Updated `Esi\Utility` to `2.0.0`


## [5.0.0] - 2024-01-11

NOTE: Not backwards compatible with prior SimpleCounter versions.

### Added

  * Added `checkLogFiles` and `checkDirectories` as strictly helper functions, that check the log and image directories and log files on instantiation.
  * Added PHP-CS-Fixer and Rector as dev dependencies.
  * Added PHPStan for static analysis
  * Added PHPUnit for unit testing.
  * Implements #5 (https://github.com/ericsizemore/simple_counter/issues/5)

### Changed

  * Bumped PHP version requirement to 8.2
  * Restructured to be installable with composer
  * Esi\SimpleCounter\Counter completely refactored
    * User configurable options are no longer class constants.
      * They are now normal class vars with protected visibility
      * Must be set upon instantiation with `getInstance($options)`. See README.md for more information.
  * Split `readWriteFile` into `read` and `write`.


### Pre-5.0

  * Unfortunately, no changelog was kept prior to v5.0


[unreleased]: https://github.com/ericsizemore/simple_counter/compare/5.0.x...6.x-dev
[5.0.1]: https://github.com/ericsizemore/simple_counter/compare/v5.0.0...v5.0.1
[5.0.0]: https://github.com/ericsizemore/simple_counter/compare/4.0.x...v5.0.0