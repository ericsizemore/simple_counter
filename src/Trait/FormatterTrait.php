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

namespace Esi\SimpleCounter\Trait;

use Esi\SimpleCounter\Interface\ConfigurationInterface;

use function array_map;
use function implode;
use function number_format;
use function sprintf;
use function str_split;

use const DIRECTORY_SEPARATOR;

/**
 * Trait used in *Storage classes.
 */
trait FormatterTrait
{
    /**
     * Returns the formatted count information given the current count.
     *
     * If the 'asImage' option is set to true, then HTML is returned. Otherwise,
     * just plain text.
     *
     * Normally called in the Storage class' display() method.
     */
    protected function formatDataForDisplay(ConfigurationInterface $configuration, int $currentCount): string
    {
        /** @var string $imageDir */
        $imageDir = $configuration::getOption('imageDir');

        /** @var string $imageExt */
        $imageExt = $configuration::getOption('imageExt');

        /** @var string $visitorTextString */
        $visitorTextString = $configuration::getOption('visitorTextString');

        if ($configuration::getOption('asImage') === true) {
            return implode('&nbsp;', array_map(static fn (string $number): string => sprintf(
                '<img src="%s%d%s" alt="%2$d" />',
                $imageDir . DIRECTORY_SEPARATOR,
                $number,
                $imageExt,
            ), str_split((string) $currentCount)));
        }

        return sprintf($visitorTextString, number_format((float) $currentCount));
    }
}
