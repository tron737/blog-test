<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/public',
    ])
    ->exclude([
        'storage',
        'vendor',
    ]);

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PHP81Migration' => true,

        'array_syntax' => [
            'syntax' => 'short',
        ],

        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],

        'no_unused_imports' => true,

        'single_quote' => true,

        'trailing_comma_in_multiline' => [
            'elements' => [
                'arrays',
                'arguments',
                'parameters',
                'match',
            ],
        ],

        'declare_strict_types' => true,
    ])
    ->setFinder($finder);