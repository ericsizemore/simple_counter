# Flatfile Storage

Defined methods in accordance with `Esi\SimpleCounter\Interface\StorageInterface`.

`Esi\SimpleCounter\Storage\FlatfileStorage`

* [__construct](#__construct)(private FlatfileConfiguration $configuration);
* [display](#display)(): string;
* [fetchCurrentCount](#fetchcurrentcount)(): int;
* [fetchCurrentIpList](#fetchcurrentiplist)(): array;
* [getOption](#getoption)(string $option): string | bool | null;


## __construct

The class constructor. Takes an argument that is an instance of `FlatfileConfiguration`. Validates log files on instantiation.

## display

Updates the count, then formats the count and returns the information ready for display. 

## fetchCurrentCount

Retrieves the current count as an integer.

## fetchCurrentIpList

Retrives the current contents of the IP list log file. It is returned as a list of strings, i.e.:

```php
Array (
    [0] => "127.0.0.1",
    [1] => "127.0.0.2",
    // ... etc.
)
```

## getOption

Returns the given option, if it exists.
