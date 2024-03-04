<?php

declare(strict_types=1);

/**
 * Simple Counter - A simple web hit counter.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @copyright (C) 2006 - 2024 Eric Sizemore
 * @license   GNU Lesser General Public License
 *
 * Copyright (C) 2006 - 2024 Eric Sizemore<https://www.secondversion.com/>. All rights reserved.
 *
 * SV's Simple Counter is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Esi\SimpleCounter\Tests;

use Esi\SimpleCounter\Counter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

use function dirname;

use const DIRECTORY_SEPARATOR;

/**
 * @package Esi\SimpleCounter\Tests
 * @version 5.0.1
 */
#[CoversClass(Counter::class)]
class CounterTest extends TestCase
{
    #[\Override]
    public function setUp(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }

    #[\Override]
    public function tearDown(): void
    {
        $logDir = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'logs';

        file_put_contents($logDir . DIRECTORY_SEPARATOR . 'counter.txt', 0);
        file_put_contents($logDir . DIRECTORY_SEPARATOR . 'ips.txt', "\n");
    }

    /**
     * Test with default options
     */
    public function testWithDefault(): void
    {
        $counter = Counter::getInstance([
                'logDir'          => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'logs',
                'imageDir'        => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'images',
                'imageExt'        => '.gif', // '.png', '.jpg' etc. default images are GIF images
                'useImages'       => true, // true = images, false = plain text
                'useFileLocking'  => true, // recommended = true for file locking on read/write operations for the log files
                'countOnlyUnique' => true, // true = counts only unique ip's, false = counts all
            ]);

        $count = trim($counter->process(), '&nbsp;');

        self::assertNotEmpty($count);

        // Reset
        $reflectionProperty = new ReflectionProperty(Counter::class, 'instance');
        $reflectionProperty->setValue(null, null);
    }

    /**
     * Test with default options
     */
    public function testWithDefaultNewVisitor(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.2';
        $counter = Counter::getInstance([
                'logDir'          => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'logs',
                'imageDir'        => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'images',
                'imageExt'        => '.gif', // '.png', '.jpg' etc. default images are GIF images
                'useImages'       => true, // true = images, false = plain text
                'useFileLocking'  => true, // recommended = true for file locking on read/write operations for the log files
                'countOnlyUnique' => true, // true = counts only unique ip's, false = counts all
            ]);

        $count = trim($counter->process(), '&nbsp;');

        self::assertNotEmpty($count);
        // Reset
        $reflectionProperty = new ReflectionProperty(Counter::class, 'instance');
        $reflectionProperty->setValue(null, null);
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }

    /**
     * Test with not using images
     */
    public function testWithNoImage(): void
    {
        $counter = Counter::getInstance([
                'logDir'          => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'logs',
                'imageDir'        => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'images',
                'imageExt'        => 'gif', // '.png', '.jpg' etc. default images are GIF images
                'useImages'       => false, // true = images, false = plain text
                'useFileLocking'  => false, // recommended = true for file locking on read/write operations for the log files
                'countOnlyUnique' => true, // true = counts only unique ip's, false = counts all
            ]);

        $count = $counter->process();

        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $count);
        // Reset
        $reflectionProperty = new ReflectionProperty(Counter::class, 'instance');
        $reflectionProperty->setValue(null, null);
    }

    /**
     * Test with not using unique
     */
    public function testWithNoUnique(): void
    {
        $counter = Counter::getInstance([
                'logDir'          => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'logs',
                'imageDir'        => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'images\\',
                'imageExt'        => 'gif', // '.png', '.jpg' etc. default images are GIF images
                'useImages'       => false, // true = images, false = plain text
                'useFileLocking'  => true, // recommended = true for file locking on read/write operations for the log files
                'countOnlyUnique' => false, // true = counts only unique ip's, false = counts all
            ]);

        $count = $counter->process();
        $countTwo = $counter->process();

        self::assertNotEquals($count, $countTwo);
        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $count);
        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $countTwo);
        // Reset
        $reflectionProperty = new ReflectionProperty(Counter::class, 'instance');
        $reflectionProperty->setValue(null, null);
    }

    /**
     * Test invalid options
     */
    public function testInvalidLogDir(): void
    {
        self::expectException(InvalidArgumentException::class);
        $counter = Counter::getInstance([
                'logDir'          => '/this/should/not/exist/logs',
                'imageDir'        => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'images',
            ]);
        $counter->process();
        // Reset
        $reflectionProperty = new ReflectionProperty(Counter::class, 'instance');
        $reflectionProperty->setValue(null, null);
    }

    /**
     * Test invalid options
     */
    public function testInvalidImageDir(): void
    {
        self::expectException(InvalidArgumentException::class);
        $counter = Counter::getInstance([
                'logDir'          => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'logs',
                'imageDir'        => '/this/should/not/exist/images',
            ]);
        $counter->process();
        // Reset
        $reflectionProperty = new ReflectionProperty(Counter::class, 'instance');
        $reflectionProperty->setValue(null, null);
    }

    /**
     * Test invalid options
     */
    public function testInvalidLogFilesNoExist(): void
    {
        self::expectException(InvalidArgumentException::class);
        $counter = Counter::getInstance([
                'logDir'          => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'logsNoFiles',
                'imageDir'        => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'images',
            ]);
        $counter->process();
        // Reset
        $reflectionProperty = new ReflectionProperty(Counter::class, 'instance');
        $reflectionProperty->setValue(null, null);
    }
}
