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
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'try', 'if'],
        ],
        'declare_strict_types' => true,
        'final_class' => true,
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'no_unused_imports' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
        'single_quote' => true,
        'no_extra_blank_lines' => true,
        'single_line_throw' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_summary' => true,
        'phpdoc_trim' => true,
        'concat_space' => ['spacing' => 'one'],
        'nullable_type_declaration_for_default_null_value' => true,
        'no_superfluous_phpdoc_tags' => false,
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one',
                'property' => 'one',
                'method' => 'one',
            ],
        ],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'no_alternative_syntax' => true,
        'use_arrow_functions' => true,
        'get_class_to_class_keyword' => true,
    ])
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setUnsupportedPhpVersionAllowed(true);