### If you are upgrading from v5.* to v6.0.0

* Still a work in progress; the upgrade notes will be updated before official release.


### If you are upgrading from v4.* to v5.0.0

* Make a backup of your current `logs` directory (and `images`, if you are using custom images).
  * Your directories are most likely `counter/logs` and `counter/images` since that was the default way to install/setup SimpleCounter prior to v5 [^1]
  * This is the default in v5 as well, so if you follow installation and copy the `counter` folder to your webroot, it will overwrite your old data, so backups are key. 
* Install SimpleCounter v5 through composer.
* Copy your `ips.txt` and `counter.txt` files that you backed up to the new locations.
* Update your site/project on how you call the counter, see [usage](https://github.com/ericsizemore/simple_counter/blob/5.0.x/README.md#usage).

[^1]: If you were using custom locations for the `logs` and `images` directories already, you can change how you instantiate the class to point to these locations instead. See [usage](https://github.com/ericsizemore/simple_counter/blob/5.0.x/README.md#usage).