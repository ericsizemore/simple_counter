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

namespace Esi\SimpleCounter;

use Esi\Utility\Utility;
use InvalidArgumentException;
use RuntimeException;

use function clearstatcache;
use function count;
use function fclose;
use function filesize;
use function flock;
use function fopen;
use function fread;
use function fwrite;
use function in_array;
use function intval;
use function is_dir;
use function is_file;
use function is_null;
use function is_writable;
use function number_format;
use function preg_split;
use function rtrim;
use function sprintf;
use function strval;
use function trim;

use const LOCK_EX;
use const LOCK_SH;
use const LOCK_UN;
use const PREG_SPLIT_NO_EMPTY;

/**
 * @package Esi\SimpleCounter
 * @version 5.0.0
 */
class Counter
{
    /**
     * Log file locations/paths.
     *
     * The defaults are:
     *    logDir = 'counter/logs';
     *    countFile = '%s/counter.txt';
     *    ipFile = logDir . '%s/ips.txt';
     *
     * Where '%s' will be replaced with logDir upon class initialization.
     *
     */
    protected string $logDir = 'counter/logs';
    protected string $countFile = '%s%scounter.txt';
    protected string $ipFile = '%s%sips.txt';

    /**
     * Use file locking?
     *
     */
    protected bool $useFileLocking = true;

    /**
     * Count only unique visitors?
     *
     */
    protected bool $countOnlyUnique = true;

    /**
     * Show count as images?
     *
     */
    protected bool $useImages = false;

    /**
     * Path to the images.
     *
     * Defaults to counter/images
     *
     */
    protected string $imageDir = 'counter/images';

    /**
     * Image extension.
     *
     */
    protected string $imageExt = '.gif';

    /**
     * Class instance.
     *
     */
    protected static ?Counter $instance = null;

    /**
     * Constructor.
     *
     * Sets user configurable options.
     *
     * @param array<string, string|bool>|null $options
     */
    protected function __construct(?array $options = null)
    {
        self::setOptions($options);

        $this->imageDir = rtrim($this->imageDir, '/\\');

        self::checkDirectories();

        $this->logDir = rtrim($this->logDir, '/\\');
        $this->countFile = sprintf($this->countFile, $this->logDir, DIRECTORY_SEPARATOR);
        $this->ipFile = sprintf($this->ipFile, $this->logDir, DIRECTORY_SEPARATOR);

        self::checkLogFiles();

        if (!Utility::beginsWith($this->imageExt, '.')) {
            $this->imageExt = '.' . $this->imageExt;
        }
    }

