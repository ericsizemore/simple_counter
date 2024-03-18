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

use Esi\SimpleCounter\Interface\CounterInterface;

/**
 * Essentially a wrapper for a given Adapter.
 *
 * @see \Esi\SimpleCounter\Tests\CounterTest
 */
readonly class Counter
{
    /**
     * $adapter should be one of the available Adapters that has already
     * been instantiated with their relevant Configuration.
     */
    public function __construct(
        private CounterInterface $adapter
    ) {
        //
    }

    /**
     * Updates count and formats for display, for the given Adapter.
     */
    public function display(): string
    {
        return $this->adapter->display();
    }

    /**
     * Useful for retrieving the current count without triggering an update.
     */
    public function fetchCurrentCount(): int
    {
        return $this->adapter->fetchCurrentCount();
    }

    /**
     * Returns ip data, if any exists.
     *
     * @return list<string>
     */
    public function fetchCurrentIpList(): array
    {
        return $this->adapter->fetchCurrentIpList();
    }
}
