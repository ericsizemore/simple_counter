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
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function dirname;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * @internal
 */
#[CoversClass(FlatfileAdapter::class)]
#[CoversClass(FlatfileConfiguration::class)]
class FlatfileAdapterTest extends TestCase
{
    /**
     * @var string[]
     */
    private static array $logFiles;

    /**
     * @var string[]
     */
    private static array $testDirectories;

    /**
     * @var string[]
     */
    private static array $testInvalidDirectories;

    #[\Override]
    protected function setUp(): void
    {
        Arrays::set($_SERVER, 'REMOTE_ADDR', '127.0.0.1');

        self::$testDirectories = [
            'logDir'   => sprintf('%s%s%s', dirname(__FILE__, 2), DIRECTORY_SEPARATOR, 'logs'),
            'imageDir' => sprintf('%s%s%s', dirname(__FILE__, 2), DIRECTORY_SEPARATOR, 'images'),
        ];

        self::$testInvalidDirectories = [
            'logDir'   => '/this/should/not/exist/logs',
            'imageDir' => '/this/should/not/exist/images',
        ];

        self::$logFiles = [
            'countFile' => sprintf('%s%s%s', self::$testDirectories['logDir'], DIRECTORY_SEPARATOR, 'counter.json'),
            'ipFile'    => sprintf('%s%s%s', self::$testDirectories['logDir'], DIRECTORY_SEPARATOR, 'ips.json'),
        ];
    }

    #[\Override]
    protected function tearDown(): void
    {
        Arrays::set($_SERVER, 'REMOTE_ADDR', Environment::var('REMOTE_ADDR', null));

        Filesystem::fileWrite(self::$logFiles['countFile'], '{"currentCount":"0"}');
        Filesystem::fileWrite(self::$logFiles['ipFile'], '{"ipList":[""]}');
    }

    #[TestDox('getOption is able to return the value of a given option.')]
    public function testGetOption(): void
    {
        $counter = new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'    => self::$testDirectories['logDir'],
                'imageDir'  => self::$testDirectories['imageDir'],
            ]
        ));

        self::assertSame(self::$testDirectories['logDir'], $counter->getOption('logDir'));
    }

    #[TestDox('An exception of InvalidOptionsException is thrown when providing an invalid location to the count file.')]
    public function testInvalidLocationForCounterFile(): void
    {
        $this->expectException(InvalidOptionsException::class);
        new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'    => self::$testDirectories['logDir'],
                'imageDir'  => self::$testDirectories['imageDir'],
                'countFile' => 'counters.json',
            ]
        ));
    }

    #[TestDox('An exception of InvalidOptionsException is thrown when providing an invalid location to the ip file.')]
    public function testInvalidLocationForIpFile(): void
    {
        $this->expectException(InvalidOptionsException::class);
        new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'   => self::$testDirectories['logDir'],
                'imageDir' => self::$testDirectories['imageDir'],
                'ipFile'   => 'iplist.json',
            ]
        ));
    }

    #[TestDox('An exception of InvalidOptionsException is thrown when providing the wrong file extension/format for the count file.')]
    public function testInvalidOptionForCountFile(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $counter = new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'    => self::$testDirectories['logDir'],
                'imageDir'  => self::$testDirectories['imageDir'],
                'countFile' => 'counter.txt',
            ]
        ));

        $counter->display();
    }

    #[TestDox('New instance with a non-existing directory for "imageDir" throws InvalidOptionsException')]
    public function testInvalidOptionForImageDirectory(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $counter = new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'   => self::$testDirectories['logDir'],
                'imageDir' => self::$testInvalidDirectories['imageDir'],
            ]
        ));

        $counter->display();
    }

    #[TestDox('An exception of InvalidOptionsException is thrown when providing the wrong file extension/format for the ip file.')]
    public function testInvalidOptionForIpFile(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $counter = new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'    => self::$testDirectories['logDir'],
                'imageDir'  => self::$testDirectories['imageDir'],
                'countFile' => 'ips.txt',
            ]
        ));

        $counter->display();
    }

    #[TestDox('New instance with a non-existing directory for "logDir" throws InvalidOptionsException')]
    public function testInvalidOptionForLogDirectory(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $counter = new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'   => self::$testInvalidDirectories['logDir'],
                'imageDir' => self::$testDirectories['imageDir'],
            ]
        ));

        $counter->display();
    }

    #[TestDox('Instantiating the FlatfileAdapter with default options works properly and accurately.')]
    public function testWithDefaultOptions(): void
    {
        $counter = new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'   => self::$testDirectories['logDir'],
                'imageDir' => self::$testDirectories['imageDir'],
            ]
        ));

        $count    = $counter->display();
        $countTwo = $counter->display();

        self::assertNotEmpty($count);
        self::assertNotEmpty($countTwo);
        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $count);
        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $countTwo);
        self::assertSame($count, $countTwo);
    }

    #[TestDox('Instantiating the FlatfileAdapter with a missing dot at the beginning of the imageExt adds the dot.')]
    public function testWithDefaultOptionsMissingPeriodOnImageExtension(): void
    {
        $counter = new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'   => self::$testDirectories['logDir'],
                'imageDir' => self::$testDirectories['imageDir'],
                'imageExt' => 'png',
            ]
        ));

        self::assertSame('.png', $counter->getOption('imageExt'));

        $count = $counter->display();

        self::assertNotEmpty($count);
        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $count);
    }

    #[TestDox('A new visitor (or IP) increments the counter.')]
    public function testWithDefaultOptionsNewVisitor(): void
    {
        $counter = new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'   => self::$testDirectories['logDir'],
                'imageDir' => self::$testDirectories['imageDir'],
            ]
        ));

        $oldCount = $counter->display();

        Arrays::set($_SERVER, 'REMOTE_ADDR', '127.0.0.2');

        $newCount = $counter->display();

        self::assertNotEmpty($oldCount);
        self::assertNotEmpty($newCount);
        self::assertNotSame($oldCount, $newCount);
        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $oldCount);
        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $newCount);

        Arrays::set($_SERVER, 'REMOTE_ADDR', '127.0.0.1');
    }

    #[TestDox('Instantiating the FlatfileAdapter with asImage set to true will display the count as images.')]
    public function testWithDefaultOptionsWithImages(): void
    {
        $counter = new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'   => self::$testDirectories['logDir'],
                'imageDir' => self::$testDirectories['imageDir'],
                'asImage'  => true,
            ]
        ));

        $count = $counter->display();

        self::assertNotEmpty($count);
        self::assertTrue(\str_starts_with($count, '<img '));
    }

    #[TestDox('Instantiating the FlatfileAdapter with uniqueOnly set to false properly increments the count.')]
    public function testWithDefaultOptionsWithoutUniqueOnly(): void
    {
        $counter = new FlatfileAdapter(new FlatfileConfiguration(
            [
                'logDir'     => self::$testDirectories['logDir'],
                'imageDir'   => self::$testDirectories['imageDir'],
                'uniqueOnly' => false,
            ]
        ));

        $count    = $counter->display();
        $countTwo = $counter->display();

        self::assertNotSame($count, $countTwo);
        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $count);
        self::assertMatchesRegularExpression('/([A-Za-z]+( [A-Za-z]+)+)\s#[0-9]+/i', $countTwo);
    }
}
