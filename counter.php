<?php

/**
* @author    Eric Sizemore <admin@secondversion.com>
* @package   SV's Simple Counter
* @link      http://www.secondversion.com
* @version   2.0.1
* @copyright (C) 2006 - 2012 Eric Sizemore
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

// #################### Define Important Constants ####################
// There should be no need to edit these
define('COUNT_FILE', 'counter/logs/counter.txt');
define('IP_FILE', 'counter/logs/ips.txt');

// ######################## USER CONFIGURATION ########################
// Edit the following.. true = yes, false = no

// Use file locking?
define('USE_FLOCK', true);

// Count only unique visitors?
define('ONLY_UNIQUE', true);

// Show count as images?
define('USE_IMAGES', false);

// Path to the images
define('IMG_DIR', 'counter/images/');

// Image extension
define('IMG_EXT', '.gif');

// ############################ Functions #############################
/**
* We use this function to open, read/write to files.
*
* @param  string   Filename
* @param  string   Mode (r, w, a, etc..)
* @param  string   If writing to the file, the data to write
* @return mixed
*/
function fp($file, $mode, $data = '')
{
	if (!file_exists($file) OR !is_writable($file))
	{
		die("Error: '<code>$file</code>' does not exist or is not writable.");
	}

	if (!($fp = @fopen($file, $mode)))
	{
		die("Error: '<code>$file</code>' could not be opened.");
	}

	if (USE_FLOCK AND @flock($fp, LOCK_EX))
	{
		if ($mode == 'r')
		{
			return @fread($fp, filesize($file));
		}
		else
		{
			@fwrite($fp, $data);
		}
		@flock($fp, LOCK_UN);
	}
	else
	{
		if ($mode == 'r')
		{
			return @fread($fp, filesize($file));
		}
		@fwrite($fp, $data);
	}
	@fclose($fp);
}

/**
* Get the users ip address.
*
* @param  none
* @return string
*/
function get_ip()
{
	$ip = my_getenv('REMOTE_ADDR');

	if (my_getenv('HTTP_X_FORWARDED_FOR'))
	{
		if (preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', my_getenv('HTTP_X_FORWARDED_FOR'), $matches))
		{
			foreach ($matches[0] AS $match)
			{
				if (!preg_match('#^(10|172\.16|192\.168)\.#', $match))
				{
					$ip = $match;
					break;
				}
			}
			unset($matches);
		}
	}
	else if (my_getenv('HTTP_CLIENT_IP'))
	{
		$ip = my_getenv('HTTP_CLIENT_IP');
	}
	else if (my_getenv('HTTP_FROM'))
	{
		$ip = my_getenv('HTTP_FROM');
	}
	return $ip;
}

/**
* Returns an environment variable. Based on PMA_getenv from phpMyAdmin.
*
* @param  string  Variable name, eg: PHP_SELF
* @return string
*/
function my_getenv($varname)
{
	if (isset($_SERVER[$varname]))
	{
		return $_SERVER[$varname];
	}
	else if (isset($_ENV[$varname]))
	{
		return $_ENV[$varname];
	}
	else if (getenv($varname))
	{
		return getenv($varname);
	}
	return '';
}

// ######################## Start Main Script #########################
// Get current count
$count = fp(COUNT_FILE, 'r');

// Do we only want to count 'unique' visitors?
if (ONLY_UNIQUE)
{
	// Get visitor ip and check against our ip log
	$ip = get_ip();

	$ips = trim(fp(IP_FILE, 'r'));
	$ips = preg_split("#\n#", $ips, -1, PREG_SPLIT_NO_EMPTY);

	$visited = (bool)(in_array($ip, $ips));

	// They've not visited before
	if (!$visited)
	{
		fp(IP_FILE, 'a', "$ip\n");
		fp(COUNT_FILE, 'w', $count + 1);
	}
	// Memory saving
	unset($ips);
}
else
{
	// No, we wish to count all visitors
	fp(COUNT_FILE, 'w', $count + 1, USE_FLOCK);
}

// Do we want to display the # visitors as graphics?
if (USE_IMAGES)
{
	$count = preg_split("##", $count, -1, PREG_SPLIT_NO_EMPTY);
	$len = count($count);

	$display = '';

	for ($i = 0; $i < $len; $i++)
	{
		$display .= '<img src="' . IMG_DIR . $count[$i] . IMG_EXT . '" border="0" alt="' . $count[$i] . '" />&nbsp;';
	}
	echo $display;
}
else
{
	// Nope, let's just show it as plain text
	echo $count;
}

?>