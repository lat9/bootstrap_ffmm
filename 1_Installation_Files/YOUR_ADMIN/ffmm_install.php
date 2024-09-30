<?php
/**
 *
 * ZCA Flexible Footer Menu Multilingual Installation/Maintenance
 *
 * @package admin
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 *
 * @added for version 1.0 by ZCAdditions.com (rbarbour) 4-17-2013 $
 *
 */
require('includes/application_top.php');

$languages = zen_get_languages();

$old_menu_title = 'Configure ZCA Flexible Footer Menu';
$menu_title = 'ZCA Flexible Footer Menu Multilingual Configuration';
$menu_text = 'Configure ZCA Flexible Footer Menu Multilingual';

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action == 'new_install') {

  // find if Flexible Footer Menu Group Exists
  $sql_config = "SELECT *
                 FROM " . TABLE_CONFIGURATION_GROUP . "
                 WHERE configuration_group_title = '" . $old_menu_title . "'
                 OR configuration_group_title = '" . $menu_title . "'";

  $original_config = $db->Execute($sql_config);

  // find if Flexible Footer Menu Table Exists without throwing error
  $original_table = $db->Execute("SHOW TABLES LIKE '" . DB_PREFIX . 'flexible_footer_menu' . "'");

  // find if Flexible Footer Menu CONTENT Table Exists without throwing error
  $content_table = $db->Execute("SHOW TABLES LIKE '" . DB_PREFIX . 'flexible_footer_menu_content' . "'");

  if ($original_table->RecordCount() > 0) {
    $messageStack->add('Flexible Footer Menu Table already exists, either <b>Repair</b> or <b>Uninstall</b>.', 'error');
  }

  if ($original_table->RecordCount() == 0) {

    $db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_FLEXIBLE_FOOTER_MENU . " (
    page_id int(11) NOT NULL AUTO_INCREMENT,
    language_id int(11) NOT NULL DEFAULT '1',
    page_title varchar(64) NOT NULL DEFAULT '',
    page_url varchar(255) DEFAULT NULL,
    col_header varchar(64) DEFAULT NULL,
    col_image varchar(254) NOT NULL DEFAULT '',
    col_html_text text,
    status int(1) NOT NULL DEFAULT '0',
    col_sort_order int(11) NOT NULL DEFAULT '0',
    col_id int(11) NOT NULL DEFAULT '0',
    date_added datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    last_update datetime DEFAULT NULL,
    PRIMARY KEY (page_id)
    );");

    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, '', 'index.php?main_page=', '', 'footer_images/Home.png', '', 1, 11, 1, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, '', '', 'Quick Links', '', '', 1, 1, 1, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'Specials', 'index.php?main_page=specials', '', '', 'Don''t miss out on Deal of the Day!', 1, 13, 1, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'New Products', 'index.php?main_page=products_new', '', '', '', 1, 14, 1, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'All Products', 'index.php?main_page=products_all', '', '', '', 1, 15, 1, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, '', '', '', 'footer_images/information.jpg', '', 1, 2, 2, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'About Us', 'index.php?main_page=about_us', '', '', '', 1, 21, 2, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'Site Map', 'index.php?main_page=site_map', '', '', '', 1, 22, 2, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'Gift Certificate FAQ', 'index.php?main_page=gv_faq', '', '', '', 1, 23, 2, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'Discount Coupons', 'index.php?main_page=discount_coupon', '', '', 'Get <font color=\"red\">5% off</font><br>\r\nyour <u>first purchase</u><br>\r\nat my <i>demo site</i>!', 1, 24, 2, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'Newsletter Unsubscribe', 'index.php?main_page=unsubscribe', '', '', '', 1, 25, 2, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, '', 'index.php?main_page=contact_us', 'Customer Service', '', '', 1, 3, 3, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'Contact Us', 'index.php?main_page=contact_us', '', '', '', 1, 32, 3, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'Shipping & Returns', 'index.php?main_page=shippinginfo', '', '', '', 1, 31, 3, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'Privacy Notice', 'index.php?main_page=privacy', '', '', '', 1, 33, 3, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'Conditions of Use', 'index.php?main_page=conditions', '', '', '', 1, 34, 3, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, '', '', 'Account Links', '', '', 1, 35, 3, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'My Account', 'index.php?main_page=account', '', '', '', 1, 36, 3, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, 'Track your Order', 'index.php?main_page=order_status', '', '', '', 1, 37, 3, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, '', '', 'Share & Connect', '', '', 1, 4, 4, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, '', 'http://www.twitter.com', '', 'footer_images/twitter.png', '', 1, 41, 4, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, '', 'http://www.instagram.com', '', 'footer_images/instagram.png', '', 1, 42, 4, NOW(), NOW())");
    $db->Execute("INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU . " (language_id, page_title, page_url, col_header, col_image, col_html_text, status, col_sort_order, col_id, date_added, last_update)
                  VALUES (1, '', 'http://www.facebook.com', '', 'footer_images/facebook.png', '', 1, 43, 4, NOW(), NOW())");

    // find next sort order in admin_pages table
    $sql = "SELECT (MAX(sort_order)+2) AS sort
            FROM " . TABLE_ADMIN_PAGES;
    $result = $db->Execute($sql);
    $admin_page_sort = $result->fields['sort'];

    // now register the admin pages
    // Admin Menu for Flexible Footer Menu Configuration Menu
    zen_deregister_admin_pages('flexibleFooterMenu');
    zen_register_admin_page('flexibleFooterMenu', 'BOX_TOOLS_FLEXIBLE_FOOTER_MENU', 'FILENAME_FLEXIBLE_FOOTER_MENU', '', 'tools', 'Y', $admin_page_sort);

    $messageStack->add('Flexible Footer Menu Table Installed Successfully!', 'success');
  }

  if ($content_table->RecordCount() > 0) {
    $messageStack->add('Flexible Footer Menu Table already exists, either <b>Repair</b> or <b>Uninstall</b>.', 'error');
  }

  if ($content_table->RecordCount() == 0) {
    $db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT . " (
  pc_id int(11) NOT NULL auto_increment,
  page_id int(11) NOT NULL default '0',
  language_id int(11) NOT NULL default '1',
  page_title varchar(64) NOT NULL default '',
  col_header varchar(64) NOT NULL default '',
  col_html_text text,
  PRIMARY KEY  (pc_id),
  KEY idx_flexible_footer_content (page_id,language_id)
);");

    $pages = $db->Execute("SELECT page_id, page_title, col_header, col_html_text, language_id
                           FROM " . TABLE_FLEXIBLE_FOOTER_MENU);

    foreach ($pages as $page) {
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        if ($sql_data_array) {
          unset($sql_data_array);
        }
        if ($sql_update_array) {
          unset($sql_update_array);
        }
        if ($col_header) {
          unset($col_header);
        }
        if ($page_title) {
          unset($page_title);
        }
        if ($col_html_text) {
          unset($col_html_text);
        }
        if ($check_query) {
          unset($check_query);
        }

        $sql = "SELECT *
                FROM " . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT . "
                WHERE page_id = " . (int)$page['page_id'] . "
                AND language_id = " . (int)$languages[$i]['id'];
        $check_query = $db->Execute($sql);

        if ($check_query->RecordCount() == 0) {
          if (zen_not_null($page['col_header'])) {
            $col_header = $page['col_header'] . ($languages[$i]['id'] == $page['language_id'] ? '' : ' (translate me)');
          } else {
            $col_header = NULL;
          }

          if (zen_not_null($page['page_title'])) {
            $page_title = $page['page_title'] . ($languages[$i]['id'] == $page['language_id'] ? '' : ' (translate me)');
          } else {
            $page_title = NULL;
          }

          if (zen_not_null($page['col_html_text'])) {
            $col_html_text = $page['col_html_text'] . ($languages[$i]['id'] == $page['language_id'] ? '' : ' (translate me)');
          } else {
            $col_html_text = NULL;
          }

          $sql_data_array = array(
            'page_title' => $page_title,
            'col_header' => $col_header,
            'col_html_text' => $col_html_text,
            'language_id' => (int)$languages[$i]['id'],
            'page_id' => (int)$page['page_id']);

          zen_db_perform(TABLE_FLEXIBLE_FOOTER_MENU_CONTENT, $sql_data_array);
        } // end  if ($count_query->RecordCount() == 0)
      } // end for ($i=0, $n = sizeof($languages); $i<$n; $i++)
    } // end while (!$pages->EOF)

    $update_query = $db->Execute("SELECT *
                                  FROM " . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT . "
                                  WHERE page_title LIKE '%translate me%'
                                  OR col_header LIKE '%translate me%'
                                  OR col_html_text LIKE '%translate me%'");

    $records = $update_query->RecordCount();

    $messageStack->add('Flexible Footer Menu Content Table Installed Successfully!', 'success');
  }

  if ($original_config->RecordCount() > 0) {
    $messageStack->add('Flexible Footer Menu Configuration Group already exists, either <b>Upgrade</b> or <b>Uninstall</b> and then click the <b>New Installation</b> button.', 'error');
  } else {

    // Find max sort order in the Configuration Group Table
    $sql = "SELECT (MAX(sort_order)+2) AS sort
            FROM " . TABLE_CONFIGURATION_GROUP;

    $result = $db->Execute($sql);
    $sort = $result->fields['sort'];

    // ADD VALUES TO Flexible Footer Menu Multilingual Configuration Group
    $sql = "INSERT INTO " . TABLE_CONFIGURATION_GROUP . " (configuration_group_title, configuration_group_description, sort_order, visible)
            VALUES ('" . $menu_title . "', '" . $menu_text . "', " . $sort . ", '1')";

    $db->Execute($sql);

    // Find Configuration Group ID for Flexible Footer Menu Multilingual
    $sql = "SELECT configuration_group_id
            FROM " . TABLE_CONFIGURATION_GROUP . "
            WHERE configuration_group_title = '" . $menu_title . "'
            LIMIT 1";

    $result = $db->Execute($sql);
    $menu_configuration_id = $result->fields['configuration_group_id'];

    // ADD VALUES TO Flexible Footer Menu
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
                  VALUES (NULL, 'Assign 1 Column to a Row', 'ZCA_SHOW_1_COLS', '5', 'List the column # seperated by commas', '" . $menu_configuration_id . "', 1, NOW(), NULL, NULL)");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
                  VALUES (NULL, 'Assign 2 Column to a Row', 'ZCA_SHOW_2_COLS', '', 'List the column # seperated by commas in groups of 2 (1,2 or 6,7 or 5,6)', '" . $menu_configuration_id . "', 2, NOW(), NULL, NULL)");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
                  VALUES (NULL, 'Assign 3 Column to a Row', 'ZCA_SHOW_3_COLS', '6,7,8', 'List the column # seperated by commas in groups of 3 (1,2,3 or 6,7,8)', '" . $menu_configuration_id . "', 3, NOW(), NULL, NULL)");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
                  VALUES (NULL, 'Assign 4 Column to a Row', 'ZCA_SHOW_4_COLS', '1,2,3,4', 'List the column # seperated by commas in groups of 4 (1,2,3,4 or 6,7,8,9)', '" . $menu_configuration_id . "', 4, NOW(), NULL, NULL)");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
                  VALUES (NULL, 'Assign 5 Column to a Row', 'ZCA_SHOW_5_COLS', '', 'List the column # seperated by commas in groups of 5 (1,2,3,4,5)', '" . $menu_configuration_id . "', 5, NOW(), NULL, NULL)");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
                  VALUES (NULL, 'Assign 6 Column to a Row', 'ZCA_SHOW_6_COLS', '', 'List the column # seperated by commas in groups of 6 (1,2,3,4,5,6)', '" . $menu_configuration_id . "', 6, NOW(), NULL, NULL)");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
                  VALUES (NULL, 'Assign 7 Column to a Row', 'ZCA_SHOW_7_COLS', '', 'List the column # seperated by commas in groups of 7 (1,2,3,4,5,6,7)', '" . $menu_configuration_id . "', 7, NOW(), NULL, NULL)");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
                  VALUES (NULL, 'Assign 8 Column to a Row', 'ZCA_SHOW_8_COLS', '', 'List the column # seperated by commas in groups of 8 (1,2,3,4,5,6,7,8)', '" . $menu_configuration_id . "', 8, NOW(), NULL, NULL)");
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
                  VALUES (NULL, 'Assign 9 Column to a Row', 'ZCA_SHOW_9_COLS', '', 'List the column # seperated by commas in groups of 9 (1,2,3,4,5,6,7,8,9)', '" . $menu_configuration_id . "', 9, NOW(), NULL, NULL)");

    // find next sort order in admin_pages table
    $sql = "SELECT (MAX(sort_order)+2) AS sort
            FROM " . TABLE_ADMIN_PAGES;
    $result = $db->Execute($sql);
    $admin_page_sort = $result->fields['sort'];

    // now register the admin pages
    // Admin Menu for Flexible Footer Menu Configuration Menu
    zen_deregister_admin_pages('flexibleFooterMenuConfig');
    zen_register_admin_page('flexibleFooterMenuConfig', 'BOX_CONFIG_FLEXIBLE_FOOTER_MENU', 'FILENAME_CONFIGURATION', 'gID=' . $menu_configuration_id, 'configuration', 'Y', $admin_page_sort);

    $messageStack->add('Flexible Footer Menu Configuration Group Installed Successfully!.', 'success');
  }
}

