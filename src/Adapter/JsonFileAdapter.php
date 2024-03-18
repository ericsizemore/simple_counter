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

namespace Esi\SimpleCounter\Adapter;

use Esi\SimpleCounter\Adapter\FormatterTrait;
use Esi\SimpleCounter\Configuration\JsonFileConfiguration;
use Esi\SimpleCounter\Interface\CounterInterface;
use Esi\Utility\Environment;
use Esi\Utility\Filesystem;
use stdClass;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

use function array_filter;
use function array_values;
use function in_array;
use function json_decode;
use function json_encode;
use function sprintf;

use const DIRECTORY_SEPARATOR;
use const LOCK_EX;

/**
 * @phpstan-import-type JsonFileOptions from JsonFileConfiguration
 * @see \Esi\SimpleCounter\Tests\JsonFileAdapterTest
 */
final readonly class JsonFileAdapter implements CounterInterface
{
    use FormatterTrait;

    public function __construct(private JsonFileConfiguration $configuration)
    {
        $this->validateLogFiles();
    }

    public function display(): string
    {
        $this->updateCount();

        return $this->formatDataForDisplay(
            $this->configuration,
            $this->fetchCurrentCount()
        );
    }

    public function fetchCurrentCount(): int
    {
        /** @var string $currentCount */
        $currentCount = $this->readWrite('logs');

        /** @var stdClass $currentCount */
        $currentCount = (object) json_decode($currentCount);

        /** @var int $currentCount */
        $currentCount = (int) $currentCount->currentCount;

        return $currentCount;
    }

    public function fetchCurrentIpList(): array
    {
        /** @var string $currentIpData */
        $currentIpData = $this->readWrite('ips');

        /** @var stdClass $currentIpData */
        $currentIpData = json_decode($currentIpData);

        /** @var list<string> $currentIpData */
        $currentIpData = $currentIpData->ipList;

        return array_values(array_filter($currentIpData));
    }

    /**
     * Handles reading data from, or writing data (with given $data) to, a given file.
     */
    private function readWrite(string $file, ?string $data = null): string | false | int
    {
        /** @var string $logDir */
        $logDir = $this->configuration->getOption('logDir');

        /** @var string $countFile */
        $countFile = $this->configuration->getOption('countFile');

        /** @var string $ipFile */
        $ipFile = $this->configuration->getOption('ipFile');

        $filePaths = [
            'logs' => sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $countFile),
            'ips'  => sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $ipFile),
        ];

        if ($data === null) {
            return Filesystem::fileRead($filePaths[$file]);
        }

        return Filesystem::fileWrite($filePaths[$file], $data, LOCK_EX);
    }

    /**
     * Updates the count information, taking into account configuration for 'uniqueOnly'.
     */
    private function updateCount(): void
    {
        $currentCount = $this->fetchCurrentCount();

        $newCount = $currentCount + 1;
        $newCount = (string) json_encode(['currentCount' => (string) $newCount]);

        if ($this->configuration->getOption('uniqueOnly') === true) {
            $visitorIp = Environment::ipAddress();

            $currentIpData = $this->fetchCurrentIpList();

            if (!in_array($visitorIp, $currentIpData, true)) {
                $currentIpData[] = $visitorIp;

                $newIpList = (string) json_encode(['ipList' => $currentIpData]);
                $this->readWrite('ips', $newIpList);
                $this->readWrite('logs', $newCount);
            }

            unset($currentIpData);
        } else {
            $this->readWrite('logs', $newCount);
        }
    }

    /**
     * Validates that the given log files are valid files.
     *
     * @throws InvalidOptionsException
     */
    private function validateLogFiles(): void
    {
        /** @var string $logDir */
        $logDir = $this->configuration->getOption('logDir');

        /** @var string $countFile */
        $countFile = $this->configuration->getOption('countFile');
        $countFile = sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $countFile);

        /** @var string $ipFile */
        $ipFile = $this->configuration->getOption('ipFile');
        $ipFile = sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $ipFile);

        if (!Filesystem::isFile($countFile)) {
            throw new InvalidOptionsException(sprintf("'%s' appears to be an invalid file", $countFile));
        }

        if (!Filesystem::isFile($ipFile)) {
            throw new InvalidOptionsException(sprintf("'%s' appears to be an invalid file", $ipFile));
        }
    }
}