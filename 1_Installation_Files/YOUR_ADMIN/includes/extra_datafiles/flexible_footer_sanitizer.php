<?php

/**
 * @package Multi Language Flexible Footer
 * @copyright Copyright 2008-2017 Zen4All
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
if (class_exists('AdminRequestSanitizer')) {
  $sanitizer = AdminRequestSanitizer::getInstance();
  $group = array(
    'col_header' => array('sanitizerType' => 'PRODUCT_DESC_REGEX',
      'method' => 'both',
      'pages' => array('flexible_footer_menu'),
      'params' => array()),
    'page_title' => array('sanitizerType' => 'PRODUCT_DESC_REGEX',
      'method' => 'both',
      'pages' => array('flexible_footer_menu'),
      'params' => array()),
    'col_html_text' => array('sanitizerType' => 'PRODUCT_DESC_REGEX',
      'method' => 'both',
      'pages' => array('flexible_footer_menu'),
      'params' => array()),
    'page_url' => array('sanitizerType' => 'PRODUCT_URL_REGEX',
      'method' => 'both',
      'pages' => array('flexible_footer_menu'),
      'params' => array()),
  );
  $sanitizer->addComplexSanitization($group);
}
