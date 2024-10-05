<?php

/**
 *
 * Flexible Footer Menu Multilingual
 *
 * @package templateSystem
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 *
 * @added for version 1.0 by ZCAdditions.com (rbarbour) 4-17-2013 $
 *
 * */
include(DIR_WS_MODULES . zen_get_module_directory('flexible_footer_menu.php'));

if (sizeof($var_linksList) >= 1) {
  $col_output = '';
  $cols = 0;
  for ($list_col = 1; $list_col <= 9; $list_col++) {
    $col = '';
    $col_links = 0;

    $col .= '<div class="section group">';

    if (in_array($list_col, explode(",", ZCA_SHOW_9_COLS))) {
      $col .= '<div class="col span_9_of_9">';
    }
    if (in_array($list_col, explode(",", ZCA_SHOW_8_COLS))) {
      $col .= '<div class="col span_8_of_9">';
    }
    if (in_array($list_col, explode(",", ZCA_SHOW_7_COLS))) {
      $col .= '<div class="col span_7_of_9">';
    }
    if (in_array($list_col, explode(",", ZCA_SHOW_6_COLS))) {
      $col .= '<div class="col span_6_of_9">';
    }
    if (in_array($list_col, explode(",", ZCA_SHOW_5_COLS))) {
      $col .= '<div class="col span_5_of_9">';
    }
    if (in_array($list_col, explode(",", ZCA_SHOW_4_COLS))) {
      $col .= '<div class="col span_4_of_9">';
    }
    if (in_array($list_col, explode(",", ZCA_SHOW_3_COLS))) {
      $col .= '<div class="col span_3_of_9">';
    }
    if (in_array($list_col, explode(",", ZCA_SHOW_2_COLS))) {
      $col .= '<div class="col span_2_of_9">';
    }
    if (in_array($list_col, explode(",", ZCA_SHOW_1_COLS))) {
      $col .= '<div class="col span_1_of_9">';
    }

    $col .= '<ul id="col_' . $list_col . '">';

    for ($i = 1, $n = sizeof($var_linksList); $i <= $n; $i++) {
      if (substr($var_linksList[$i]['sort'], 0, 1) == $list_col) {

        $pages_id = $var_linksList[$i]['id'];

        if ($var_linksList[$i]['link'] && $var_linksList[$i]['header']) {
          $col .= '<li><h4>' . '<a href="' . $var_linksList[$i]['link'] . '"' . active_footer_page_class($var_linksList[$i]['id'], $var_linksList[$i]['link']) . '>' . $var_linksList[$i]['header'] . '</a></h4>';
        } else if ($var_linksList[$i]['header']) {
          $col .= '<li><h4>' . $var_linksList[$i]['header'] . '</h4>';
        } else {
//show nothing 
        }

        if ($var_linksList[$i]['title']) {
          $col .= '<li><a href="' . $var_linksList[$i]['link'] . '"' . active_footer_page_class($var_linksList[$i]['id'], $var_linksList[$i]['link']) . '>' . $var_linksList[$i]['title'] . '</a>';
        } else {
//show nothing 
        }

        if ($var_linksList[$i]['link'] && $var_linksList[$i]['image']) {
          $col .= '<li><span class="flexFooterColImage">' . '<a href="' . $var_linksList[$i]['link'] . '">' . zen_image(DIR_WS_IMAGES . $var_linksList[$i]['image']) . '</a></span>';
        } else if ($var_linksList[$i]['image']) {
          $col .= '<li><span class="flexFooterColImage">' . zen_image(DIR_WS_IMAGES . $var_linksList[$i]['image'], '') . '</span>';
        } else {
//show nothing 
        }




        if ($var_linksList[$i]['text']) {
          $col .= '<br /><span class="flexFooterColText">' . $var_linksList[$i]['text'] . '</span>';
        } else {
//show nothing 
        }

        $col .= '</li>' . "\n";
        $col_links++;
      }
    }
    $col .= '</ul>';
    $col .= '</div>';

    if ($col_links >= 1) {
      $col_output .= $col;
      $cols ++;
    }
  } // end FOR loop

  echo $col_output . '<br class="clearBoth" />';
} // end if