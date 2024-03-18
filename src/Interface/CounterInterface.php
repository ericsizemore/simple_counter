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

interface CounterInterface
{
    /**
     * Updates the count and uses \Esi\SimpleCounter\Adapter\FormatterTrait::formatDataForDisplay()
     * to format the count as text or images, depending on configuration.
     */
    public function display(): string;

    /**
     * Returns the current count data, without updating the count itself.
     *
     * Mostly internal use, but can be used if you need the count information without
     * triggering an update.
     */
    public function fetchCurrentCount(): int;

    /**
     * Returns the current IP data, if any.
     *
     * @return list<string>
     */
    public function fetchCurrentIpList(): array;
}