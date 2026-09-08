<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Basic\BracesPositionFixer;
use PhpCsFixer\Fixer\Basic\SingleLineEmptyBodyFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Operator\TernaryToElvisOperatorFixer;
use PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitAttributesFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    )
    ->withFileExtensions(['php'])
    ->withCache('var/cache/ecs')
    ->withPreparedSets(
        psr12: true,
        common: true,
        cleanCode: true
    )
    ->withSkip([
        LineLengthFixer::class,
        MethodChainingIndentationFixer::class,
        MethodChainingNewlineFixer::class,
        MultilineWhitespaceBeforeSemicolonsFixer::class,
        BracesPositionFixer::class,
        NotOperatorWithSuccessorSpaceFixer::class,
    ])
    ->withRules([
        ArrayIndentationFixer::class,
        DeclareStrictTypesFixer::class,
        NoUnusedImportsFixer::class,
        NoLeadingImportSlashFixer::class,
        TrimArraySpacesFixer::class,
        PhpUnitSetUpTearDownVisibilityFixer::class,
        TernaryToElvisOperatorFixer::class,
        TernaryToNullCoalescingFixer::class,
        SingleLineEmptyBodyFixer::class,
    ])
    ->withConfiguredRule(GlobalNamespaceImportFixer::class, [])
    ->withConfiguredRule(OrderedImportsFixer::class, [])
    ->withConfiguredRule(FunctionDeclarationFixer::class, [])
    ->withConfiguredRule(
        NoExtraBlankLinesFixer::class,
        [
            'tokens' => [
                'attribute',
                'break',
                'case',
                'comma',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'switch',
                'throw',
                'use',
                'use_trait',
            ],
        ],
    )
    ->withConfiguredRule(ClassAttributesSeparationFixer::class, [
        'elements' => [
            'property' => 'none',
            'method' => 'one',
            'const' => 'none',
        ],
    ])
    ->withConfiguredRule(PhpdocLineSpanFixer::class, [
        'const' => 'single',
        'property' => 'single',
        'method' => 'single',
    ])
    ->withConfiguredRule(PhpUnitAttributesFixer::class, ['keep_annotations' => false]);