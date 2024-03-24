# Configuration Interface

Defines an interface (contract) that Configuration classes must implement.

`Esi\SimpleCounter\Interface\ConfigurationInterface`

```php
    /**
     * Validates and resolves the $options passed in initOptions().
     *
     * @throws InvalidOptionsException If a passed option does not exist or does not meet defined rules.
     */
    public static function configureOptions(OptionsResolver $optionsResolver): void;

    /**
     * Returns the given option, if it exists.
     */
    public static function getOption(string $option): string | bool | null;

    /**
     * Takes an array of options to be used in the chosen Storage implementation.
     *
     * The allowed types for $options will be updated as new Storage implementations are added.
     *
     * @param BaseStorageOptions&FlatfileOptions $options
     */
    public static function initOptions(array $options = []): ConfigurationInterface;
```