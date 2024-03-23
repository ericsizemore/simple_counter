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

namespace Esi\SimpleCounter\Storage;

use Esi\SimpleCounter\Trait\FormatterTrait;
use Esi\SimpleCounter\Configuration\FlatfileConfiguration;
use Esi\SimpleCounter\Interface\StorageInterface;
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
use function in_array;
use function json_decode;
use function json_encode;
use function sprintf;

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
        $currentCount = $this->readWrite('logs');

        //@codeCoverageIgnoreStart
        if ($currentCount === false || $currentCount === '') {
            throw new RuntimeException('Unable to retrieve current count information.');
        }
        //@codeCoverageIgnoreEnd

        /** @var stdClass $currentCount */
        $currentCount = json_decode((string) $currentCount);

        return (int) $currentCount->currentCount;
    }

    public function fetchCurrentIpList(): array
    {
        $currentIpData = $this->readWrite('ips');

        //@codeCoverageIgnoreStart
        if ($currentIpData === false || $currentIpData === '') {
            throw new RuntimeException('Unable to retrieve current ip list information.');
        }
        //@codeCoverageIgnoreEnd

        /** @var stdClass $currentIpData */
        $currentIpData = json_decode((string) $currentIpData);

        return array_values(array_filter($currentIpData->ipList));
    }

    public function getOption(string $option): string | bool | null
    {
        return $this->configuration::getOption($option);
    }

    /**
     * Performs a read operation on a given file, using file locking.
     *
     * @throws RuntimeException If the file cannot be opened or if a lock is unable to be acquired.
     */
    private static function fileRead(string $file): string | false
    {
        clearstatcache(true, $file);

        $fileHandle = new SplFileObject($file, 'rb');

        //@codeCoverageIgnoreStart
        if (!$fileHandle->flock(LOCK_SH | LOCK_NB)) {
            throw new RuntimeException(sprintf("Unable to acquire lock while attempting to read '%s'.", $file));
        }
        //@codeCoverageIgnoreEnd

        $data = $fileHandle->fread($fileHandle->getSize());

        $fileHandle->flock(LOCK_UN);
        $fileHandle = null;

        return $data;
    }

    /**
     * Performs a write operation on a given file with given $data, using file locking.
     *
     * @throws RuntimeException If the file cannot be opened or if a lock is unable to be acquired.
     */
    private static function fileWrite(string $file, string $data): int
    {
        clearstatcache(true, $file);

        $fileHandle = new SplFileObject($file, 'wb');

        //@codeCoverageIgnoreStart
        if (!$fileHandle->flock(LOCK_EX | LOCK_NB)) {
            throw new RuntimeException(sprintf("Unable to acquire lock while attempting to write '%s'.", $file));
        }
        //@codeCoverageIgnoreEnd

        $data = $fileHandle->fwrite($data, Strings::length($data));

        $fileHandle->flock(LOCK_UN);
        $fileHandle = null;

        return $data;
    }

    /**
     * Handles reading data from, or writing data (with given $data) to, a given file.
     *
     * @throws RuntimeException If the file cannot be opened or if a lock is unable to be acquired.
     */
    private function readWrite(string $file, ?string $data = null): string | false | int
    {
        /** @var string $logDir */
        $logDir = $this->configuration::getOption('logDir');

        /** @var string $countFile */
        $countFile = $this->configuration::getOption('countFile');

        /** @var string $ipFile */
        $ipFile = $this->configuration::getOption('ipFile');

        $filePaths = [
            'logs' => sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $countFile),
            'ips'  => sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $ipFile),
        ];

        if ($data === null) {
            return FlatfileStorage::fileRead($filePaths[$file]);
        }

        return FlatfileStorage::fileWrite($filePaths[$file], $data);
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
        $isDnt = (int) Environment::var('HTTP_DNT', 0);

        /** @var bool $honorDnt */
        $honorDnt = $this->configuration::getOption('honorDnt');

        if ($honorDnt && $isDnt === 1) {
            return;
        }

        $currentCount = $this->fetchCurrentCount();

        $newCount = $currentCount + 1;
        $newCount = (string) json_encode(['currentCount' => (string) $newCount]);

        if ($this->configuration::getOption('uniqueOnly') === true) {
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
     * @throws InvalidOptionsException If either the 'countFile' or 'ipFile' appears to be an invalid file.
     */
    private function validateLogFiles(): void
    {
        /** @var string $logDir */
        $logDir = $this->configuration::getOption('logDir');

        /** @var string $countFile */
        $countFile = $this->configuration::getOption('countFile');
        $countFile = sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $countFile);

        /** @var string $ipFile */
        $ipFile = $this->configuration::getOption('ipFile');
        $ipFile = sprintf('%s%s%s', $logDir, DIRECTORY_SEPARATOR, $ipFile);

        if (!Filesystem::isFile($countFile)) {
            throw new InvalidOptionsException(sprintf("'%s' appears to be an invalid file", $countFile));
        }

        if (!Filesystem::isFile($ipFile)) {
            throw new InvalidOptionsException(sprintf("'%s' appears to be an invalid file", $ipFile));
        }
    }
}
