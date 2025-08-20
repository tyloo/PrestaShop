<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        // __DIR__ . '/admin-dev',
        // __DIR__ . '/app',
        // __DIR__ . '/classes',
        // __DIR__ . '/config',
        // __DIR__ . '/controllers',
        // __DIR__ . '/install-dev',
        __DIR__ . '/src',
        // __DIR__ . '/tests',
        // __DIR__ . '/tools',
        // __DIR__ . '/webservice',
    ])

    ->withRules([
        NoUnusedImportsFixer::class,
    ])

    // add sets - group of rules, from easiest to more complex ones
    // uncomment one, apply one, commit, PR, merge and repeat
    //->withPreparedSets(
    //      spaces: true,
    //      namespaces: true,
    //      docblocks: true,
    //      arrays: true,
    //      comments: true,
    //)
;
