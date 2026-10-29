<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@Symfony' => true,
        'blank_line_after_opening_tag' => false,
        'new_with_parentheses' => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'increment_style' => ['style' => 'post'],
        'not_operator_with_successor_space' => true,
        'phpdoc_align' => false,
        'phpdoc_order' => false,
        'phpdoc_separation' => false,
        'phpdoc_types' => false,
        'trailing_comma_in_multiline' => false,
        'no_unused_imports' => true,
    ])
    ->setFinder(
        (new Finder())
            ->in([__DIR__.'/src', __DIR__.'/tests', __DIR__.'/migrations'])
            ->exclude('tmp')
            ->append([__DIR__.'/extend.php', __FILE__])
    );
