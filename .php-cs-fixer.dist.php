<?php

$rules = [
    '@PSR12' => true,
    '@PhpCsFixer' => true,
    'declare_strict_types' => true,
    'array_syntax' => ['syntax' => 'short'],
    'no_unused_imports' => true,
    'ordered_imports' => ['sort_algorithm' => 'alpha'],
    'php_unit_method_casing' => ['case' => 'camel_case'],
    'single_quote' => true,
    'binary_operator_spaces' => ['default' => 'single_space'],
    'global_namespace_import' => ['import_functions' => true, 'import_classes' => true, 'import_constants' => true],
    'types_spaces' => ['space' => 'single'],
];

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([__DIR__ . '/src', __DIR__ . '/tests'])
            ->name('*.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
    )
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache');
