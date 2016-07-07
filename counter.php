<?php

/**
* @author    Eric Sizemore <admin@secondversion.com>
* @package   SV's Simple Counter
* @link      http://www.secondversion.com/downloads/
* @version   4.0.0
* @copyright (C) 2006 - 2016 Eric Sizemore
* @license   GNU Lesser General Public License
*
*	SV's Simple Counter is free software: you can redistribute it and/or modify
*	it under the terms of the GNU Lesser General Public License as published by
*	the Free Software Foundation, either version 3 of the License, or
*	(at your option) any later version.
*
*	This program is distributed in the hope that it will be useful, but WITHOUT 
*	ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
*	FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more 
*	details.
*
*	You should have received a copy of the GNU Lesser General Public License
*	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
namespace Esi\SimpleCounter;

/** */
class Counter {
	/** User Configuration **/
	// Log file locations/paths.
	const COUNT_FILE  = 'counter/logs/counter.txt';
	const IP_FILE     = 'counter/logs/ips.txt';

	// Use file locking?
	const USE_FLOCK   = true;

	// Count only unique visitors?
	const ONLY_UNIQUE = true;

	// Show count as images?
	const USE_IMAGES  = false;

	// Path to the images.
	const IMAGE_DIR   = 'counter/images/';

	// Image extension.
	const IMAGE_EXT   = '.gif';
	/** End User Configuration **/

	/**
	* Class instance.
	*
	* @var	object	\SimpleCounter\Counter()
	*/
	private static $instance;

	/**
	* Constructor.
	*
	* @param	void
	* @return	void
	*/
	private function __construct() {}

	/**
	* Instantiate class instance.
	*
	* @param	void
	* @return	object	\SimpleCounter\Counter()
	*/
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	* Return the visitor's IP address.
	*
	* @param	$trust_proxy_headers	Whether or not to trust the proxy headers HTTP_CLIENT_IP
	*									and HTTP_X_FORWARDED_FOR.
	* @return	string
	*/
	private function getIpAddress($trust_proxy_headers = false) {
		$ip = $_SERVER['REMOTE_ADDR'];

		if ($trust_proxy_headers === false) {
			return $ip;
		}

		$ips = [];

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		}
		else if (isset($_SERVER['HTTP_X_REAL_IP']))
		{
			$ips = explode(',', $_SERVER['HTTP_X_REAL_IP']);
		}

		if (!empty($ips)) {
			foreach ($ips AS $val) {
				$val = trim($val);

				if (inet_ntop(inet_pton($val)) == $val AND !preg_match("#^(10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.|192\.168\.|fe80:|fe[c-f][0-f]:|f[c-d][0-f]{2}:)#i", $val)) {
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

	/**
	* We use this function to open and read/write to files.
	*
	* @param	string	$file	Filename
	* @param	string	$mode	Mode (r, w, a, etc..)
	* @param	string	$data	If writing to the file, the data to write
	* @return	mixed
	*/
	private function readWriteFile($file, $mode, $data = '') {
		if (!file_exists($file) OR !is_writable($file)) {
			throw new \Exception(__CLASS__ . "\\readWriteFile() - '$file' does not exist or is not writable.");
		}

		if (!($fp = fopen($file, $mode))) {
			throw new \Exception(__CLASS__ . "\\readWriteFile() - '$file' could not be opened.");
		}

		if (self::USE_FLOCK AND flock($fp, LOCK_EX)) {
			if ($mode == 'r') {
				return fread($fp, filesize($file));
			}
			else {
				fwrite($fp, $data);
			}
			flock($fp, LOCK_UN);
		}
		else {
			if ($mode == 'r') {
				return fread($fp, filesize($file));
			}
			fwrite($fp, $data);
		}
		fclose($fp);
	}

	/**
	* Processes the visitor (adds to count/etc. if needed) and 
	* then displays current count.
	*
	* @param	void
	* @return	string	
	*/
	public function process() {
		$display = '';

		$count = self::readWriteFile(self::COUNT_FILE, 'r');

		// Do we only want to count 'unique' visitors?
		if (self::ONLY_UNIQUE) {
			$ip = self::getIpAddress();

			$ips = trim(self::readWriteFile(self::IP_FILE, 'r'));
			$ips = preg_split("#\n#", $ips, -1, PREG_SPLIT_NO_EMPTY);

			// They've not visited before
			if (!in_array($ip, $ips)) {
				self::readWriteFile(self::IP_FILE, 'a', "$ip\n");
				self::readWriteFile(self::COUNT_FILE, 'w', $count + 1);
			}
			unset($ips);
		}
		else {
			// No, we wish to count all visitors
			self::readWriteFile(self::COUNT_FILE, 'w', $count + 1);
		}

		// Do we want to display the # visitors as graphics?
		if (self::USE_IMAGES) {
			$count = preg_split('##', $count, -1, PREG_SPLIT_NO_EMPTY);
			$length = count($count);

			for ($i = 0; $i < $length; $i++) {
				$display .= '<img src="' . self::IMAGE_DIR . $count[$i] . self::IMAGE_EXT . '" border="0" alt="' . $count[$i] . '" />&nbsp;';
			}
		}
		else {
			// Nope, let's just show it as plain text
			// Props to Roger Cusson. Adding "You are visitor #" to plain text count.
			$display = "You are visitor #$count";
		}
		echo $display;
	}
}

// Instantiate and process.
\Esi\SimpleCounter\Counter::getInstance()->process();
