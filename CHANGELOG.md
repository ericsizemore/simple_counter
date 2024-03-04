## CHANGELOG
A not so exhaustive list of changes for each release.

For a more detailed listing of changes between each version, 
you can use the following url: https://github.com/ericsizemore/simple_counter/compare/v4.0.6...v5.0.0. 

Simply replace the version numbers depending on which set of changes you wish to see.

### 5.0.1 (2024-03-04)

  * Updated `Esi\Utility` to `2.0.0`

### 5.0.0 (2024-01-11)
  * NOTE: Not backwards compatible with prior SimpleCounter versions.
  * Bumped PHP version requirement to 8.2
  * Restructured to be installable with composer
  * Esi\SimpleCounter\Counter completely refactored
    * User configurable options are no longer class constants.
      * They are now normal class vars with protected visibility
      * Must be set upon instantiation with `getInstance($options)`. See README.md for more information.
  * Split `readWriteFile` into `read` and `write`.
  * Added `checkLogFiles` and `checkDirectories` as strictly helper functions, that check the log and image directories and log files on instantiation.
  * Added PHP-CS-Fixer and Rector as dev dependencies.
  * Added PHPStan for static analysis
  * Added PHPUnit for unit testing.
  * Implements #5 (https://github.com/ericsizemore/simple_counter/issues/5)

### Pre-5.0
  * Unfortunately, no changelog was kept prior to v5.0