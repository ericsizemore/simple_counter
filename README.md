# Simple Counter - A simple web hit counter.

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fericsizemore%2Fsimple_counter.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Fericsizemore%2Fsimple_counter?ref=badge_shield)
[![Build Status](https://scrutinizer-ci.com/g/ericsizemore/simple_counter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/simple_counter/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/ericsizemore/simple_counter/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/simple_counter/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ericsizemore/simple_counter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/simple_counter/?branch=master)
[![Tests](https://github.com/ericsizemore/simple_counter/actions/workflows/tests.yml/badge.svg)](https://github.com/ericsizemore/simple_counter/actions/workflows/tests.yml)
[![PHPStan](https://github.com/ericsizemore/simple_counter/actions/workflows/main.yml/badge.svg)](https://github.com/ericsizemore/simple_counter/actions/workflows/main.yml)

[![Latest Stable Version](https://img.shields.io/packagist/v/esi/simple_counter.svg)](https://packagist.org/packages/esi/simple_counter)
[![Downloads per Month](https://img.shields.io/packagist/dm/esi/simple_counter.svg)](https://packagist.org/packages/esi/simple_counter)
[![License](https://img.shields.io/packagist/l/esi/simple_counter.svg)](https://packagist.org/packages/esi/simple_counter)

[Simple Counter](http://github.com/ericsizemore/simple_counter/) is a simple PHP counter that counts your website visitors. It has the ability to 
either show the count as plain text or images; and whether to count only unique hits, or all hits. (IP Based)

### Small Note
This code is many years old (first started in 2006). I am in the process of bringing it into the modern world, and that includes the 
documentation. I am working on it, I promise. ;)

### If you are upgrading from v4.* to v5.0.0

* Make a backup of your current `logs` directory (and `images`, if you are using custom images).
  * Your directories are most likely `counter/logs` and `counter/images` since that was the default way to install/setup SimpleCounter prior to v5 [^1]
  * This is the default in v5 as well, so if you follow installation and copy the `counter` folder to your webroot, it will overwrite your old data, so backups are key. 
* Install SimpleCounter v5 through composer.
* Copy your `ips.txt` and `counter.txt` files that you backed up to the new locations.
* Update your site/project on how you call the counter, see [usage](#usage).

[^1]: If you were using custom locations for the `logs` and `images` directories already, you can change how you instantiate the class to point to these locations instead. See [usage](#usage).

## Installation

To install Simple Counter, first install via composer:

```bash
composer require esi/simple_counter
```

There are several options defined by default, however you will likely run into issues if you do not change some of them.
More information can be found in [Usage](#usage) below.

* Copy the `counter` directory from `vendor/esi/simple_counter` to your webroot
  * The `counter` directory contains the `logs` and `images` directory.
  * You can change the name of either directory if you wish, or skip using the `counter` directory all together and just move `logs` and `images` to your webroot.
    * However:
      * The ip file and counter file must remain `ips.txt` and `counter.txt`
      * The images must be named 0-9.
* Make sure the `ips.txt` and `counter.txt` files within your logs directory are writable.

## Usage
Usage is fairly simple once installed. Simply add the following code to the page where you want the counter to be shown:

```php
<?php

// Load the composer autoload file, if not already loaded
require_once 'vendor/autoload.php';

use Esi\SimpleCounter\Counter;

// Options is a string => string | bool, key => value pair array.
// Valid options are:
$options = [
    'logDir'          => '/path/to/some/dir/logs',
    'imageDir'        => '/path/to/some/dir/images',
    'imageExt'        => '.gif', // '.png', '.jpg' etc. default images are GIF images
    'useImages'       => true, // true = images, false = plain text
    'useFileLocking'  => true, // recommended = true for file locking on read/write operations for the log files
    'countOnlyUnique' => true // true = counts only unique ip's, false = counts all
];

// If no options are passed, uses defaults
$counter = Counter::getInstance();

// ... or maybe custom directories
$counter = Counter::getInstance([
    'logDir' => '/var/www/html/hitcounter/logs',
    'imageDir' => '/var/www/html/hitcounter/images'
]);

// ... or maybe defaults, but I want to use custom images and higher quality PNG images
$counter = Counter::getInstance([
    'imageExt' => '.png'
]);

// Finally, call process(). You can either output it directly or save it to a variable if needed
echo $counter->process();

// ... or ...

$hitCount = $counter->process();

// ..
// ...
// ... do some stuff
echo $hitCount;

?>
```

## About

### Requirements

- Simple Counter works with PHP 8.2.0 or above.

### Submitting bugs and feature requests

Bugs and feature requests are tracked on [GitHub](https://github.com/ericsizemore/simple_counter/issues)

Issues are the quickest way to report a bug. If you find a bug or documentation error, please check the following first:

* That there is not an Issue already open concerning the bug
* That the issue has not already been addressed (within closed Issues, for example)

### Contributing

Simple Counter accepts contributions of code and documentation from the community. These contributions can be made in the form of Issues or [Pull Requests](http://help.github.com/send-pull-requests/) on the [Simple Counter repository](https://github.com/ericsizemore/simple_counter).

Simple Counter is licensed under the GNU LGPL v3 license. When submitting new features or patches to Simple Counter, you are giving permission to license those features or patches under the GNU LGPL v3 license.

Simple Counter tries to adhere to PHPStan level 9 with strict rules and bleeding edge. Please ensure any 
contributions do as well.

#### Guidelines

Before we look into how, here are the guidelines. If your Pull Requests fail to pass these guidelines it will be declined, and you will need to re-submit when you’ve made the changes. This might sound a bit tough, but it is required for me to maintain quality of the code-base.

#### PHP Style

Please ensure all new contributions match the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style guide. The project is not fully PSR-12 compatible, yet; however, to ensure the easiest transition to the coding guidelines, I would like to go ahead and request that any contributions follow them.

#### Documentation

If you change anything that requires a change to documentation then you will need to add it. New methods, parameters, changing default values, adding constants, etc. are all things that will require a change to documentation. The change-log must also be updated for every change. Also, PHPDoc blocks must be maintained.

##### Documenting functions/variables (PHPDoc)

Please ensure all new contributions adhere to:
  * [PSR-5 PHPDoc](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md)
  * [PSR-19 PHPDoc Tags](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md)

when documenting new functions, or changing existing documentation.

#### Branching

One thing at a time: A pull request should only contain one change. That does not mean only one commit, but one change - however many commits it took. The reason for this is that if you change X and Y but send a pull request for both at the same time, we might really want X but disagree with Y, meaning we cannot merge the request. Using the Git-Flow branching model you can create new branches for both of these features and send two requests.

### Author

Eric Sizemore - <admin@secondversion.com> - <https://www.secondversion.com>

### License

Simple Counter is licensed under the GNU LGPL v3 License - see the `COPYING` file for details
