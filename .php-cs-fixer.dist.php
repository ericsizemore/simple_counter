<?php

declare(strict_types=1);

$header = <<<'EOF'
    This file is part of Esi\SimpleCounter.

    (c) Eric Sizemore <https://github.com/ericsizemore>

    This source file is subject to the MIT license. For the full copyright and
    license information, please view the LICENSE file that was distributed with
    this source code.
    EOF;

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS'                 => true,
        '@PSR12'                  => true,
        '@PHP82Migration'         => true,
        'align_multiline_comment' => true,
        'array_syntax'            => [
            'syntax' => 'short',
        ],
        'binary_operator_spaces' => [
            'operators' => [
                '*=' => 'align_single_space_minimal',
                '+=' => 'align_single_space_minimal',
                '-=' => 'align_single_space_minimal',
                '/=' => 'align_single_space_minimal',
                '='  => 'align_single_space_minimal',
                '=>' => 'align_single_space_minimal',
            ],
        ],
        'blank_line_between_import_groups' => true,
        'cast_spaces'                      => true,
        'declare_equal_normalize'          => [
            'space' => 'none',
        ],
        'declare_parentheses'          => true,
        'declare_strict_types'         => true,
        'fully_qualified_strict_types' => true,
        'header_comment'               => [
            'comment_type' => 'PHPDoc',
            'header'       => $header,
            'separate'     => 'top',
        ],
        'heredoc_indentation' => true,
        'heredoc_to_nowdoc'   => true,
        //'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'native_function_invocation' => [
            'include' => [
                '@compiler_optimized',
            ],
            'scope'  => 'namespaced',
            'strict' => true,
        ],
        'native_constant_invocation' => [
            'fix_built_in' => false,
            'include'      => [
                'DIRECTORY_SEPARATOR',
                'PHP_INT_SIZE',
                'PHP_SAPI',
                'PHP_VERSION_ID',
            ],
            'scope'  => 'namespaced',
            'strict' => true,
        ],
        'no_empty_comment'     => true,
        'no_empty_phpdoc'      => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'square_brace_block',
                'switch',
                'throw',
                'use',
            ],
        ],
        'no_leading_import_slash'  => true,
        'no_unneeded_import_alias' => true,
        'no_unused_imports'        => true,
        'ordered_class_elements'   => [
            'order' => [
                'use_trait',
                'case',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_public_static',
                'property_protected',
                'property_protected_static',
                'property_private',
                'property_private_static',
                'construct',
                'destruct',
                'magic',
                'phpunit',
                'method_public',
                'method_public_static',
                'method_protected',
                'method_protected_static',
                'method_private',
                'method_private_static',
            ],
            'sort_algorithm' => 'alpha',
        ],
        'ordered_imports' => [
            'imports_order' => [
                'class',
                'function',
                'const',
            ],
        ],
        'ordered_interfaces' => [
            'direction' => 'ascend',
            'order'     => 'alpha',
        ],
        'ordered_traits'                                => true,
        'ordered_types'                                 => true,
        'phpdoc_align'                                  => true,
        'phpdoc_indent'                                 => true,
        'phpdoc_inline_tag_normalizer'                  => true,
        'phpdoc_no_access'                              => true,
        'phpdoc_no_alias_tag'                           => true,
        'phpdoc_no_empty_return'                        => true,
        'phpdoc_no_package'                             => true,
        'phpdoc_no_useless_inheritdoc'                  => true,
        'phpdoc_order'                                  => true,
        'phpdoc_param_order'                            => true,
        'phpdoc_return_self_reference'                  => true,
        'phpdoc_scalar'                                 => true,
        'phpdoc_separation'                             => true,
        'phpdoc_single_line_var_spacing'                => true,
        'phpdoc_summary'                                => true,
        'phpdoc_tag_casing'                             => true,
        'phpdoc_tag_type'                               => true,
        'phpdoc_to_comment'                             => false,
        'phpdoc_trim'                                   => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types_order'                            => true,
        'phpdoc_var_annotation_correct_order'           => true,
        'phpdoc_var_without_name'                       => true,
        'php_unit_data_provider_return_type'            => true,
        'php_unit_data_provider_static'                 => [
            'force' => true,
        ],
        'php_unit_expectation'    => true,
        'php_unit_internal_class' => [
            'types' => [
                'normal',
                'final',
            ],
        ],
        'php_unit_method_casing' => [
            'case' => 'camel_case',
        ],
        'php_unit_mock' => [
            'target' => 'newest',
        ],
        'php_unit_mock_short_will_return'      => true,
        'php_unit_set_up_tear_down_visibility' => true,
        'php_unit_size_class'                  => false,
        'php_unit_test_annotation'             => [
            'style' => 'prefix',
        ],
        'php_unit_test_case_static_method_calls' => [
            'call_type' => 'self',
        ],
        'single_import_per_statement'     => true,
        'single_quote'                    => true,
        'single_space_around_construct'   => true,
        'static_lambda'                   => true,
        'strict_param'                    => true,
        'types_spaces'                    => true,
        'use_arrow_functions'             => true,
        'whitespace_after_comma_in_array' => true,
    ])
    ->setLineEnding("\n")
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/scripts')
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
    )
;
$config->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());

return $config;
