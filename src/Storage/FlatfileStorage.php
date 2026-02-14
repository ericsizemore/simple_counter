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

namespace Esi\SimpleCounter\Storage;

use Esi\SimpleCounter\Configuration\FlatfileConfiguration;
use Esi\SimpleCounter\Interface\StorageInterface;
use Esi\SimpleCounter\Trait\FormatterTrait;
use Esi\Utility\Environment;
use Esi\Utility\Filesystem;
use Esi\Utility\Strings;
use RuntimeException;
use SplFileObject;
use stdClass;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

use function array_filter;
use function array_values;
use function clearstatcache;
use function json_decode;
use function json_encode;

use const DIRECTORY_SEPARATOR;
use const LOCK_EX;
use const LOCK_NB;
use const LOCK_SH;
use const LOCK_UN;

/**
 * @see \Esi\SimpleCounter\Tests\FlatfileStorageTest
 */
final readonly class FlatfileStorage implements StorageInterface
{
    use FormatterTrait;

    public function __construct(private FlatfileConfiguration $configuration)
    {
        $this->validateLogFiles();
    }

    #[\Override]
    public function display(): string
    {
        $this->updateCount();

        return $this->formatDataForDisplay(
            $this->configuration,
            $this->fetchCurrentCount()
        );
    }

    #[\Override]
    public function fetchCurrentCount(): int
    {
        $currentCount = $this->readWrite('logs');

        //@codeCoverageIgnoreStart
        if ($currentCount === false || $currentCount === '') {
            throw new RuntimeException('Unable to retrieve current count information.');
        }
        //@codeCoverageIgnoreEnd

        /** @var object{currentCount: string} $currentCount */
        $currentCount = json_decode((string) $currentCount);

        return (int) $currentCount->currentCount;
    }

    #[\Override]
    public function fetchCurrentIpList(): array
    {
        $currentIpData = $this->readWrite('ips');

        //@codeCoverageIgnoreStart
        if ($currentIpData === false || $currentIpData === '') {
            throw new RuntimeException('Unable to retrieve current ip list information.');
        }
        //@codeCoverageIgnoreEnd

        /** @var object{ipList: array<string>} $currentIpData */
        $currentIpData = json_decode((string) $currentIpData);

        return array_values(array_filter($currentIpData->ipList, static fn (string $value): bool => (trim($value) !== '')));
    }

    #[\Override]
    public function getOption(string $option): null|bool|string
    {
        return $this->configuration::getOption($option);
    }

    /**
     * Handles reading data from, or writing data (with given $data) to, a given file; with file locking.
     *
     * @throws RuntimeException If the file cannot be opened or if a lock is unable to be acquired.
     */
    private function readWrite(string $file, ?string $data = null): false|int|string
    {
        /** @var string $logDir */
        $logDir = $this->configuration::getOption('logDir');
        /** @var string $countFile */
        $countFile = $this->configuration::getOption('countFile');
        /** @var string $ipFile */
        $ipFile = $this->configuration::getOption('ipFile');

        $filePaths = [
            'logs' => \sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $countFile),
            'ips'  => \sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $ipFile),
        ];

        clearstatcache(true, $filePaths[$file]);

        $mode  = 'rb';
        $flags = LOCK_SH | LOCK_NB;

        if ($data !== null) {
            $mode  = 'wb';
            $flags = LOCK_EX | LOCK_NB;
        }

        $fileHandle = new SplFileObject($filePaths[$file], $mode);

        //@codeCoverageIgnoreStart
        $wouldBlock = null;

        if (!$fileHandle->flock($flags, $wouldBlock)) {
            unset($fileHandle);

            if ($wouldBlock) {
                return false;
            }

            throw new RuntimeException(\sprintf("Unable to acquire lock on '%s'.", $file));
        }
        //@codeCoverageIgnoreEnd

        if ($data !== null) {
            $data = $fileHandle->fwrite($data, Strings::length($data));
        } else {
            $data = $fileHandle->fread($fileHandle->getSize());
        }

        $fileHandle->flock(LOCK_UN);
        unset($fileHandle);

        return $data;
    }

    /**
     * Updates the count information, taking into account configuration for 'uniqueOnly'
     * and 'honorDnt'.
     *
     * @throws RuntimeException If self::readWrite() cannot open the counter or ip file,
     *                          or if a file lock is unable to be acquired.
     */
    private function updateCount(): void
    {
        // Honor the Do Not Track setting in the user's browser, if enabled.
        if (
            (bool) $this->configuration::getOption('honorDnt')
            && (int) Environment::var('HTTP_DNT', 0) === 1
        ) {
            return;
        }

        $currentCount = $this->fetchCurrentCount();

        $newCount = $currentCount + 1;
        $newCount = (string) json_encode(['currentCount' => (string) $newCount]);

        if ($this->configuration::getOption('uniqueOnly') === true) {
            $visitorIp = Environment::ipAddress();

            $currentIpData = $this->fetchCurrentIpList();

            if (!\in_array($visitorIp, $currentIpData, true)) {
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
     * @throws InvalidOptionsException If either the 'countFile' or 'ipFile' appears to be an invalid file.
     */
    private function validateLogFiles(): void
    {
        /** @var string $logDir */
        $logDir = $this->configuration::getOption('logDir');

        /** @var string $countFile */
        $countFile = $this->configuration::getOption('countFile');
        $countFile = \sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $countFile);

        /** @var string $ipFile */
        $ipFile = $this->configuration::getOption('ipFile');
        $ipFile = \sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $ipFile);

        if (!Filesystem::isFile($countFile)) {
            throw new InvalidOptionsException(\sprintf("'%s' appears to be an invalid file", $countFile));
        }

        if (!Filesystem::isFile($ipFile)) {
            throw new InvalidOptionsException(\sprintf("'%s' appears to be an invalid file", $ipFile));
        }
    }
}
