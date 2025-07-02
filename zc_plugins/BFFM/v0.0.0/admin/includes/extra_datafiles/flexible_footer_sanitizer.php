<?php
/**
 * @package Multi Language Flexible Footer (for Bootstrap)
 *
 * Last updated: v2.0.0
 *
 * @copyright Copyright 2008-2017 Zen4All
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
$group = [
    'col_header' => [
        'sanitizerType' => 'PRODUCT_DESC_REGEX',
        'method' => 'both',
        'pages' => ['flexible_footer_menu2'],
        'params' => []
    ],
    'page_title' => [
        'sanitizerType' => 'PRODUCT_DESC_REGEX',
        'method' => 'both',
        'pages' => ['flexible_footer_menu2'],
        'params' => []
    ],
    'col_html_text' => [
        'sanitizerType' => 'PRODUCT_DESC_REGEX',
        'method' => 'both',
        'pages' => ['flexible_footer_menu2'],
        'params' => []
    ],
    'page_url' => [
        'sanitizerType' => 'PRODUCT_URL_REGEX',
        'method' => 'both',
        'pages' => ['flexible_footer_menu2'],
        'params' => []
    ],
];
$sanitizer = AdminRequestSanitizer::getInstance();
$sanitizer->addComplexSanitization($group);
