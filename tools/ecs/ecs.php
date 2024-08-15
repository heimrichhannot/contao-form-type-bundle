<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Basic\BracesPositionFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\TypeDeclarationSpacesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/../../src',
    ])

    // add a single rule
    ->withRules([
        NoUnusedImportsFixer::class,
        BracesPositionFixer::class,
    ])

    // add sets - group of rules
    ->withPreparedSets(
         arrays: true,
         namespaces: true,
         spaces: true,
         docblocks: true,
         comments: true,
//        psr12: true,
     )
    ->withSkip([
        NotOperatorWithSuccessorSpaceFixer::class
    ])
     
     ;
