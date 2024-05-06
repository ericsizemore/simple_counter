# Formatter Trait

Contains functions used in all *Storage classes. Currently, it is home to one function.

`Esi\SimpleCounter\Trait\FormatterTrait`

```php
protected function formatDataForDisplay(ConfigurationInterface $configuration, int $currentCount): string;
```

## formatDataForDisplay

Returns the formatted count information given the current count.

If the 'asImage' option is set to true, then HTML is returned. Otherwise, just plain text.

Normally called in the Storage class' display() method.