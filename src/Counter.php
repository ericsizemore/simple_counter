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

namespace Esi\SimpleCounter;

use Esi\SimpleCounter\Interface\StorageInterface;

/**
 * Essentially a wrapper for a given Storage implementation.
 *
 * @see \Esi\SimpleCounter\Tests\CounterTest
 */

/**
 * PHPStan type for each options array definition, for each storage implementation.
 *
 * Placed here so that it can be imported via phpstan-import-type into where it is needed.
 *
 * @phpstan-type BaseStorageOptions = array{
 *     imageDir?: string,
 *     imageExt?: string,
 *     uniqueOnly?: bool,
 *     asImage?: bool,
 *     honorDnt?: bool,
 *     visitorTextString?: string
 * }|array{}
 * @phpstan-type FlatfileOptions = array{
 *     logDir?: string,
 *     countFile?: string,
 *     ipFile?: string
 * }
 */
readonly class Counter
{
    /**
     * Current Simple Counter package version.
     */
    public const VERSION = '6.0.0';

    /**
     * $storage should be one of the available Storage implementations that has already
     * been instantiated with their relevant Configuration.
     */
    public function __construct(
        private StorageInterface $storage
    ) {
        //
    }

    /**
     * Updates count and formats for display, for the given Storage implementation.
     */
    public function display(): string
    {
        return $this->storage->display();
    }

    /**
     * Useful for retrieving the current count without triggering an update.
     */
    public function fetchCurrentCount(): int
    {
        return $this->storage->fetchCurrentCount();
    }

    /**
     * Returns ip data, if any exists.
     *
     * @return list<string>
     */
    public function fetchCurrentIpList(): array
    {
        return $this->storage->fetchCurrentIpList();
    }

    /**
     * Returns the given option, if it exists.
     */
    public function getOption(string $option): string | bool | null
    {
        return $this->storage->getOption($option);
    }
}
