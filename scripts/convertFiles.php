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
/**
 * Edit paths below, if needed.
 */

// Update the location to your current log files, if needed.
$oldCounterFile = \dirname(__DIR__) . '/counter/counter.txt';
$oldIpFile      = \dirname(__DIR__) . '/counter/ips.txt';

// Update the location where the new files will be placed, if needed.
$newCounterFile = \dirname(__DIR__) . '/counter/counter.json';
$newIpFile      = \dirname(__DIR__) . '/counter/ips.json';

/**
 * NO EDITING BEYOND THIS POINT.
 */
checkFileLocations($oldCounterFile, $oldIpFile);
convertCounterFile($oldCounterFile, $newCounterFile);
convertIpFile($oldIpFile, $newIpFile);

/**
 * Helper function to convert count data.
 */
function convertCounterFile(string $oldCounterFile, string $newCounterFile): void
{
    echo "Checking counter.txt ...<br/>\n";

    $countData = \trim(\file_get_contents($oldCounterFile));

    if ($countData === '') {
        echo "No count data found, creating default data...<br/>\n";
        $countData = '0';
    } else {
        echo "Converting data...<br/>\n";
    }

    $countData = ['currentCount' => $countData];

    $bytesWritten = \file_put_contents($newCounterFile, \json_encode($countData), \LOCK_EX);

    if ($bytesWritten === false) {
        echo "Unable to update $newCounterFile<br/>\n";
    } else {
        echo "Count data conversion complete.<br/>\n<br/>\n";
    }
}

/**
 * Helper function to convert IP data.
 */
function convertIpFile(string $oldIpFile, string $newIpFile): void
{
    echo "Checking ips.txt ...<br/>\n";

    $ipData = \trim(\file_get_contents($oldIpFile));
    $ipData = \preg_split("#\n#", $ipData, -1, \PREG_SPLIT_NO_EMPTY);

    if ($ipData === []) {
        echo "No IP data found, creating default data...<br/>\n";
        $ipData = [""];
    } else {
        echo "Converting data...<br/>\n";
    }

    $ipData = ['ipList' => $ipData];

    $bytesWritten = \file_put_contents($newIpFile, \json_encode($ipData), \LOCK_EX);

    if ($bytesWritten === false) {
        echo "Unable to update $newIpFile<br/>\n";
    } else {
        echo "IP data conversion complete.<br/>\n<br/>\n";
    }
}

/**
 * Helper function to check file existence.
 */
function checkFileLocations(string $firstFile, string $secondFile): void
{
    if (!\file_exists($firstFile)) {
        throw new \RuntimeException(\sprintf("'%s' file could not be found.", $firstFile));
    }

    if (!\file_exists($secondFile)) {
        throw new \RuntimeException(\sprintf("'%s' file could not be found.", $secondFile));
    }
}
