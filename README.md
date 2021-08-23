# Simple Counter - Website visitor counter.

[Simple Counter](http://github.com/ericsizemore/simple_counter/) is a simple PHP counter that counts your website visitors. It has the ability to 
either show the count as plain text or images; and whether or not to count only unique hits, or all hits. (IP Based)

### Small Note
This code is many years old (first started in 2006). I am in the process of bringing it into the modern world, and that includes the 
documentation. I am working on it, I promise. ;)

## Installation

To install Simple Counter:

1. Open 'counter.php', configure the settings near the top of the script.
2. Create a new folder, named 'counter'.
3. Upload 'counter.php', 'index.html', and the 'logs' directory in ASCII mode.
4. Upload the 'images' folder in BINARY mode.
5. CHMOD the 'counter.txt' and 'ips.txt' files to 0666 (if required). They are located in the 'logs' folder.

## Usage
Usage is fairly simple once installed. Simply add the following code to the page where you want the counter to be shown:

```php
<?php include './counter/counter.php'; ?>
```

## About

### Requirements

- Simple Counter works with PHP 7.0.0 or above.

### Submitting bugs and feature requests

Bugs and feature requests are tracked on [GitHub](https://github.com/ericsizemore/simple_counter/issues)

Issues are the quickest way to report a bug. If you find a bug or documentation error, please check the following first:

* That there is not an Issue already open concerning the bug
* That the issue has not already been addressed (within closed Issues, for example)

### Contributing

Simple Counter accepts contributions of code and documentation from the community. 
These contributions can be made in the form of Issues or [Pull Requests](http://help.github.com/send-pull-requests/) 
on the [Simple Counter repository](https://github.com/ericsizemore/simple_counter).

Simple Counter is licensed under the GNU GPL v3 license. When submitting new features or patches to Simple Counter, you are 
giving permission to license those features or patches under the GNU GPL v3 license.

#### Guidelines

Before we look into how, here are the guidelines. If your Pull Requests fail to
pass these guidelines it will be declined and you will need to re-submit when
you’ve made the changes. This might sound a bit tough, but it is required for
me to maintain quality of the code-base.

#### PHP Style

Please ensure all new contributions match the [PSR-2](http://www.php-fig.org/psr/psr-2/)
coding style guide. The project is not fully PSR-2 compatible, yet; however, to ensure 
the easiest transition to the coding guidelines, I would like to go ahead and request 
that any contributions follow them.

#### Documentation

If you change anything that requires a change to documentation then you will
need to add it. New methods, parameters, changing default values, adding
constants, etc are all things that will require a change to documentation. The
change-log must also be updated for every change. Also PHPDoc blocks must be
maintained.

#### Branching

One thing at a time: A pull request should only contain one change. That does
not mean only one commit, but one change - however many commits it took. The
reason for this is that if you change X and Y but send a pull request for both
at the same time, we might really want X but disagree with Y, meaning we cannot
merge the request. Using the Git-Flow branching model you can create new
branches for both of these features and send two requests.

### Author

Eric Sizemore - <admin@secondversion.com> - <http://www.secondversion.com>

### License

Simple Counter is licensed under the GNU GPL v3 License - see the `LICENSE` file for details
