<?php

/**
 * SV's Simple Counter - A simple web hit counter.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   SV's Simple Counter
 * @link      https://www.secondversion.com/
 * @version   4.0.6
 * @copyright (C) 2006 - 2023 Eric Sizemore
 * @license   GNU Lesser General Public License
 */
namespace Esi\SimpleCounter;

use Exception;

/**
 * SV's Simple Counter - A simple web hit counter.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @package   SV's Simple Counter
 * @link      https://www.secondversion.com/
 * @version   4.0.6
 * @copyright (C) 2006 - 2023 Eric Sizemore
 * @license   GNU Lesser General Public License
 *
 * Copyright (C) 2006 - 2023 Eric Sizemore. All rights reserved.
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
class Counter
{
    /** User Configuration **/

    // Log file locations/paths.
    private const COUNT_FILE  = 'counter/logs/counter.txt';
    private const IP_FILE     = 'counter/logs/ips.txt';

    // Use file locking?
    private const USE_FLOCK   = true;

    // Count only unique visitors?
    private const ONLY_UNIQUE = true;

    // Show count as images?
    private const USE_IMAGES  = false;

    // Path to the images.
    private const IMAGE_DIR   = 'counter/images/';

    // Image extension.
    private const IMAGE_EXT   = '.gif';

    /** End User Configuration **/

    /**
    * Class instance.
    *
    * @var  object  \SimpleCounter\Counter()
    */
    private static $instance;

    /**
    * Constructor.
    */
    private function __construct()
    {
        //
    }

    /**
    * Instantiate class instance.
    *
    * @return  object  \SimpleCounter\Counter()
    */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
    * Return the visitor's IP address.
    *
    * @param   bool    $trustProxyHeaders  Whether or not to trust the proxy headers HTTP_CLIENT_IP
    *                                      and HTTP_X_FORWARDED_FOR.
    * @return  string
    */
    private function getIpAddress(bool $trustProxyHeaders = false): string
    {
        // Pretty self-explanatory. Try to get an 'accurate' IP
        $ip = $_SERVER['REMOTE_ADDR'];

        if ($trustProxyHeaders) {
            return $ip;
        }

        $ips = [];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = \explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ips = \explode(',', $_SERVER['HTTP_X_REAL_IP']);
        }

        $ips = \array_map('trim', $ips);

        if (!empty($ips)) {
            foreach ($ips AS $val) {
                if (\inet_ntop(\inet_pton($val)) == $val AND self::isPublicIp($val)) {
                    $ip = $val;
                    break;
                }
            }
        }

        if (!$ip AND isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        return $ip;
    }

    // --- getIpAddress helpers --- //
    /**
     * isPrivateIp()
     *
     * Determines if an IP address is within the private range.
     *
     * @param   string  $ipaddress  IP address to check.
     * @return  bool
     */
    private function isPrivateIp(string $ipaddress): bool
    {
        return !(bool)\filter_var(
            $ipaddress, 
            \FILTER_VALIDATE_IP, 
            \FILTER_FLAG_IPV4 | \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_PRIV_RANGE
        );
    }

    /**
     * isReservedIp()
     *
     * Determines if an IP address is within the reserved range.
     *
     * @param   string  $ipaddress  IP address to check.
     * @return  bool
     */
    private function isReservedIp(string $ipaddress): bool
    {
        return !(bool)\filter_var(
            $ipaddress, 
            \FILTER_VALIDATE_IP, 
            \FILTER_FLAG_IPV4 | \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * isPublicIp()
     *
     * Determines if an IP address is not within the private or reserved ranges.
     *
     * @param   string  $ipaddress  IP address to check.
     * @return  bool
     */
    private function isPublicIp(string $ipaddress): bool
    {
        return (!self::isPrivateIp($ipaddress) AND !self::isReservedIp($ipaddress));
    }

    /**
    * We use this function to open and read/write to files.
    *
    * @param   string  $file  Filename
    * @param   string  $mode  Mode (r, w, a)
    * @param   string  $data  If writing to the file, the data to write
    * @return  string|false
    *
    * @throws Exception
    */
    private function readWriteFile(string $file, string $mode, string $data = ''): string|false
    {
        if (!\file_exists($file) OR !\is_writable($file)) {
            throw new Exception(\sprintf("'%s' does not exist or is not writable.", $file));
        }

        if (!($fp = \fopen($file, $mode))) {
            throw new Exception(\sprintf("'%s' could not be opened.", $file));
        }

        $return = '';

        \clearstatcache();

        //
        $filesize = \filesize($file);

        if (self::USE_FLOCK) {
            // If using file locking, we will use shared for 'r' mode or exclusive for 'w' or 'a' mode
            switch ($mode) {
                case 'r':
                    // Shared lock
                    if (\flock($fp, \LOCK_SH)) {
                        $return = \fread($fp, $filesize);
                    } else {
                        throw new Exception(\sprintf("Unable to acquire lock on '%s'", $file));
                    }
                break;
                case 'a':
                case 'w':
                    // Exclusive lock
                    if (\flock($fp, \LOCK_EX)) {
                        \fwrite($fp, $data);
                    } else {
                        throw new Exception(\sprintf("Unable to acquire lock on '%s'", $file));
                    }
                break;
                default:
                    // Invalid mode
                    throw new Exception(\sprintf("Invalid mode '%s' specified, must be either read ('r') or write ('w'/'a')", $mode));
            }
            // Attempt to release the lock
            \flock($fp, \LOCK_UN);
        } else {
            // We are not using file locks
            if ($mode == 'r') {
                $return = \fread($fp, $filesize);
            } else {
                \fwrite($fp, $data);
            }
        }
        \fclose($fp);

        return $return;
    }

    /**
     * Gathers a list of IPs and the number of times they occur from the ip log file.
     *
     * ** currently not in use, to be used for an admin page **
     *
     * @return  array
     */
    public function listIps(): array
    {
        $ips = \trim(self::readWriteFile(self::IP_FILE, 'r'));
        $ips = \preg_split("# #", $ips, -1, \PREG_SPLIT_NO_EMPTY);

        return \array_count_values($ips);
    }

    /**
    * Processes the visitor (adds to count/etc. if needed) and 
    * then displays current count.
    */
    public function process(): void
    {
        $display = '';

        $count = self::readWriteFile(self::COUNT_FILE, 'r');

        // Do we only want to count 'unique' visitors?
        if (self::ONLY_UNIQUE) {
            $ip = self::getIpAddress();

            $ips = \trim(self::readWriteFile(self::IP_FILE, 'r'));
            $ips = \preg_split("#\n#", $ips, -1, \PREG_SPLIT_NO_EMPTY);

            // They've not visited before
            if (!\in_array($ip, $ips)) {
                self::readWriteFile(self::IP_FILE, 'a', "$ip\n");
                self::readWriteFile(self::COUNT_FILE, 'w', $count + 1);
            }
            unset($ips);
        } else {
            // No, we wish to count all visitors
            self::readWriteFile(self::COUNT_FILE, 'w', $count + 1);
        }

        // Do we want to display the # visitors as graphics?
        if (self::USE_IMAGES) {
            $count = \preg_split('##', $count, -1, \PREG_SPLIT_NO_EMPTY);
            $length = \count($count);

            for ($i = 0; $i < $length; $i++) {
                $display .= '<img src="' . self::IMAGE_DIR . $count[$i] . self::IMAGE_EXT . '" border="0" alt="' . $count[$i] . '" />&nbsp;';
            }
        } else {
            // Nope, let's just show it as plain text
            // Props to Roger Cusson. Adding "You are visitor #" to plain text count.
            $display = "You are visitor #$count";
        }
        echo $display;
    }
}

// Instantiate and process.
Counter::getInstance()->process();
