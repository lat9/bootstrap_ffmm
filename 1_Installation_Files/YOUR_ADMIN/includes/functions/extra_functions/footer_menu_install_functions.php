<?php

/**
 *
 * Flexible Footer Menu Multilingual
 *
 * @package admin
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 *
 * @added for version 1.0 by ZCAdditions.com (rbarbour) 4-17-2013 $
 *
 * */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (function_exists('zen_register_admin_page')) {
  if (!zen_page_key_exists('ffmm_install')) {
    zen_register_admin_page('ffmm_install', 'BOX_TOOLS_FLEXIBLE_FOOTER_MENU_INSTALL', 'FILENAME_FLEXIBLE_FOOTER_MENU_INSTALL', '', 'tools', 'Y', 70);
  }
}