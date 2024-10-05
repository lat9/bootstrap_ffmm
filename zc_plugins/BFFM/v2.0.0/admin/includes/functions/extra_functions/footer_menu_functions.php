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

function zen_set_flexible_footer_menu_status($page_id, $status)
{
  global $db;
  if ($status == '1') {
    return $db->Execute("UPDATE " . TABLE_FLEXIBLE_FOOTER_MENU . " SET status = 1 WHERE page_id = " . (int)$page_id);
  } elseif ($status == '0') {
    return $db->Execute("UPDATE " . TABLE_FLEXIBLE_FOOTER_MENU . " SET status = 0 WHERE page_id = " . (int)$page_id);
  } else {
    return -1;
  }
}
