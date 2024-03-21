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

namespace Esi\SimpleCounter\Interface;

use Esi\SimpleCounter\Configuration\FlatfileConfiguration;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @phpstan-import-type BaseAdapterOptions from \Esi\SimpleCounter\Counter
 * @phpstan-import-type FlatfileOptions from \Esi\SimpleCounter\Counter
 */
interface ConfigurationInterface
{
    /**
     * Takes an array of options to be used in the chosen Adapter.
     *
     * The allowed types for $options will be updated as new Adapters are added.
     *
     * @param BaseAdapterOptions&FlatfileOptions $options
     */
    public function __construct(array $options);

    /**
     * Validates and resolves the $options passed in the constructor.
     *
     * @throws InvalidOptionsException If a passed option does not exist or does not meet
     *                                 defined rules.
     */
    public function configureOptions(OptionsResolver $optionsResolver): void;

    /**
     * Returns the given option, if it exists.
     */
    public function getOption(string $option): string | bool | null;
}
