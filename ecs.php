<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer;
use PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer;
use PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        // __DIR__ . '/admin-dev',
        // __DIR__ . '/app',
        // __DIR__ . '/classes',
        // __DIR__ . '/config',
        // __DIR__ . '/controllers',
        // __DIR__ . '/install-dev',
        // __DIR__ . '/src/Adapter',
        // __DIR__ . '/src/Core',
        __DIR__ . '/src/PrestaShopBundle',
        // __DIR__ . '/tests',
        // __DIR__ . '/tools',
        // __DIR__ . '/webservice',
    ])
    ->withPhpCsFixerSets(
        symfony: true,
    )
    ->withPreparedSets(
        psr12: true,
        common: true,
        symplify: true,
        strict: true,
    )
    ->withRules([
        CombineConsecutiveUnsetsFixer::class,
        MbStrFunctionsFixer::class,
        NativeConstantInvocationFixer::class,
        NoSuperfluousElseifFixer::class,
        PhpdocOrderFixer::class,
    ])
    ->withConfiguredRule(GlobalNamespaceImportFixer::class, [
        'import_classes' => true,
    ])
    ->withConfiguredRule(NativeFunctionInvocationFixer::class, [
        'include' => ['@compiler_optimized'],
        'scope' => 'namespaced',
        'strict' => true,
    ])
    ->withSkip([
        ArrayListItemNewlineFixer::class,
        ArrayOpenerAndCloserNewlineFixer::class,
        DeclareStrictTypesFixer::class,
        ExplicitStringVariableFixer::class,
        GeneralPhpdocAnnotationRemoveFixer::class,
        LineLengthFixer::class,
        MethodChainingNewlineFixer::class,
        PhpdocSummaryFixer::class,
        PhpUnitTestClassRequiresCoversFixer::class,
    ])
;
