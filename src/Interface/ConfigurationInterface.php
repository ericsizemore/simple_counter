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

namespace Esi\SimpleCounter\Interface;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @phpstan-import-type BaseStorageOptions from \Esi\SimpleCounter\Counter
 * @phpstan-import-type FlatfileOptions from \Esi\SimpleCounter\Counter
 *
 * @see \Esi\SimpleCounter\Storage\FlatfileStorage for implementation details.
 */
interface ConfigurationInterface
{
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
}