if ($action == 'repair') {

  // find if Flexible Footer Menu Group Exists
  $sql_config = "SELECT *
                 FROM " . TABLE_CONFIGURATION_GROUP . "
                 WHERE configuration_group_title = '" . $old_menu_title . "'
                 OR configuration_group_title = '" . $menu_title . "'";

  $original_config = $db->Execute($sql_config);

  // find if Flexible Footer Menu Table Exists without throwing error
  $original_table = $db->Execute("SHOW TABLES LIKE '" . TABLE_FLEXIBLE_FOOTER_MENU . "'");

  // find if Flexible Footer Menu CONTENT Table Exists without throwing error
  $content_table = $db->Execute("SHOW TABLES LIKE '" . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT . "'");

  if ($original_config->RecordCount() > 0 && $original_table->RecordCount() > 0 && $content_table->RecordCount() > 0) {

    $pages = $db->Execute("SELECT page_id, page_title, col_header, col_html_text, language_id
	                       FROM " . TABLE_FLEXIBLE_FOOTER_MENU);

    foreach ($pages as $page) {

      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        if ($sql_data_array) {
          unset($sql_data_array);
        }
        if ($sql_update_array) {
          unset($sql_update_array);
        }
        if ($col_header) {
          unset($col_header);
        }
        if ($page_title) {
          unset($page_title);
        }
        if ($col_html_text) {
          unset($col_html_text);
        }
        if ($check_query) {
          unset($check_query);
        }

        $sql = "SELECT *
                FROM " . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT . "
                WHERE page_id = " . (int)$page['page_id'] . "
                AND language_id = " . (int)$languages[$i]['id'];

        $check_query = $db->Execute($sql);

        if ($check_query->RecordCount() == 0) {

          if (zen_not_null($page['col_header'])) {
            $col_header = $page['col_header'] . ($languages[$i]['id'] == $page['language_id'] ? '' : ' (translate me)');
          } else {
            $col_header = NULL;
          }

          if (zen_not_null($page['page_title'])) {
            $page_title = $page['page_title'] . ($languages[$i]['id'] == $page['language_id'] ? '' : ' (translate me)');
          } else {
            $page_title = NULL;
          }

          if (zen_not_null($page['col_html_text'])) {
            $col_html_text = $page['col_html_text'] . ($languages[$i]['id'] == $page['language_id'] ? '' : ' (translate me)');
          } else {
            $col_html_text = NULL;
          }

          $sql_data_array = array(
            'page_title' => $page_title,
            'col_header' => $col_header,
            'col_html_text' => $col_html_text,
            'language_id' => (int)$languages[$i]['id'],
            'page_id' => (int)$page['page_id']);

          zen_db_perform(TABLE_FLEXIBLE_FOOTER_MENU_CONTENT, $sql_data_array);
        } // end  if ($count_query->RecordCount() == 0)
      } // end for ($i=0, $n = sizeof($languages); $i<$n; $i++)
    } // end while (!$pages->EOF)

    $update_query = $db->Execute("SELECT *
                                  FROM " . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT . "
                                  WHERE page_title LIKE '%translate me%'
                                  OR col_header LIKE '%translate me%'
                                  OR col_html_text LIKE '%translate me%'");

    $records = $update_query->RecordCount();
  } else {

    $messageStack->add('Neither Flexible Footer Menu or Flexible Footer Menu Multilingual exist so they can\'t be repaired.', 'error');
  }
} // end if ($action == 'repair')

if ($action == 'uninstall') {

  $db->Execute("DELETE FROM configuration_group WHERE configuration_group_description = 'Configure ZCA Flexible Footer Menu'");
  $db->Execute("DELETE FROM configuration_group WHERE configuration_group_description = 'Configure ZCA Flexible Footer Menu Multilingual'");
  $db->Execute("DELETE FROM configuration WHERE configuration_key = 'ZCA_SHOW_1_COLS'");
  $db->Execute("DELETE FROM configuration WHERE configuration_key = 'ZCA_SHOW_2_COLS'");
  $db->Execute("DELETE FROM configuration WHERE configuration_key = 'ZCA_SHOW_3_COLS'");
  $db->Execute("DELETE FROM configuration WHERE configuration_key = 'ZCA_SHOW_4_COLS'");
  $db->Execute("DELETE FROM configuration WHERE configuration_key = 'ZCA_SHOW_5_COLS'");
  $db->Execute("DELETE FROM configuration WHERE configuration_key = 'ZCA_SHOW_6_COLS'");
  $db->Execute("DELETE FROM configuration WHERE configuration_key = 'ZCA_SHOW_7_COLS'");
  $db->Execute("DELETE FROM configuration WHERE configuration_key = 'ZCA_SHOW_8_COLS'");
  $db->Execute("DELETE FROM configuration WHERE configuration_key = 'ZCA_SHOW_9_COLS'");

  $db->Execute("DROP TABLE IF EXISTS " . TABLE_FLEXIBLE_FOOTER_MENU);
  $db->Execute("DROP TABLE IF EXISTS " . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT);

  $db->Execute("DELETE FROM admin_pages WHERE page_key = 'flexibleFooterMenu'");
  $db->Execute("DELETE FROM admin_pages WHERE page_key = 'flexibleFooterMenuConfig'");
} // end if ($action == 'uninstall')
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" href="includes/stylesheet.css">
    <link rel="stylesheet" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <script src="includes/menu.js"></script>
    <script src="includes/general.js"></script>
    <script>
      function init() {
          cssjsmenu('navbar');
          if (document.getElementById)
          {
              var kill = document.getElementById('hoverJS');
              kill.disabled = true;
          }
      }
    </script>

    <style>
      td.mainffmm {
          color:#444;
          border-radius:10px;
          font-family:Tahoma,Geneva,Arial,sans-serif;font-size:12px;
          padding:10px 10px 20px 10px;
          margin:10px;
          background:#e3f7fc;
          border:1px solid #FCE883;
      }

      td.mainffmm.caution {
          background:#FFFF99;
          border:1px solid #FDDB6D;
      }

      span.alert,td.messageStackError {
          border-radius:10px;
          font-family:Tahoma,Geneva,Arial,sans-serif;font-size:12px;
          padding:10px 20px 10px 20px;
          display:block;
          background:#ffecec ;
          border:1px solid #f5aca6;
      }

      td.messageStackError { border-radius:0px;border:0px solid #f5aca6;}

      td.messageStackSuccess {
          font-family:Tahoma,Geneva,Arial,sans-serif;font-size:12px;
          padding:10px 20px 10px 20px;
          display:block;
      }


      td.pageHeading {
          color:#2ba6c6; }

      td.mainffmm>div>a {
          border-radius:10px;
          font-family:Tahoma,Geneva,Arial,sans-serif;font-size:12px;
          padding:8px;
          background: #e6e6e6;
          background-image: -ms-linear-gradient(right, #F5F5F5 0%, #FFFFFF 100%);/* IE10 Consumer Preview */
          background-image: -moz-linear-gradient(right, #F5F5F5 0%, #FFFFFF 100%);/* Mozilla Firefox */
          background-image: -o-linear-gradient(right, #F5F5F5 0%, #FFFFFF 100%);/* Opera */
          background-image: -webkit-gradient(linear, right top, left top, color-stop(0, #F5F5F5), color-stop(1, #FFFFFF));/* Webkit (Safari/Chrome 10) */
          background-image: -webkit-linear-gradient(right, #F5F5F5 0%, #FFFFFF 100%);/* Webkit (Chrome 11+) */
          background-image: linear-gradient(to left, #F5F5F5 0%, #FFFFFF 100%);/* W3C Markup, IE10 Release Preview */
      }
    </style>

  </head>
  <body onLoad="init()">
      <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <table>
      <!-- body_text //-->
      <tr>
        <td class="pageHeading">Flexible Footer Menu Multilingual - Installation/Maintenance
        </td>
      </tr>
      <?php if ($action == 'repair') { ?>
        <tr>
          <td class="mainffmm caution"><strong>Flexible Footer Menu Multilingual content table repaired.</strong><br>
              <?php
              if ($records > 0) {
                echo $records . ' pages now need translations to be fixed from Admin > Tools > Flexible Footer Menu Multilingual.<br>Those that need to be fixed have "(translate me)" at the end of the page title and/or col header or the html text.<br>(Please check the Flexible Footer Menu Multilingual page with each of your languages selected or you may miss some entries that need to be fixed).<br><br><br><br>';
              }
              ?>
          </td>
        </tr>
      <?php } ?>

      <?php if ($action == 'uninstall') { ?>
        <tr>
          <td class="mainffmm caution"><strong>Flexible Footer Menu Multilingual has been uninstalled.</strong> Flexible Footer Menu Multilingual, all Admin pages, configurations and tables have been removed.<br><br>
            <span class="alert">Unless you intend to re-install Flexible Footer Menu Multilingual, now you MUST <b>Restore</b> your templates original /includes/templates/YOUR_TEMPLATE_NAME/common/tpl_footer.php file on your server with the original file.</span>
          </td>
        </tr>
      <?php } ?>

      <?php if ($action != 'new_install') { ?>
        <tr>
          <td class="mainffmm"><strong>Install Flexible Footer Menu Multilingual v1.0</strong> This link completely installs Flexible Footer Menu Multilingual v1.0.<br><br>
            <span class="alert">Caution: This action should only be used for NEW INSTALLATIONS. If you have a version of Flexible Footer Menu already installed, use the repair/upgrade link to upgrade to Flexible Footer Menu Multilingual v1.0.</span>
            <div align="center">
              <a href="ffmm_install.php?action=new_install" title="new_install">New Installation</a>
            </div>
          </td>
        </tr>
      <?php } ?>

      <?php if ($action != 'repair') { ?>
        <tr>
          <td class="mainffmm"><strong>Repair/Upgrade Flexible Footer Menu</strong> This link will upgrade Flexible Footer Menu to Flexible Footer Menu Multilingual v1.0.<br><br> If you are experiencing problems with Flexible Footer Menu Multilingual links not displaying in the store in some languages, try using this link. It is likely that there are some blank entries for column headers, page titles and/or html content for the language(s) that the links are not appearing in. The Repair/Upgrade link will fix this, although you will have to translate the content of those 'missing' links again.<br><br>
            <span class="alert">Caution: If you have a version of Flexible Footer Menu already installed, use the Repair/Upgrade link to install Flexible Footer Menu Multilingual v1.0.</span><br><br>
            <div align="center">
              <a href="ffmm_install.php?action=repair" title="upgrade">Repair/Upgrade Flexible Footer Menu Multilingual v1.0</a>
            </div>
          </td>
        </tr>
      <?php } ?>

      <?php if ($action != 'uninstall') { ?>
        <tr>
          <td class="mainffmm"><strong>Uninstall Flexible Footer Menu Multilingual</strong> This link will completely remove Admin pages, configurations and tables.<br><br>
            <span class="alert">Caution: After you uninstall, neither versions of Flexible Footer Menu WILL NOT FUNCTION, you will need to <b>Restore</b> your templates original /includes/templates/YOUR_TEMPLATE_NAME/common/tpl_footer.php file</span>
            <div align="center">
              <a href="ffmm_install.php?action=uninstall" title="Uninstall">Uninstall Flexible Footer Menu Multilingual</a>
            </div>
          </td>
        </tr>
        <?php
      }// if (!$action == 'uninstall')
      ?>
      <!-- body_text_eof //-->
    </table>
    <!-- body_eof //-->

    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
    <br>
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>