    /**
     * Instantiate class instance.
     *
     * @param array<string, string|bool>|null $options
     */
    public static function getInstance(?array $options = null): Counter
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($options);
        }
        return self::$instance;
    }

    /**
     * Processes the visitor (adds to count/etc. if needed) and
     * then returns current count.
     *
     */
    public function process(): string
    {
        $display = '';

        // Update count, but first we need to cast to int
        $count = (int) self::read($this->countFile) + 1;

        // Back to a str value for filesystem functions
        $count = (string) $count;

        // Do we only want to count 'unique' visitors?
        if ($this->countOnlyUnique) {
            $ip = Utility::getIpAddress();

            /** @var string $ips **/
            $ips = self::read($this->ipFile);
            $ips = trim($ips);
            /** @var list<string> $ips **/
            $ips = preg_split("#\n#", $ips, -1, PREG_SPLIT_NO_EMPTY);

            // They've not visited before
            if (!in_array($ip, $ips, true)) {
                self::write($this->ipFile, 'a', "$ip\n");
                self::write($this->countFile, 'w', $count);
            }
            unset($ips);
        } else {
            // No, we wish to count all visitors
            self::write($this->countFile, 'w', $count);
        }

        // Do we want to display the # visitors as graphics?
        if ($this->useImages) {
            /** @var list<string> $count **/
            $count = preg_split('##', $count, -1, PREG_SPLIT_NO_EMPTY);
            $length = count($count);

            for ($i = 0; $i < $length; $i++) {
                $display .= sprintf('<img src="%1$s%4$s%2$d%3$s" alt="%2$d" />&nbsp;', $this->imageDir, $count[$i], $this->imageExt, DIRECTORY_SEPARATOR);
            }
        } else {
            // Nope, let's just show it as plain text
            // Props to Roger Cusson. Adding "You are visitor #" to plain text count.
            // Add number_format, Issue #5
            $display = sprintf('You are visitor #%s', number_format((int) $count));
        }
        return $display;
    }

    /**
     * Helper function. We use this function to open and read files.
     *
     * @param   string  $file  Filename
     * @return  string|false
     *
     * @throws InvalidArgumentException | RuntimeException
     */
    protected function read(string $file): string | false
    {
        if (($fp = fopen($file, 'r')) === false) {
            //@codeCoverageIgnoreStart
            throw new InvalidArgumentException(sprintf("'%s' could not be opened.", $file));
            //@codeCoverageIgnoreEnd
        }

        clearstatcache(true, $file);

        /** @var int<0,max> $filesize **/
        $filesize = filesize($file);

        if ($this->useFileLocking) {
            // If using file locking, we will use shared for read
            if (flock($fp, LOCK_SH)) {
                $return = fread($fp, $filesize);
            } else {
                //@codeCoverageIgnoreStart
                throw new RuntimeException(sprintf("Unable to acquire lock on '%s'", $file));
                //@codeCoverageIgnoreEnd
            }
            // Attempt to release the lock
            flock($fp, LOCK_UN);
        } else {
            $return = fread($fp, $filesize);
        }
        fclose($fp);

        return $return;
    }

    /**
     * Helper function. We use this function to open and write files.
     *
     * @param   string  $file  Filename
     * @param   string  $mode  Mode (w, a)
     * @param   string  $data  If writing to the file, the data to write
     * @return  int|false
     *
     * @throws InvalidArgumentException | RuntimeException
     */
    protected function write(string $file, string $mode, string $data = ''): int | false
    {
        if (($fp = fopen($file, $mode)) === false) {
            //@codeCoverageIgnoreStart
            throw new InvalidArgumentException(sprintf("'%s' could not be opened.", $file));
            //@codeCoverageIgnoreEnd
        }

        clearstatcache(true, $file);

        if ($this->useFileLocking) {
            // If using file locking, we will use exclusive for writes
            if (flock($fp, LOCK_EX)) {
                $return = fwrite($fp, $data);
            } else {
                //@codeCoverageIgnoreStart
                throw new RuntimeException(sprintf("Unable to acquire lock on '%s'", $file));
                //@codeCoverageIgnoreEnd
            }
            // Attempt to release the lock
            flock($fp, LOCK_UN);
        } else {
            $return = fwrite($fp, $data);
        }
        fclose($fp);

        return $return;
    }

    /**
     * Helper function. Verifies that the log files (count file and ip file) are valid files.
     */
    protected function checkLogFiles(): void
    {
        if (
            (!is_file($this->countFile) || !is_writable($this->countFile))
            || (!is_file($this->ipFile) || !is_writable($this->ipFile))
        ) {
            throw new InvalidArgumentException('Please double check your counter and ips files, they are possibly invalid files, or not writable');
        }
    }

    /**
     * Helper function. Verifies that the log and image directories are valid.
     */
    protected function checkDirectories(): void
    {
        if (!is_dir($this->logDir)) {
            throw new InvalidArgumentException('$this->logDir is an invalid directory');
        }

        if (!is_dir($this->imageDir)) {
            throw new InvalidArgumentException('$this->imageDir is an invalid directory');
        }
    }

    /**
     * @param array<string, string|bool>|null $options
     */
    protected function setOptions(?array $options = null): void
    {
        static $optionDefaults = [
            'logDir' => 'counter/logs',
            'useFileLocking' => true,
            'countOnlyUnique' => true,
            'useImages' => false,
            'imageDir' => 'counter/images',
            'imageExt' => '.gif',
        ];

        if ($options !== null) {
            array_walk($options, static fn (string|bool $value, string $key): bool|string => match($key) {
                'useFileLocking', 'countOnlyUnique', 'useImages' => (bool) $value,
                default => $value
            });

            $this->logDir = $options['logDir'] ?? $optionDefaults['logDir'];
            $this->useFileLocking = $options['useFileLocking'] ?? $optionDefaults['useFileLocking'];
            $this->countOnlyUnique = $options['countOnlyUnique'] ?? $optionDefaults['countOnlyUnique'];
            $this->useImages = $options['useImages'] ?? $optionDefaults['useImages'];
            $this->imageDir = $options['imageDir'] ?? $optionDefaults['imageDir'];
            $this->imageExt = $options['imageExt'] ?? $optionDefaults['imageExt'];
        }
    }
}
