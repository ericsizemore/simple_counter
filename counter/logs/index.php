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
\session_start([
    'serialize_handler' => 'php_serialize',
    'use_trans_sid'     => 0,
    'use_strict_mode'   => 1,
    'use_only_cookies'  => 1,
    'use_cookies'       => 1
]);

/**
 * Sanity checks for login information.
 *
 * @return array
 * @throws \Exception
 */
function loginInfo(): array
{
    $loginInfo = \parse_ini_file(__DIR__ . '/credentials.env');

    if (empty($loginInfo) OR !isset($loginInfo['login_user'], $loginInfo['login_pass'], $loginInfo['login_algo'])) {
        throw new \Exception('Invalid or missing credentials in environment file.');
    }

    $isHash = \password_get_info($loginInfo['login_pass']);

    if ($isHash['algo'] == 0 OR $isHash['algoName'] == 'unknown') {
        throw new \Exception('Please update your password within the environment file. No plaintext password please.');
    }

    if ($isHash['algoName'] != $loginInfo['login_algo']) {
        throw new \Exception('Algorithm mismatch detected.');
    }

    if (!\in_array($loginInfo['login_algo'], [\PASSWORD_BCRYPT, \PASSWORD_ARGON2I, \PASSWORD_ARGON2ID])) {
        throw new \Exception(
            'Invalid algorithm detected. Must be one of: "' . \PASSWORD_BCRYPT . '", "' . \PASSWORD_ARGON2I . '", "' . \PASSWORD_ARGON2ID . '".' . 
            'If you need to create a password hash, try using PHP\'s password_hash() function and updating your environment file. ' . 
            'See: https://www.php.net/manual/en/function.password-hash.php'
        );
    }
    return $loginInfo;
}

$loginInfo = loginInfo();

if (
    !isset($_SESSION['loggedin'], $_SESSION['loggedinHash']) OR 
    $_SESSION['loggedIn'] != true OR 
    $_SESSION['loggedinHash'] != \md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])
) {
    $_SESSION = [];
    \session_destroy();

    \header('Location: ' . __DIR__ . '/index.php?do=login');
    exit;
}

$action = \filter_input(INPUT_GET, 'do');

switch ($action) {
    case 'login':
    case 'logout':
    case 'index':
    break;
}