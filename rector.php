<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->phpVersion(phpVersion: PhpVersion::PHP_80);
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses();
    $rectorConfig->parallel();
//    $rectorConfig->symfonyContainerPhp(filePath: __DIR__ . 'var/cache/dev/App_KernelDevDebugContainer.php');

    $rectorConfig->autoloadPaths(autoloadPaths: [
        __DIR__ . '/vendor/autoload.php',
    ]);

    $rectorConfig->paths(paths: [
        __DIR__ . '/lib',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->skip(criteria: [
        __DIR__ . '/vendor',
    ]);

    $rectorConfig->sets(sets: [
//        SetList::CODE_QUALITY,
//        SetList::CODING_STYLE,
//        SetList::DEAD_CODE,
//        SetList::EARLY_RETURN,
        SetList::PHP_80,
//        SetList::PSR_4,
//        SetList::PRIVATIZATION,
//        SetList::TYPE_DECLARATION,
//        SetList::TYPE_DECLARATION_STRICT,
//        SymfonySetList::SYMFONY_STRICT,
//        SymfonySetList::SYMFONY_60,
//        SymfonySetList::SYMFONY_CODE_QUALITY,
//        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
#SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
    ]);

};
