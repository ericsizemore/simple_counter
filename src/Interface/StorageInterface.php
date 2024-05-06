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

use RuntimeException;

interface StorageInterface
{
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
     * @throws RuntimeException If, using the FlatfileStorage, the current ip list cannot be obtained.
     *
     * @return list<string>
     */
    public function fetchCurrentIpList(): array;

    /**
     * Returns the given option, if it exists.
     */
    public function getOption(string $option): null|bool|string;
}
