### If you are upgrading from v5.* to v6.0.0

* As with any upgrade, it is recommended to make a backup of your current `logs` directory (and `images`, if you are using custom images).
* v6 uses different files (`.json` instead of `.txt`), and stores data differently (as json data instead of plain text), than versions <=5.0.1
  * The default counter images are also different, and are PNG images instead of GIF images.
* You can upgrade using composer, though you would need to update your `composer.json` first to change the version constraint from `^5.0` to `^6.0`. Then:

```shell
composer update
```

* A script is provided at `scripts/convertFiles.php` that can update the counter and log files to the new format.

```shell
php -f scripts/convertFiles.php
```

Before running the script, you'll need to edit it to make sure it is pointing to the proper location of the counter files:

```php
// Update the location to your current log files, if needed.
$oldCounterFile = \dirname(__DIR__) . '/counter/counter.txt';
$oldIpFile      = \dirname(__DIR__) . '/counter/ips.txt';

// Update the location where the new files will be placed, if needed.
$newCounterFile = \dirname(__DIR__) . '/counter/counter.json';
$newIpFile      = \dirname(__DIR__) . '/counter/ips.json';
```

* See [Usage](index.md#usage) for more details on how to update your calls to, or instantiation of, the counter library.


### If you are upgrading from v4.* to v5.0.0

* Make a backup of your current `logs` directory (and `images`, if you are using custom images).
  * Your directories are most likely `counter/logs` and `counter/images` since that was the default way to install/setup SimpleCounter prior to v5 [^1]
  * This is the default in v5 as well, so if you follow installation and copy the `counter` folder to your webroot, it will overwrite your old data, so backups are key. 
* Install SimpleCounter v5 through composer.
* Copy your `ips.txt` and `counter.txt` files that you backed up to the new locations.
* Update your site/project on how you call the counter, see [usage](https://github.com/ericsizemore/simple_counter/blob/5.0.x/README.md#usage).

[^1]: If you were using custom locations for the `logs` and `images` directories already, you can change how you instantiate the class to point to these locations instead. See [usage](https://github.com/ericsizemore/simple_counter/blob/5.0.x/README.md#usage).