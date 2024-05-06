# Counter

Main class file.

`Esi\SimpleCounter\Counter`

* [__construct](#__construct)(private StorageInterface $storage);
* [display](#display)(): string;
* [fetchCurrentCount](#fetchcurrentcount)(): int;
* [fetchCurrentIpList](#fetchcurrentiplist)(): array;
* [getOption](#getoption)(string $option): string | bool | null;


## __construct

The class constructor. Used to pass a class implementing `StorageInterface` (such as `Storage\FlatfileStorage`).

## display

Updates count and formats for display, for the given Storage implementation.

## fetchCurrentCount

Useful for retrieving the current count without triggering an update.

## fetchCurrentIpList

Returns ip data, if any exists.

```php
/**
 * @return list<string>
 */
```

## getOption

Returns the given option, if it exists.
