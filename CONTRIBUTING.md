# Contributing to Simple Counter

#### Important Note

The contributing guidelines for this project are heavily inspired by, and borrowed from, the [contributing guidelines](https://github.com/sebastianbergmann/phpunit/main/.github/CONTRIBUTING.md) of PHPUnit.

## Welcome!

This project accepts contributions of code and documentation from the community. 
These contributions can be made in the form of Issues or [Pull Requests](http://help.github.com/send-pull-requests/) on the [Simple Counter repository](https://github.com/ericsizemore/simple_counter).

Here are some examples how you can contribute:

* [Report a bug](https://github.com/ericsizemore/simple_counter/issues/new?labels=bug,unverified&template=1-bug_report.yml)
* [Propose a new feature](https://github.com/ericsizemore/simple_counter/issues/new?labels=enhancement,unverified&template=2-feature_request.yml)
* [Send a pull request](https://github.com/ericsizemore/simple_counter/pulls)

I look forward to your contributions! 

## Code of Conduct

Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md). By participating in this project you agree to abide by its terms.

## Any contributions you make will be under the MIT License

When you submit code changes, your submissions are understood to be under the same [MIT License](https://github.com/ericsizemore/simple_counter/blob/master/LICENSE.md) that covers the project. By contributing to this project, you agree that your contributions will be licensed under its MIT License.

## Workflow for Pull Requests

- Fork the repository.
- Create your branch from `master` if you plan to implement new functionality or change existing code significantly.
  - Create your branch from the oldest branch that is affected by the bug if you plan to fix a bug.
  - Pull requests for bug fixes must be made for the oldest branch that is [supported](https://github.com/ericsizemore/simple_counter/blob/master/SECURITY.md).
- Implement your change and add tests for it.
- Ensure the test suite passes.
- Ensure the code complies with our coding guidelines (see below).
- Create the pull request.

Please make sure you have [set up your username and email address](https://git-scm.com/book/en/v2/Getting-Started-First-Time-Git-Setup) for use with Git.
You are encouraged to [sign your Git commits with your GPG key](https://docs.github.com/en/github/authenticating-to-github/signing-commits).

[Backwards compatibility](https://github.com/ericsizemore/simple_counter/blob/master/backward-compatibility.md) breaks in this project are being kept to an absolute minimum. Please take this into account when proposing changes.

## Guidelines

Before we look into how, here are the guidelines. If your Pull Requests fail to pass these guidelines it will be declined, and you will need to re-submit when you’ve made the changes. This might sound a bit tough, but it is required for me to maintain quality of the code-base.

### Git Checkout

The following commands can be used to perform the initial checkout of Simple Counter:

```bash
$ git clone https://github.com/ericsizemore/simple_counter.git
$ cd simple_counter
```

Install Simple Counter's dependencies using [Composer](https://getcomposer.org/):

```bash
$ composer install
```

### PHP Style and Coding Guidelines

Please ensure all new contributions match the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style guide.

This project attempts to adhere to PHPStan level 9 with strict rules and bleeding edge. Please ensure any contributions do as well.

This project comes with configuration files for various tools that are used within the development workflow of this project. Please understand that I will not accept a pull request when its changes violate this project's coding guidelines.

#### Coverage Check

No configuration is needed for [PHPUnit Coverage Check](https://github.com/ericsizemore/phpunit-coverage-check). You can use PHPUnit Coverage Check to check the coverage percentage in the codebase after your changes. I try to stick to 100% line coverage.

```bash
$ composer run-script coverage
```

#### PHPStan

The configuration file can be found at `phpstan.neon`, in the repository, for [PHPStan](https://phpstan.org/). You can use PHPStan to perform static analysis:

```bash
$ composer run-script phpstan
```

#### PHP-CS-Fixer

The configuration file can be found at `.php-cs-fixer.dist.php`, in the repository, for [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer). You can use PHP-CS-Fixer to (re)format your source code for compliance with this project's coding guidelines:

```bash
$ composer run-script cs:fix
```

#### Unit tests (via PHPUnit)

The configuration file can be found at `phpunit.xml`, in the repository, for [PHPUnit](https://phpunit.de/index.html). You can run the test suite with:

```bash
$ composer run-script test
```

### Documentation

If you change anything that requires a change to documentation then you will need to add it. New methods, parameters, changing default values, adding constants, etc. are all things that will require a change to documentation. The change-log must also be updated for every change. Also, PHPDoc blocks must be maintained.

#### Documenting functions/variables (PHPDoc)

Please ensure all new contributions adhere to:

* [PSR-5 PHPDoc](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md)
* [PSR-19 PHPDoc Tags](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md)

when documenting new functions, or changing existing documentation.

#### Changelog Entries

If adding new changelog entries, please ensure they adhere to the [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) format as much as possible.
