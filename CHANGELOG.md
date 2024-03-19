# CHANGELOG
A not so exhaustive list of changes for each release.

For a more detailed listing of changes between each version, 
you can use the following url: https://github.com/ericsizemore/simple_counter/compare/v5.0.1...v6.0.0. 

Simply replace the version numbers depending on which set of changes you wish to see.

## 6.0.0 (work in progress)

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

### Added

  * `Adapter\JsonFileAdapter` which is default, and currently only, available adapter.
  * `Adapter\FormatterTrait` which will be used by Adapters to handle formatting the count display.
  * `Configuration\JsonFileConfiguration` which is the default, and currently only, available Adapter configuration.
    * Used by `Adapter\JsonFileAdapter`
  * `Interface\CounterInterface` which defines methods that must be implemented in each adapter.
  * `Interface\ConfigurationInterface` which defines methods that must be implemented in each Adapter configuration.
  * `symonfy/options-resolver` dependency added to handle counter options.
  * `scripts/convertFiles.php` which can be used by those moving from version <5 of the library, to convert their `*.txt` counter/ips files to json.

### Removed

  * Removed `0-9` `.gif` images in `counter/images/`. *See above, replaced with new icons*


## 5.0.1 (2024-03-04)

  * Updated `Esi\Utility` to `2.0.0`

## 5.0.0 (2024-01-11)

NOTE: Not backwards compatible with prior SimpleCounter versions.

### Changed

  * Bumped PHP version requirement to 8.2
  * Restructured to be installable with composer
  * Esi\SimpleCounter\Counter completely refactored
    * User configurable options are no longer class constants.
      * They are now normal class vars with protected visibility
      * Must be set upon instantiation with `getInstance($options)`. See README.md for more information.
  * Split `readWriteFile` into `read` and `write`.

### Added

  * Added `checkLogFiles` and `checkDirectories` as strictly helper functions, that check the log and image directories and log files on instantiation.
  * Added PHP-CS-Fixer and Rector as dev dependencies.
  * Added PHPStan for static analysis
  * Added PHPUnit for unit testing.
  * Implements #5 (https://github.com/ericsizemore/simple_counter/issues/5)

### Removed

  * None


### Pre-5.0

  * Unfortunately, no changelog was kept prior to v5.0
