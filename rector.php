<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\CodeQuality\Rector\ClassMethod\LocallyCalledStaticMethodToNonStaticRector;
use Rector\Core\ValueObject\PhpVersion;
use Rector\CodeQuality\Rector\Concat\JoinStringConcatRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests/src',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_82);
    $rectorConfig->phpVersion(PhpVersion::PHP_83);
    //$rectorConfig->importNames();

    // here we can define, what sets of rules will be applied
    // tip: use "SetList" class to autocomplete sets with your IDE
    $rectorConfig->sets([
        //SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        //SetList::CODING_STYLE,
        //SetList::TYPE_DECLARATION,
        //SetList::NAMING,
        //SetList::PRIVATIZATION,
        //SetList::EARLY_RETURN,
        LevelSetList::UP_TO_PHP_83,
        PHPUnitSetList::PHPUNIT_100,
    ]);
    $rectorConfig->skip([
        LocallyCalledStaticMethodToNonStaticRector::class,
        JoinStringConcatRector::class,
    ]);
};