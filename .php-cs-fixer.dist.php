<?php
/*
 * Copyright (C) 2024 GT+ Logistics.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301
 * USA
 */

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('src/Bridge/Laravel/config')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP83Migration' => true,
        '@PHP80Migration:risky' => true,
        'array_indentation' => true,
        'concat_space' => ['spacing' => 'one'],
        'heredoc_indentation' => ['indentation' => 'same_as_start'],
        'native_constant_invocation' => false,
        'native_function_invocation' => false,
        'no_alternative_syntax' => ['fix_non_monolithic_code' => false],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'phpdoc_align' => ['align' => 'left'],
        'trailing_comma_in_multiline' => ['elements' => ['arguments', 'arrays', 'match', 'parameters']],
        'use_arrow_functions' => false,
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false, 'always_move_variable' => false],
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
