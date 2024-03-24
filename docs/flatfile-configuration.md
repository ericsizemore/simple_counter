# Flatfile Configuration

Defined methods in accordance with `Esi\SimpleCounter\Interface\ConfigurationInterface`.

`Esi\SimpleCounter\Configuration\FlatfileConfiguration`

* [__construct](#__construct)(private StorageInterface $storage);
* [configureOptions](#configureoptions)(OptionsResolver $optionsResolver): void;
* [getOption](#getoption)(string $option): string | bool | null;
* [initOptions](#initoptions)(array $options = []): FlatfileConfiguration;


## __construct

The class constructor. Cannot be called directly. Builds the `OptionsResolver`, calls `configureOptions`, and populates the `self::$options` array.

## configureOptions

Validates and resolves options based on a set of rules provided to `OptionsResolver`.

## getOption

Returns the given option, if it exists.

## initOptions

Takes an array of options to be used in the FlatfileStorage implementation.
