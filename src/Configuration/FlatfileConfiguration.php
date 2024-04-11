<?php

declare(strict_types=1);

/**
 * This file is part of Esi\SimpleCounter.
 *
 * (c) Eric Sizemore <https://github.com/ericsizemore>
 *
 * This source file is subject to the MIT license. For the full copyright and
 * license information, please view the LICENSE file that was distributed with
 * this source code.
 */

namespace Esi\SimpleCounter\Configuration;

use Esi\SimpleCounter\Interface\ConfigurationInterface;
use Esi\Utility\Filesystem;
use Esi\Utility\Strings;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function rtrim;

/**
 * @phpstan-import-type BaseStorageOptions from \Esi\SimpleCounter\Counter
 * @phpstan-import-type FlatfileOptions from \Esi\SimpleCounter\Counter
 *
 * @see \Esi\SimpleCounter\Tests\FlatfileStorageTest
 */
final class FlatfileConfiguration implements ConfigurationInterface
{
    /**
     * @var BaseStorageOptions&FlatfileOptions
     */
    private static array $options = [];

    /**
     * @param BaseStorageOptions&FlatfileOptions $options
     */
    private function __construct(array $options = [])
    {
        $optionsResolver = new OptionsResolver();
        self::configureOptions($optionsResolver);

        self::$options = $optionsResolver->resolve($options);
    }

    public static function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'logDir'            => \dirname(__DIR__, 2) . '/counter/logs/',
            'countFile'         => 'counter.json',
            'ipFile'            => 'ips.json',
            'imageDir'          => \dirname(__DIR__, 2) . '/counter/images/',
            'imageExt'          => '.png',
            'uniqueOnly'        => true,
            'asImage'           => false,
            'honorDnt'          => false,
            'visitorTextString' => 'You are visitor #%s',
        ])
            ->setAllowedTypes('logDir', 'string')
            ->setAllowedTypes('countFile', 'string')
            ->setAllowedTypes('ipFile', 'string')
            ->setAllowedTypes('imageDir', 'string')
            ->setAllowedTypes('imageExt', 'string')
            ->setAllowedTypes('uniqueOnly', 'bool')
            ->setAllowedTypes('asImage', 'bool')
            ->setAllowedTypes('honorDnt', 'bool')
            ->setAllowedTypes('visitorTextString', 'string')
            ->setAllowedValues('logDir', static fn (string $value): bool => Filesystem::isDirectory($value))
            ->setAllowedValues('imageDir', static fn (string $value): bool => Filesystem::isDirectory($value))
            ->setAllowedValues('countFile', static fn (string $value): bool => Strings::endsWith($value, '.json'))
            ->setAllowedValues('ipFile', static fn (string $value): bool => Strings::endsWith($value, '.json'))
            ->setAllowedValues('visitorTextString', static fn (string $value): bool => Strings::doesContain($value, '%s'))
            ->setNormalizer('logDir', static fn (Options $options, string $value): string => rtrim($value, '/\\'))
            ->setNormalizer('imageDir', static fn (Options $options, string $value): string => rtrim($value, '/\\'))
            ->setNormalizer('imageExt', static function (Options $options, string $value): string {
                if (!Strings::beginsWith($value, '.')) {
                    $value = '.' . $value;
                }

                return $value;
            });
    }

    public static function getOption(string $option): string | bool | null
    {
        return self::$options[$option] ?? null;
    }

    public static function initOptions(array $options = []): FlatfileConfiguration
    {
        return new self($options);
    }
}
