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

namespace Esi\SimpleCounter;

use Esi\SimpleCounter\Interface\StorageInterface;

/**
 * Essentially a wrapper for a given Storage implementation.
 *
 * @see Tests\CounterTest
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
    public function __construct(private StorageInterface $storage) {}

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
    public function getOption(string $option): null|bool|string
    {
        return $this->storage->getOption($option);
    }
}
