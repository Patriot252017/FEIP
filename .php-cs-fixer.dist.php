<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude(['var', 'vendor', 'tests/_output'])
    ->notPath('config/preload.php')
    ->notPath('public/index.php')
    ->name('*.php');

return (new Config())
    ->setRules([
        '@PSR12' => true,
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_extra_blank_lines' => true,
        'line_ending' => true,
        'single_quote' => true,
        'phpdoc_to_comment' => false,
        'declare_strict_types' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
    ])
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setRiskyAllowed(true);