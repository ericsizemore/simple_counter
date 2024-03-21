# Backward Compatibility (BC) Promise

I try to develop my libraries to be as backward compatible (BC) as possible. This file describes my backward compatibility promise, along with:

* Changes that are (or are not) allowed in minor or patch versions.
* Exceptions to this promise.
* How deprecations are handled.
* Tags and branches in the Git repository.
* ...etc.

Further restrictions (and/or allowances) might be added in the future.

| **Document Information** |            |
|:-------------------------|:-----------|
| **Effective Date**       | 03/18/2024 |
| **Last Updated**         | 03/21/2024 |
| **Version**              | 1.1.0      |

## Semantic Versioning

Simple Counter follows [Semantic Versioning](https://semver.org/) (`<major>.<minor>.<patch>`). In general, this means that the version number is incremented based on the type of changes made:

* Patch version for backward-compatible bug fixes.
* Minor version for backward-compatible additions.
* Major version for backward-incompatible changes.

#### Major Version Changes

Any backward-incompatible changes will only occur in a new major version. Users should expect to update their code to accommodate these changes.

#### Minor Version Changes

Minor versions may include new features and improvements but will maintain backward compatibility with previous minor versions within the same major release.

#### Patch Version Changes

Patch versions will only include backward-compatible bug fixes. Users can safely update without fear of breaking changes.

## Public API Stability:

* All public classes, interfaces, methods, and properties are subject to our backward compatibility promise.
* Any changes to the public API that are not backward-compatible will result in a new major version release.

### Exceptions

There are some exceptions which are not covered by the backward compatibility promise.

#### Security fixes and Critical bug fixes

Backward compatibility can be ignored in security bug fixes or critical bugs. In this case, all the incompatible changes are described in the [CHANGELOG](CHANGELOG.md).

#### Internal Code

* Classes, interfaces, properties and methods that are tagged with the @internal annotation.
* The classes located in the *\Tests\ namespace.

They are meant for internal use only and should not be accessed by your own code. They are subject to change or removal even in minor or patch versions.

#### Unreleased

* Generally, any code found within a `*-dev*` branch is not covered by this backward compatibility promise.
  * Normally, only major versions will be assigned their own `dev` branch.
* Unreleased versions will be tagged with `[Unreleased]` in the `CHANGELOG.md`.

See [Branches](#branches) for more information.

#### Named Arguments

[Named arguments](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments) are not covered by the backward compatibility (BC) promise. I may choose to rename method/function parameter names when necessary in order to improve the codebase.

## Deprecations

* Deprecated features will be clearly marked with @deprecated annotations or documented in the changelog.
* Deprecated features will continue to function without issues for at least two minor versions before being removed.
* Users are encouraged to migrate away from deprecated features to maintain compatibility with future releases.

## Composer Dependencies:

* I will strive to minimize direct dependencies on external libraries as much as possible.
* Any changes to dependencies that could impact users will be clearly documented in the changelog, with guidance on how to adapt.

## Changelogs

* I will strive to format the changelog to adhere to [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
* Changelogs will clearly indicate whether a release contains backward-incompatible changes, deprecated features, or backward-compatible bug fixes.

## Version Control

### Tags

Tags in Simple Counter's Git repository are immutable. I do not change published tags to point to a different revision, for example.

In very rare cases I may delete a tag in order to remove a broken release. The new release that fixes what was broken will always have a different tag than the one that was removed.

### Branches

Branches in Simple Counter's Git repository are private implementation details. For example, I delete branches for versions of Simple Counter that are no longer supported. In general:

* The `master` branch will contain the currently released major version (and `<minor>` or `<patch>` versions that fall under it).
* Previous major versions will be under a `<major>.x` branch.
* `Next` or development code of a new major version will be under a `<major>.x-dev` branch.

For example, let's say the latest released version of Simple Counter is 5.0.1, the last major version is 4, and the next major version is 6.

* The `master` branch will be all version 5 code, which includes: 5.0.1, 5.1.0, 5.2.0, etc.
* The previous major version, version 4, will be under `4.x` (which includes: 4.0.1, 4.1.0, 4.2.0, etc.).
* The `Next` or development code for a new major version, version 6, will be in `6.x-dev`.
  * Once v6.0.0 is offically released, it will be merged into `master` and the `6.x-dev` branch will be deleted.
  * Which then makes version 5 the previous major version, so it would have a new branch created for it: `5.x`

## Updates

I will keep the backward compatibility promise updated as Simple Counter evolves and new use cases emerge. The last updated date and version of this document under `Document Information` at the beginning of this file will be updated if any changes are made.

## Acknowledgements

This backward compatibility promise was highly inspired by, and borrows from, the BC promises of:

* [PHPUnit](https://phpunit.de/backward-compatibility.html)
* [Symfony](https://symfony.com/doc/current/contributing/code/bc.html)
