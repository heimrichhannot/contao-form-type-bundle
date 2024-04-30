<?php

declare(strict_types=1);

use Contao\Rector\Set\ContaoLevelSetList;
use Contao\Rector\Set\ContaoSetList;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../../src',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
    ])
    ->withSkip([
        \Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector::class,
    ])
    ->withSets([
        SetList::PHP_81,
        LevelSetList::UP_TO_PHP_81,
        SymfonySetList::SYMFONY_44,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        ContaoSetList::CONTAO_49,
        ContaoSetList::FQCN,
        ContaoLevelSetList::UP_TO_CONTAO_49,
    ]);
