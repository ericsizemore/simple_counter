# Backward Compatibility (BC) Promise

I try to develop my libraries to be as backward compatible (BC) as possible. This file describes my backward compatibility promise, along with:

* Changes that are (or are not) allowed in minor or patch versions.
* Exceptions to this promise.
* How deprecations are handled.
* Tags and branches in the Git repository.
* PHP version support policy.
* Experimental features handling.
* ...etc.

Further restrictions (and/or allowances) might be added in the future.

| **Document Information** |            |
|:-------------------------|:-----------|
| **Effective Date**       | 03/18/2024 |
| **Last Updated**         | 02/13/2026 |
| **Version**              | 2.0.0      |

## PHP Version Support

* PHP version support changes will only occur in major versions.
* Each major version will clearly state its minimum PHP version requirement.
* Security fixes may require newer point releases within a supported PHP version.
* End-of-life PHP versions will not receive support.

## Semantic Versioning

This project follows [Semantic Versioning](https://semver.org/) (`<major>.<minor>.<patch>`). In general, this means that the version number is incremented based on the type of changes made:

### Major Version Changes (BC breaks)
* Removing or renaming public methods.
* Adding required parameters to methods.
* Changing method signatures.
* Changing return types to incompatible types.
* Removing public properties.
* Changing class/interface hierarchies.
* Dropping support for PHP versions.
* Examples:
  ```php
  // BC Break: Adding required parameters
  // Before
  public function process(array $data): bool {}
  // After
  public function process(array $data, bool $required): bool {}

  // BC Break: Adding return type
  // Before
  public function getData() {}
  // After
  public function getData(): array {}

  // BC Break: Adding nullable type
  // Before
  public function getData(): array {}
  // After
  public function getData(): ?array {}

  // BC Break: Adding union type
  // Before
  public function getData(): array {}
  // After
  public function getData(): array|Collection {}
  ```

### Minor Version Changes (allowed)
* Adding new methods.
* Adding optional parameters.
* Adding new classes/interfaces.
* Adding new constants.
* Deprecating features (with notice).
* Adding stricter type hints to parameters.
* Examples:
  ```php
  // Allowed: Adding optional parameter
  // Before
  public function process(array $data): bool {}
  // After
  public function process(array $data, ?bool $optional = null): bool {}

  // Allowed: Adding new method
  class Existing
  {
      public function newFeature(): void {}
  }
  ```

### Patch Version Changes (allowed)
* Bug fixes that don't change interfaces.
* Performance improvements.
* Documentation updates.
* Security fixes (see exceptions.

## Public API Stability

* All public classes, interfaces, methods, and properties are subject to our backward compatibility promise.
* Any changes to the public API that are not backward-compatible will result in a new major version release.
* Return type changes:
  * Adding a return type to a method that didn't have one is considered a BC break.
  * Changing from a specific type to a union type is considered a BC break.
  * Adding a nullable type is considered a BC break.

### Exception Handling
* Adding new exceptions for edge cases is not considered a BC break.
* Changing the exception hierarchy is considered a BC break.
* Removing thrown exceptions is considered a BC break.
* Examples of allowed changes:
  ```php
  // Allowed: Adding new exception for edge case
  public function process(array $data): bool
  {
      if ($edge_case) {
          throw new NewSpecificException();
      }
  }
  ```

### Experimental Features
Features marked as @experimental are not covered by the BC promise and may:
* Change functionality in minor versions.
* Be removed without major version bump.
* Have different BC rules.

Usage of experimental features should be considered unstable and not suitable for production environments.

## Exceptions to BC Promise

There are some exceptions to the backward compatible promise:

### Security Fixes and Critical Bug Fixes
* Backward compatibility can be ignored in security bug fixes or critical bugs.
* All incompatible changes are described in the [CHANGELOG](CHANGELOG.md).
* Security fixes may require newer point releases of PHP.

### Internal Code
* Classes, interfaces, properties and methods tagged with the @internal annotation.
* The classes located in the *\Tests\ namespace.
* They are meant for internal use only and should not be accessed by your own code.
* They are subject to change or removal even in minor or patch versions.

### Unreleased Code
* Any code found within a `*-dev*` branch is not covered by this backward compatibility promise.
* Unreleased versions will be tagged with `[Unreleased]` in the `CHANGELOG.md`.

### Named Arguments
* [Named arguments](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments) are not covered by the backward compatibility promise.
* Parameter names may be renamed to improve code clarity.

## Testing Requirements

* All new features must include appropriate unit tests.
* Minimum test coverage requirement: 80%
* BC breaking changes must include:
  * Unit tests verifying the new behavior.
  * Integration tests where appropriate.
  * Documentation of migration path.

## Deprecations

* Deprecated features will be clearly marked with @deprecated annotations.
* Deprecation notices will include:
  * Reason for deprecation.
  * Suggested alternative.
  * Planned removal version.
* Deprecated features will continue to function for at least two minor versions before removal.
* Users are encouraged to migrate away from deprecated features to maintain compatibility.

## Composer Dependencies

* Direct dependencies will be minimized where possible.
* Dependencies will maintain semver compliance.
* Changes to dependencies will be documented in the changelog.
* Major version bumps of dependencies will trigger a major version bump of this package.

## Changelogs

* Changelogs will adhere to [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
* Each entry will clearly indicate:
  * Type of change (Added, Changed, Deprecated, Removed, Fixed, Security).
  * Whether it contains BC breaks.
  * Migration instructions if necessary.

## Version Control

### Tags
* Tags are immutable.
* Tags will not be changed to point to different revisions.
* Broken release tags may be deleted and replaced with new version tags.

### Branches

Branches are private implementation details. For example:

* `master` branch contains the current major version.
* Previous major versions use `<major>.x` branches
* Development of new major versions use `<major>.x-dev` branches.

#### Branch Support Timeline
* Master branch: Full support, all updates.
* Previous major version: Bug and security fixes for 12 months.
* Older versions: Security fixes only for 6 months.

Example scenario with version 3.0.0 as latest release:
* `master`: Version 3.x code (3.0.1, 3.1.0, etc.).
* `2.x`: Previous major version.
* `4.x-dev`: Development of next major version.

## Updates

I will keep the backward compatibility promise updated as this project evolves and new use cases emerge.
The last updated date and version of this document under `Document Information` at the beginning of this file will be updated if any changes are made.

## Acknowledgements

This backward compatibility promise was highly inspired by, and borrows from, the BC promises of:

* [PHPUnit](https://phpunit.de/backward-compatibility.html)
* [Symfony](https://symfony.com/doc/current/contributing/code/bc.html)
