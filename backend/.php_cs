<?php

/**
 * @noinspection PhpUndefinedNamespaceInspection
 * @noinspection PhpUndefinedClassInspection
 */

$finder = PhpCsFixer\Finder::create()
    ->exclude('var')
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRules([
        '@PSR12' => true,
        '@PHP70Migration' => true,
        '@PHP71Migration' => true,
        '@PHP73Migration' => true,
        '@PHP74Migration' => true,
        '@PHP80Migration' => true,

        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'phpdoc_align' => ['align' => 'left'],
        'self_static_accessor' => true,

        // risky
        '@PHP70Migration:risky' => true,
        '@PHP71Migration:risky' => true,
        '@PHP74Migration:risky' => true,
        '@PHP80Migration:risky' => true,
        '@PhpCsFixer:risky' => true,
        'final_class' => true,
        'php_unit_test_annotation' => ['style' => 'annotation']
    ])
    ->setRiskyAllowed(false);
