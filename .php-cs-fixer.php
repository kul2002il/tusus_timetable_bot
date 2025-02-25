<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['bootstrap', 'storage'])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12'                  => true,
        'align_multiline_comment' => [
            'comment_type' => 'all_multiline'
        ],
        'array_indentation' => true,
        'array_syntax'      => [
            'syntax' => 'short'
        ],
        'assign_null_coalescing_to_coalesce_equal' => true,
        'blank_line_before_statement'              => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try', 'foreach', 'if']
        ],
        'no_unused_imports'            => true,
        'no_extra_blank_lines'         => true,
        'single_space_after_construct' => [
            'constructs' => ['return']
        ],
        'binary_operator_spaces' => [
            'default'   => null,
            'operators' => [
                '=>' => 'align_single_space_minimal',
                '>'  => 'single_space',
                '<'  => 'single_space',
            ]
        ],
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
            ]
        ],
        'whitespace_after_comma_in_array'       => true,
        'cast_spaces'                           => true,
        'elseif'                                => true,
        'no_trailing_comma_in_singleline_array' => true,
        'method_argument_space'                 => true,
        'no_whitespace_before_comma_in_array'   => true,
    ])
    ->setFinder($finder)
;
