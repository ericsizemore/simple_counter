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

namespace Esi\SimpleCounter\Tests;

use Esi\SimpleCounter\Adapter\FlatfileAdapter;
use Esi\SimpleCounter\Configuration\FlatfileConfiguration;
use Esi\SimpleCounter\Counter;
use Esi\Utility\Arrays;
use Esi\Utility\Environment;
use Esi\Utility\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

use function dirname;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * @internal
 */
#[CoversClass(Counter::class)]
#[UsesClass(FlatfileAdapter::class)]
#[UsesClass(FlatfileConfiguration::class)]
class CounterTest extends TestCase
{
    private ?Counter $counter;

    /**
     * @var string[]
     */
    private static array $logFiles;

    /**
     * @var string[]
     */
    private static array $testDirectories;

    #[\Override]
    protected function setUp(): void
    {
        Arrays::set($_SERVER, 'REMOTE_ADDR', '127.0.0.1');

        self::$testDirectories = [
            'logDir'   => sprintf('%s%s%s', dirname(__FILE__, 2), DIRECTORY_SEPARATOR, 'logs'),
            'imageDir' => sprintf('%s%s%s', dirname(__FILE__, 2), DIRECTORY_SEPARATOR, 'images'),
        ];

        self::$logFiles = [
            'countFile' => sprintf('%s%s%s', self::$testDirectories['logDir'], DIRECTORY_SEPARATOR, 'counter.json'),
            'ipFile'    => sprintf('%s%s%s', self::$testDirectories['logDir'], DIRECTORY_SEPARATOR, 'ips.json'),
        ];

        $this->counter = new Counter(new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'   => self::$testDirectories['logDir'],
                'imageDir' => self::$testDirectories['imageDir'],
            ]
        )));
    }

    #[\Override]
    protected function tearDown(): void
    {
        $this->counter = null;

        Arrays::set($_SERVER, 'REMOTE_ADDR', Environment::var('REMOTE_ADDR', null));

        Filesystem::fileWrite(self::$logFiles['countFile'], '{"currentCount":"0"}');
        Filesystem::fileWrite(self::$logFiles['ipFile'], '{"ipList":[""]}');
    }

    #[TestDox('fetchCurrentCount is able to return the current count data accurately.')]
    public function testFetchCurrentCount(): void
    {
        self::assertInstanceOf(Counter::class, $this->counter);

        $currentCount = $this->counter->fetchCurrentCount();
        self::assertSame(0, $currentCount);

        $this->counter->display();
        $newCount = $this->counter->fetchCurrentCount();
        self::assertSame(1, $newCount);
    }

    #[TestDox('fetchCurrentIpList is able to return the current ip data accurately.')]
    public function testFetchCurrentIpList(): void
    {
        self::assertInstanceOf(Counter::class, $this->counter);
        self::assertEmpty($this->counter->fetchCurrentIpList());
        $this->counter->display();
        self::assertSame(['127.0.0.1'], $this->counter->fetchCurrentIpList());
    }

    #[TestDox('getOption is able to return the value of a given option.')]
    public function testGetOption(): void
    {
        self::assertInstanceOf(Counter::class, $this->counter);
        self::assertSame(self::$testDirectories['logDir'], $this->counter->getOption('logDir'));
    }

    #[TestDox('Was able to instantiate Counter with default options and retrieve count information.')]
    public function testWithDefaultOptions(): void
    {
        self::assertInstanceOf(Counter::class, $this->counter);

        $count    = $this->counter->display();
        $countTwo = $this->counter->display();

        self::assertNotEmpty($count);
        self::assertNotEmpty($countTwo);
        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $count);
        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $countTwo);
        self::assertSame($count, $countTwo);
    }
}
