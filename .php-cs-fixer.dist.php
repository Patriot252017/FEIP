<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_extra_blank_lines' => true,
        'line_ending' => true,
        'single_quote' => true,
        'declare_strict_types' => true,
        'fully_qualified_strict_types' => true,
        'phpdoc_to_comment' => ['ignored_tags' => ['psalm']],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude(['vendor', 'var', 'bin'])
    );