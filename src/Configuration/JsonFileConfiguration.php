<?php

declare(strict_types=1);

/**
 * This file is part of Esi\SimpleCounter.
 *
 * (c) Eric Sizemore <https://github.com/ericsizemore>
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 */

namespace Esi\SimpleCounter\Configuration;

use Esi\SimpleCounter\Interface\ConfigurationInterface;
use Esi\Utility\Filesystem;
use Esi\Utility\Strings;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function dirname;
use function rtrim;

/**
 * @phpstan-type JsonFileOptions = array{
 *     logDir?: string,
 *     countFile?: string,
 *     ipFile?: string,
 *     imageDir?: string,
 *     imageExt?: string,
 *     uniqueOnly?: bool,
 *     asImage?: bool
 * }|array{}
 *
 * @see \Esi\SimpleCounter\Tests\JsonFileAdapterTest
 */
final class JsonFileConfiguration implements ConfigurationInterface
{
    /**
     * @var JsonFileOptions
     */
    private array $options;

    public function __construct(array $options = [])
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        $this->options = $optionsResolver->resolve($options);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'logDir'     => dirname(__DIR__, 2) . '/counter/logs/',
            'countFile'  => 'counter.json',
            'ipFile'     => 'ips.json',
            'imageDir'   => dirname(__DIR__, 2) . '/counter/images/',
            'imageExt'   => '.png',
            'uniqueOnly' => true,
            'asImage'    => false,
        ])
            ->setAllowedTypes('logDir', 'string')
            ->setAllowedTypes('countFile', 'string')
            ->setAllowedTypes('ipFile', 'string')
            ->setAllowedTypes('imageDir', 'string')
            ->setAllowedTypes('imageExt', 'string')
            ->setAllowedTypes('uniqueOnly', 'bool')
            ->setAllowedTypes('asImage', 'bool')
            ->setAllowedValues('logDir', static fn (string $value): bool => Filesystem::isDirectory($value))
            ->setAllowedValues('imageDir', static fn (string $value): bool => Filesystem::isDirectory($value))
            ->setAllowedValues('countFile', static fn (string $value): bool => Strings::endsWith($value, '.json'))
            ->setAllowedValues('ipFile', static fn (string $value): bool => Strings::endsWith($value, '.json'))
            ->setNormalizer('logDir', static fn (Options $options, string $value): string => rtrim($value, '/\\'))
            ->setNormalizer('imageDir', static fn (Options $options, string $value): string => rtrim($value, '/\\'))
            ->setNormalizer('imageExt', static function (Options $options, string $value): string {
                if (!Strings::beginsWith($value, '.')) {
                    $value = '.' . $value;
                }

                return $value;
            });
    }

    public function getOption(string $option): string | bool | null
    {
        return $this->options[$option] ?? null;
    }
}
