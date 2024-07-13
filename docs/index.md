# Simple Counter - A simple web hit counter.

[Simple Counter](https://github.com/ericsizemore/simple_counter/) is a simple PHP counter that counts your website visitors. It has the ability to 
either show the count as plain text or images; and whether to count only unique hits, or all hits. (IP Based)

## Important Note

* As of v6.0.0, Simple Counter is no longer licensed under the GNU LGPLv3 license.
* v6.0.0 is a complete rewrite of the library, and no GNU LGPLv3 licensed code remains.
* v5.0.1 and previous are still licensed under the GNU LGPLv3 license.

With that being said, it is important to read the `Upgrading` section below if you are coming from an older version.


## Acknowledgements

The icons used for the default image set (0-9 'png' images found in `counter/images/`) are licensed under the [CC BY 4.0 DEED license](https://creativecommons.org/licenses/by/4.0/), and were designed by [StreamlineHQ](https://www.streamlinehq.com/freebies/typeface).


## Upgrading

Pre-v6 -> v6 is not a simple upgrade. Several things have changed in this rewrite, and there are breaking changes.

Please read [Upgrading](upgrading.md) first before attempting an upgrade.


## Installation

To install Simple Counter, first install via composer:

```bash
$ composer require esi/simple_counter:^6.0
```

There are several options defined by default, however you will likely run into issues if you do not change some of them.
More information can be found in [Usage](#usage) below.

* Copy the `counter` directory from `vendor/esi/simple_counter` to your webroot
  * The `counter` directory contains the `logs` and `images` directory.
  * You can change the name of either directory if you wish, or skip using the `counter` directory all together and just move `logs` and `images` to your webroot.
    * However:
      * The ip file and counter file must remain `ips.json` and `counter.json`
      * The images must be named 0-9.
* Make sure the `ips.json` and `counter.json` files within your logs directory are writable.


## Usage

**More detailed documentation is a work in progress.**

Usage is fairly simple once installed. There is currently one option for the type of counter you wish to use, and that is the `FlatfileStorage`. A `DatabaseStorage` is slated for an upcoming release.

Simply add the following code to the page where you want the counter to be shown:

```php
<?php

// Load the composer autoload file, if not already loaded
require_once 'vendor/autoload.php';

use Esi\SimpleCounter\Counter;
use Esi\SimpleCounter\Storage\FlatfileStorage;
use Esi\SimpleCounter\Configuration\FlatfileConfiguration;

/**
 * $options is an array of:
 *
 * array{
 *     logDir: string,
 *     countFile: string,
 *     ipFile: string,
 *     imageDir: string,
 *     imageExt: string,
 *     uniqueOnly: bool,
 *     asImage: bool,
 *     honorDnt: bool,
 *     visitorTextString?: string
 * }
 *
 * Default values are:
 *
 * [
 *      'logDir'            => dirname(__DIR__, 2) . '/counter/logs/',
 *      'countFile'         => 'counter.json',
 *      'ipFile'            => 'ips.json',
 *      'imageDir'          => dirname(__DIR__, 2) . '/counter/images/',
 *      'imageExt'          => '.png',
 *      'uniqueOnly'        => true,
 *      'asImage'           => false,
 *      'honorDnt'          => false,
 *      'visitorTextString' => 'You are visitor #%s',
 * ] 
 */
// Valid options are:
$options = [
    'logDir'            => '/path/to/some/dir/logs',
    'countFile'         => 'counter.json',
    'ipFile'            => 'ips.json',
    'imageDir'          => '/path/to/some/dir/images',
    'imageExt'          => '.png', // '.png', '.jpg' etc. default images are PNG images
    'asImage'           => true,   // true = images, false = plain text
    'uniqueOnly'        => true,   // true = counts only unique ip's, false = counts all,
    'honorDnt'          => false,
    'visitorTextString' => 'You are visitor #%s',
];

/**
 * Important note regarding the 'visitorTextString'. This is the text that is shown if 'asImage' is false.
 *
 * For example, by default, it would show: You are visitor #123.
 * If you wanted to change it to something like: Counter: #123,
 * you would set the 'visitorTextString' option to:
 * 
 * 'Counter: #%s'
 */

/**
 * When creating the counter instance, a Storage implementation with a valid Configuration is required.
 * Currently, Simple Counter ships with one Storage implementation, and it's corresponding Configuration:
 *
 * \Esi\SimpleCounter\Storage\FlatfileStorage
 * \Esi\SimpleCounter\Configuration\FlatfileConfiguration
 *
 * \Esi\SimpleCounter\Counter can be used as a wrapper, but it is not necessary. For example:
 *
 * $counter = new Counter(
 *     new FlatfileStorage(
 *         FlatfileConfiguration::initOptions($options)
 *     )
 * );
 */
// Pass custom options
$counter = new FlatfileStorage(
    FlatfileConfiguration::initOptions($options)
);

// ... or if you wish to use defaults
$counter = new FlatfileStorage(
    FlatfileConfiguration::initOptions()
);

// ... or maybe you only want to switch to using images, for example
$counter = new FlatfileStorage(
    FlatfileConfiguration::initOptions(['asImage' => true])
);

// Finally, call display(). You can either output it directly or save it to a variable if needed
echo $counter->display();

// ... or ...

$hitCount = $counter->display();

// ... do some stuff
echo $hitCount;

?>
```

## Handling Errors

Simple Counter uses Exceptions for various issues that may arise throughout its process.

Currently, most exceptions fall under `\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException`.

These exceptions can be thrown if:

* You pass an option that is not defined.
* A given option's value type does not match the allowed types.
* When reading/writing to a file encounters an error, since it is likely due to a file name/location issue.
* For invalid directories or files, in terms of `logDir`, `imageDir`, `countFile`, `ipFile`


## About

### Requirements

- Simple Counter works with PHP 8.2.0 or above.

### Submitting bugs and feature requests

Bugs and feature requests are tracked on [GitHub](https://github.com/ericsizemore/simple_counter/issues)

Issues are the quickest way to report a bug. If you find a bug or documentation error, please check the following first:

* That there is not an Issue already open concerning the bug
* That the issue has not already been addressed (within closed Issues, for example)

### Contributing

* See [Contributing](https://github.com/ericsizemore/simple_counter/blob/master/CONTRIBUTING.md)

### Backward Compatibility Promise

* See [Backward Compatibility](https://github.com/ericsizemore/simple_counter/blob/master/backward-compatibility.md)

### Author

Eric Sizemore - <admin@secondversion.com> - <https://www.secondversion.com>

### License

#### Simple Counter v6.0.0 and newer

* Licensed under the MIT License. See the [License](license.md) file for details.

#### Simple Counter v5.0.1 and older

* Licensed under the GNU LGPL v3 License. See the [`<= 5.x LICENSE.md`](https://github.com/ericsizemore/simple_counter/blob/5.0.x/COPYING.LESSER) file for details.
