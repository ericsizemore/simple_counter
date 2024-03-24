# Storage Interface

Defines an interface (contract) that Storage classes must implement.

`Esi\SimpleCounter\Interface\StorageInterface`


```php
    /**
     * Updates the count and uses \Esi\SimpleCounter\Trait\FormatterTrait::formatDataForDisplay()
     * to format the count as text or images, depending on configuration.
     */
    public function display(): string;

    /**
     * Returns the current count data, without updating the count itself.
     *
     * Mostly internal use, but can be used if you need the count information without
     * triggering an update.
     *
     * @throws RuntimeException If, using the FlatfileStorage, the current count cannot be obtained.
     */
    public function fetchCurrentCount(): int;

    /**
     * Returns the current IP data, if any.
     *
     * @return list<string>
     *
     * @throws RuntimeException If, using the FlatfileStorage, the current ip list cannot be obtained.
     */
    public function fetchCurrentIpList(): array;

    /**
     * Returns the given option, if it exists.
     */
    public function getOption(string $option): string | bool | null;
```