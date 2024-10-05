<?php
/**
 *
 * Flexible Footer Menu Multilingual
 *
 * @package admin
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @updated for Flexible Footer Menu v1.0 4/17/2013 ZCadditions.com (Raymond A. Barbour) $
 * @updated for Multilingual 2018-03-17 Zen4All (design75) zen4all.nl$
 */
require('includes/application_top.php');
$languages = zen_get_languages(); // modification for multi-language support
$action = (isset($_GET['action']) ? $_GET['action'] : '');
if (zen_not_null($action)) {
  switch ($action) {
    case 'setflag':
      if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
        zen_set_flexible_footer_menu_status(zen_db_prepare_input($_GET['footerID']), zen_db_prepare_input($_GET['flag']));
        $messageStack->add_session(SUCCESS_PAGE_STATUS_UPDATED, 'success');
      } else {
        $messageStack->add_session(ERROR_UNKNOWN_STATUS_FLAG, 'error');
      }
      zen_redirect(zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, 'page=' . (int)$_GET['page'] . '&footerID=' . (int)$_GET['footerID']));
      break;
    case 'insert':
    case 'update':
// start modification for multi-language support
      if (isset($_POST['page_id'])) {
        $page_id = zen_db_prepare_input($_POST['page_id']);
      }
      $page_url = zen_db_prepare_input($_POST['page_url']);
      $col_id = zen_db_prepare_input($_POST['col_id']);
      $col_sort_order = zen_db_prepare_input($_POST['col_sort_order']);
      $page_date = (empty($_POST['date_added']) ? zen_db_prepare_input('0001-01-01 00:00:00') : zen_db_prepare_input($_POST['date_added']));

      $page_error = false;
      if (empty($col_sort_order)) {
        $messageStack->add('Please add a Sort Order value', 'error');
        $page_error = true;
      }

      if ($page_error == false) {
        $language_id = (int)$_SESSION['languages_id'];
        $sql_data_array = array(
          'page_url' => $page_url,
          'col_id' => $col_id,
          'col_sort_order' => $col_sort_order,
        );

        if ($action == 'insert') {
          if (empty($_POST['date_added'])) {
            $page_date = 'now()';
          } else {
            $page_date = zen_date_raw($_POST['date_added']);
          }

          $insert_sql_data = array(
            'status' => '0',
            'date_added' => $page_date);
          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          zen_db_perform(TABLE_FLEXIBLE_FOOTER_MENU, $sql_data_array);
          $page_id = zen_db_insert_id();
          $page_title_array = zen_db_prepare_input($_POST['page_title']);
          $col_header_array = zen_db_prepare_input($_POST['col_header']);
          $col_html_text_array = zen_db_prepare_input($_POST['col_html_text']);
          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
            $sql_data_array = array(
              'page_title' => $page_title_array[$language_id],
              'col_header' => $col_header_array[$language_id],
              'col_html_text' => $col_html_text_array[$language_id],
              'language_id' => $language_id,
              'page_id' => $page_id);

            zen_db_perform(TABLE_FLEXIBLE_FOOTER_MENU_CONTENT, $sql_data_array);
          }
          $messageStack->add_session(SUCCESS_PAGE_INSERTED, 'success');
          zen_record_admin_activity('footer item with ID ' . (int)$page_id . ' added.', 'info');
        } elseif ($action == 'update') {
          $insert_sql_data = array('last_update' => 'now()');
          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          zen_db_perform(TABLE_FLEXIBLE_FOOTER_MENU, $sql_data_array, 'update', "page_id = " . (int)$page_id);
          $page_title_array = zen_db_prepare_input($_POST['page_title']);
          $col_header_array = zen_db_prepare_input($_POST['col_header']);
          $col_html_text_array = zen_db_prepare_input($_POST['col_html_text']);
          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
            $sql_data_array = array('page_title' => $page_title_array[$language_id],
              'col_header' => $col_header_array[$language_id],
              'col_html_text' => $col_html_text_array[$language_id]);

            zen_db_perform(TABLE_FLEXIBLE_FOOTER_MENU_CONTENT, $sql_data_array, 'update', "page_id = " . (int)$page_id . " and language_id = " . (int)$language_id);
          }
          $messageStack->add(SUCCESS_PAGE_UPDATED, 'success');
        }
// end modification for multi-language support
        if ($col_image = new upload('col_image')) {
          $col_image->set_destination(DIR_FS_CATALOG_IMAGES . 'footer_images/');
          if ($col_image->parse() && $col_image->save()) {
            $col_image_name = 'footer_images/' . $col_image->filename;
          }
          if ($col_image->filename != 'none' && $col_image->filename != '') {
            $db->Execute("UPDATE " . TABLE_FLEXIBLE_FOOTER_MENU . "
                          SET col_image = '" . $col_image_name . "'
                          WHERE page_id = " . (int)$page_id);
          } elseif ($col_image->filename == 'none' || $_POST['image_delete'] == 1) {
//      if ($col_image->filename == 'none') {
            $db->Execute("UPDATE " . TABLE_FLEXIBLE_FOOTER_MENU . "
                          SET col_image = ''
                          WHERE page_id = " . (int)$page_id);
          }
        }

        zen_redirect(zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'footerID=' . $page_id));
      } else {
        $action = 'new';
      }
      break;
    case 'delete_confirm':
      $page_id = $_POST['footerID'];
      $db->Execute("DELETE FROM " . TABLE_FLEXIBLE_FOOTER_MENU . "
                    WHERE page_id = " . (int)$page_id);
      $db->Execute("DELETE FROM " . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT . "
                    WHERE page_id = " . (int)$page_id);

      $messageStack->add(SUCCESS_PAGE_REMOVED, 'success');
      zen_redirect(zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, 'page=' . $_GET['page']));
      break;
  }
}
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
          if (document.getElementById) {
              var kill = document.getElementById('hoverJS');
              kill.disabled = true;
          }
      }
    </script>
    <?php
    if ($editor_handler != '') {
      include ($editor_handler);
    }
    ?>
  </head>
  <body onLoad="init()">
      <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <div class="container-fluid">
      <h1><?php echo HEADING_TITLE . ' ' . ($_GET['footerID'] != '' ? TEXT_INFO_PAGES_ID . $_GET['footerID'] : TEXT_INFO_PAGES_ID_SELECT); ?></h1>
      <div class="row">
        <!-- body_text //-->
        <?php
        if ($action != 'new') {
          ?>
          <div class="col-sm-offset-8 col-sm-4 text-right">
              <?php
// toggle switch for editor
              echo zen_draw_form('set_editor_form', FILENAME_FLEXIBLE_FOOTER_MENU, '', 'get', 'class="form-horizontal"');
              echo zen_draw_label(TEXT_EDITOR_INFO, 'reset_editor', 'class="col-sm-6 col-md-4 control-label"');
              echo '<div class="col-sm-6 col-md-4">';
              echo zen_draw_pull_down_menu('reset_editor', $editors_pulldown, $current_editor_key, 'onchange="this.form.submit();" class="form-control"');
              echo '</div>';
              echo zen_hide_session_id();
              echo zen_draw_hidden_field('action', 'set_editor');
              echo '</form>';
            }
            ?>
        </div>
      </div>
      <?php
      if ($action == 'new') {
        $form_action = 'insert';

        $parameters = array(
          'language_id' => '',
          'page_title' => '',
          'page_url' => '',
          'col_header' => '',
          'col_image' => '',
          'col_html_text' => '',
          'col_id' => '',
          'col_sort_order' => '',
          'date_added' => '',
          'status' => '0'
        );

        $footerInfo = new objectInfo($parameters);

        if (isset($_GET['footerID'])) {
          $form_action = 'update';

          $footerID = zen_db_prepare_input($_GET['footerID']);

// query modified for multilanguage support
          $page_query = "SELECT *
                         FROM " . TABLE_FLEXIBLE_FOOTER_MENU . "
                         WHERE page_id = " . (int)$_GET['footerID'];
          $page = $db->Execute($page_query);
          $footerInfo->updateObjectInfo($page->fields);
        } elseif (zen_not_null($_POST)) {
          $footerInfo->updateObjectInfo($_POST);
        }
        ?>
        <?php
        echo zen_draw_form('new_page', FILENAME_FLEXIBLE_FOOTER_MENU, (isset($_GET['page']) ? 'page=' . zen_db_prepare_input($_GET['page']) . '&' : '') . 'action=' . $form_action, 'post', 'enctype="multipart/form-data" class="form-horizontal"');
        if ($form_action == 'update') {
          echo zen_draw_hidden_field('page_id', $footerID);
        }
        ?>

        <div class="form-group">
          <div class="col-sm-12"><?php echo (($form_action == 'insert') ? '<button type="submit" class="btn btn-primary">' . IMAGE_INSERT . '</button>' : '<button type="submit" class="btn btn-primary">' . IMAGE_UPDATE . '</button>') . ' <a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . (isset($_GET['footerID']) ? 'footerID=' . $_GET['footerID'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>'; ?></div>
        </div>
        <div class="form-group">
            <?php echo zen_draw_label(TEXT_COLUMN_HEADER, 'col_header', 'class="col-sm-3 control-label"'); ?>
          <div class="col-sm-9 col-md-6">
            <span class="help-block"><?php echo TEXT_COLUMN_HEADER_TIP; ?></span>
            <?php
// modified code for multi-language support
            $col_header = '';
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
              if (isset($_GET['footerID']) && zen_not_null($_GET['footerID'])) {
                $colHeaderQuery = "SELECT col_header
                                   FROM " . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT . "
                                   WHERE page_id = '" . $_GET['footerID'] . "'
                                   AND language_id = '" . $languages[$i]['id'] . "'";
                $colHeader = $db->Execute($colHeaderQuery);
                $col_header = $colHeader->fields['col_header'];
              } else {
                $col_header = '';
              }
              ?>
              <div class="input-group">
                <span class="input-group-addon"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?></span>
                <?php echo zen_draw_input_field('col_header[' . $languages[$i]['id'] . ']', htmlspecialchars($col_header, ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_FLEXIBLE_FOOTER_MENU_CONTENT, 'col_header') . ' class="form-control"'); ?>
              </div>
              <?php
            }
// end modified code for multi-language support
            ?>
          </div>
        </div>
        <div class="form-group">
            <?php echo zen_draw_label(TEXT_PAGES_NAME, 'page_title', 'class="col-sm-3 control-label"'); ?>
          <div class="col-sm-9 col-md-6">
            <span class="help-block"><?php echo TEXT_PAGES_NAME_TIP; ?></span>
            <?php
// modified code for multi-language support
            $page_title = '';
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
              if (isset($_GET['footerID']) && zen_not_null($_GET['footerID'])) {
                $pageTitleQuery = "SELECT page_title
                                   FROM " . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT . "
                                   WHERE page_id = " . (int)$_GET['footerID'] . "
                                   AND language_id = " . (int)$languages[$i]['id'];
                $pageTitle = $db->Execute($pageTitleQuery);
                $page_title = $pageTitle->fields['page_title'];
              } else {
                $page_title = '';
              }
              ?>
              <div class="input-group">
                <span class="input-group-addon"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?></span>
                <?php echo zen_draw_input_field('page_title[' . $languages[$i]['id'] . ']', htmlspecialchars($page_title, ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_FLEXIBLE_FOOTER_MENU_CONTENT, 'page_title') . ' class="form-control"'); ?>
              </div>
              <br>
              <?php
            }
// end modified code for multi-language support
            ?>
          </div>
        </div>

        <?php if (($footerInfo->col_image) != ('')) { ?>
          <div class="form-group">
              <?php echo zen_draw_label(TEXT_HAS_IMAGE, '', 'class="col-sm-3 control-label"'); ?>
            <div class="col-sm-9 col-md-6"><?php echo $footerInfo->col_image; ?></div>
          </div>
        <?php } ?>
        <div class="form-group">
            <?php echo zen_draw_label(TEXT_USE_IMAGE, 'col_image', 'class="col-sm-3 control-label"'); ?>
          <div class="col-sm-9 col-md-6">
            <span class="help-block"><?php echo TEXT_USE_IMAGE_TIP; ?></span>
            <?php echo zen_draw_file_field('col_image'); ?>
          </div>
        </div>
        <div class="form-group">
            <?php echo zen_draw_label(TEXT_DELETE_IMAGE, 'image_delete', 'class="col-sm-3 control-label"'); ?>
          <div class="col-sm-9 col-md-6">
            <label class="radio-inline"><?php echo zen_draw_radio_field('image_delete', '0', true) . TEXT_DELETE_IMAGE_NO; ?></label>
            <label class="radio-inline"><?php echo zen_draw_radio_field('image_delete', '1', false) . TEXT_DELETE_IMAGE_YES; ?></label>
          </div>
        </div>
        <div class="form-group">
            <?php echo zen_draw_label(TEXT_LINKAGE, 'page_url', 'class="col-sm-3 control-label"'); ?>
          <div class="col-sm-9 col-md-6">
            <span class="help-block"><?php echo TEXT_LINKAGE_TIP; ?></span>
            <?php echo zen_draw_input_field('page_url', zen_not_null($footerInfo->page_url) ? $footerInfo->page_url : '', zen_set_field_length(TABLE_FLEXIBLE_FOOTER_MENU, page_url) . ' class="form-control"'); ?>
          </div>
        </div>
        <div class="form-group">
            <?php echo zen_draw_label(TEXT_ADD_TEXT, 'col_html_text', 'class="col-sm-3 control-label"'); ?>
          <div class="col-sm-9 col-md-6">
            <span class="help-block"><?php echo TEXT_ADD_TEXT_TIP; ?></span>
            <?php
// modified code for multi-language support
            $col_html_text = '';

            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
              if (isset($_GET['footerID']) && zen_not_null($_GET['footerID'])) {
                $colTextQuery = "SELECT col_html_text
                                 FROM " . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT . "
                                 WHERE page_id = " . (int)$_GET['footerID'] . "
                                 AND language_id = " . (int)$languages[$i]['id'];
                $colText = $db->Execute($colTextQuery);
                $col_html_text = $colText->fields['col_html_text'];
              } else {
                $col_html_text = '';
              }
              ?>
              <div class="input-group">
                <span class="input-group-addon"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?></span>
                <?php echo zen_draw_textarea_field('col_html_text[' . $languages[$i]['id'] . ']', 'soft', '100%', '20', htmlspecialchars($col_html_text, ENT_COMPAT, CHARSET, TRUE), ' class="editorHook form-control"'); ?>
              </div>
              <br>
              <?php
            }
// end modified code for multi-language support
            ?>
          </div>
        </div>
        <div class="form-group">
            <?php echo zen_draw_label(TEXT_COLUMN, 'col_id', 'class="col-sm-3 control-label"'); ?>
          <div class="col-sm-9 col-md-6">
            <span class="help-block"><?php echo TEXT_COLUMN_TIP; ?></span>
            <?php echo zen_draw_input_field('col_id', $footerInfo->col_id, 'class="editorHook form-control"'); ?>
          </div>
        </div>
        <div class="form-group">
            <?php echo zen_draw_label(TEXT_COLUMN_SORT, 'col_sort_order', 'class="col-sm-3 control-label"'); ?>
          <div class="col-sm-9 col-md-6">
            <span class="help-block"><?php echo TEXT_COLUMN_SORT_TIP; ?></span>
            <?php echo zen_draw_input_field('col_sort_order', $footerInfo->col_sort_order, 'class="editorHook form-control"'); ?>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-12"><?php echo (($form_action == 'insert') ? '<button type="submit" class="btn btn-primary">' . IMAGE_INSERT . '</button>' : '<button type="submit" class="btn btn-primary">' . IMAGE_UPDATE . '</button>') . ' <a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . (isset($_GET['footerID']) ? 'footerID=' . $_GET['footerID'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>'; ?></div>
        </div>
        <?php echo '</form>'; ?>
        <?php
      } else {
        ?>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 configurationColumnLeft">
            <table class="table table-hover">
              <thead>
                <tr class="dataTableHeadingRow">
                  <th class="dataTableHeadingContent"><?php echo FFM_TABLE_TITLE_HEADER; ?></th>
                  <th class="dataTableHeadingContent"><?php echo FFM_TABLE_TITLE_PAGE_NAME; ?></th>
                  <th class="dataTableHeadingContent"><?php echo FFM_TABLE_TITLE_IMAGE; ?></th>
                  <th class="dataTableHeadingContent"><?php echo TABLE_COLUMN_ID; ?></th>
                  <th class="dataTableHeadingContent"><?php echo TABLE_SORT_ORDER; ?></th>
                  <th class="dataTableHeadingContent text-center"><?php echo TABLE_STATUS; ?></th>
                  <th class="dataTableHeadingContent">&nbsp;</th>
                  <th class="dataTableHeadingContent">&nbsp;</th>
                </tr>
              </thead>
              <tbody>

                <?php
// query modified for multi-language support
                $flexfooter_query_raw = "SELECT f.page_id, f.language_id, f.col_image, f.status, f.date_added, f.last_update, f.col_sort_order, f.col_id,
                                                ft.page_title, ft.col_header, ft.col_html_text
                                         FROM " . TABLE_FLEXIBLE_FOOTER_MENU . " f,
                                              " . TABLE_FLEXIBLE_FOOTER_MENU_CONTENT . " ft
                                         WHERE f.page_id = ft.page_id
                                         AND ft.language_id = " . (int)$_SESSION['languages_id'] . "
                                         ORDER BY col_id ASC, col_sort_order";
// end of modification
// Split Page
// reset page when page is unknown
                if (($_GET['page'] == '' || $_GET['page'] == '1') && $_GET['footerID'] != '') {
                  $check_page = $db->Execute($flexfooter_query_raw);
                  $check_count = 1;
                  if ($check_page->RecordCount() > MAX_DISPLAY_SEARCH_RESULTS_EZPAGE) {
                    foreach ($check_page as $item) {
                      if ($item['page_id'] == $_GET['footerID']) {
                        break;
                      }
                      $check_count++;
                    }
                    $_GET['page'] = round((($check_count / MAX_DISPLAY_SEARCH_RESULTS_EZPAGE) + (fmod_round($check_count, MAX_DISPLAY_SEARCH_RESULTS_EZPAGE) != 0 ? .5 : 0)), 0);
                  } else {
                    $_GET['page'] = 1;
                  }
                }

                $pages_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_EZPAGE, $flexfooter_query_raw, $pages_query_numrows);
                $flexfooter = $db->Execute($flexfooter_query_raw);

                foreach ($flexfooter as $item) {
                  if ((!isset($_GET['footerID']) || (isset($_GET['footerID']) && ($_GET['footerID'] == $item['page_id']))) && !isset($footerInfo) && (substr($action, 0, 3) != 'new')) {
                    $footerInfo_array = array_merge($item);
                    $footerInfo = new objectInfo($footerInfo_array);
                  }
                  if (isset($footerInfo) && is_object($footerInfo) && ($item['page_id'] == $footerInfo->page_id)) {
                    ?>
                    <tr id="defaultSelected" class="dataTableRowSelected" onclick="document.location.href='<?php echo zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, 'page=' . $_GET['page'] . '&footerID=' . $item['page_id']); ?>'">
                        <?php
                      } else {
                        ?>
                    <tr class="dataTableRow" onclick="document.location.href='<?php echo zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, 'page=' . $_GET['page'] . '&footerID=' . $item['page_id']); ?>'">
                        <?php
                      }
                      ?>
                    <td class="dataTableContent"><?php echo $item['col_header']; ?></td>
                    <td class="dataTableContent"><?php echo $item['page_title']; ?></td>
                    <td class="dataTableContent"><?php echo zen_image((($request_type == 'SSL') ? HTTPS_SERVER . DIR_WS_HTTPS_CATALOG : HTTP_SERVER . DIR_WS_CATALOG ) . DIR_WS_IMAGES . $item['col_image']); ?></td>
                    <td class="dataTableContent"><?php echo $item['col_id']; ?></td>
                    <td class="dataTableContent"><?php echo $item['col_sort_order']; ?></td>
                    <td class="dataTableContent text-center">
                        <?php echo ($item['status'] == '1' ?  '<a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, 'page=' . $_GET['page'] . '&footerID=' . $item['page_id'] . '&action=setflag&flag=0') . '">' . zen_image(DIR_WS_IMAGES . 'icon_green_on.gif', ICON_STATUS_GREEN) . '</a>' : '<a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, 'page=' . $_GET['page'] . '&footerID=' . $item['page_id'] . '&action=setflag&flag=1') . '">' . zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', ICON_STATUS_RED) . '</a>')
                        ?>
                    </td>
                    <td class="dataTableContent text-right">
                        <?php
                        if (isset($footerInfo) && is_object($footerInfo) && ($item['page_id'] == $footerInfo->page_id)) {
                          echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
                        } else {
                          echo '<a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, zen_get_all_get_params(array('footerID')) . 'footerID=' . $item['page_id']) . '">' . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                        }
                        ?></td>
                    <td class="dataTableContent text-right">&nbsp;</td>
                  </tr>
                  <?php
                }
                ?>
              </tbody>
            </table>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 configurationColumnRight">
              <?php
              $heading = array();
              $contents = array();
              switch ($action) {
                case 'delete':
                  $heading[] = array('text' => '<h4>' . $footerInfo->col_header . $footerInfo->page_title . '</h4>');

                  $contents = array('form' => zen_draw_form('pages', FILENAME_FLEXIBLE_FOOTER_MENU, 'page=' . $_GET['page'] . '&action=delete_confirm') . zen_draw_hidden_field('footerID', $footerInfo->page_id));
                  $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
                  $contents[] = array('text' => '<br><b>' . $footerInfo->page_title . '</b>');
                  if ($footerInfo->col_image) {
                    $contents[] = array('text' => '<br>' . zen_draw_checkbox_field('delete_image', 'on', true) . ' ' . FFM_TEXT_DELETE_IMAGE . '?');
                  }
                  $contents[] = array('align' => 'center', 'text' => '<br><button type="submit" class="btn btn-danger">' . IMAGE_DELETE . '</button> <a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, 'page=' . $_GET['page'] . '&footerID=' . $_GET['footerID']) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
                  break;
                default:
                  if (is_object($footerInfo)) {
                    $heading[] = array('text' => '<h4>' . $footerInfo->col_header . $footerInfo->page_title . '</h4>');

                    $contents[] = array('align' => 'center', 'text' => '<br><a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, 'page=' . $_GET['page'] . '&footerID=' . $footerInfo->page_id . '&action=new') . '" class="btn btn-primary" role="button">' . IMAGE_EDIT . '</a> <a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, 'page=' . $_GET['page'] . '&footerID=' . $footerInfo->page_id . '&action=delete') . '" class="btn btn-warning">' . IMAGE_DELETE . '</a><br>');

                    $contents[] = array('text' => '<br>' . BOX_INFO_STATUS . ' ' . ($footerInfo->status == 0 ? ICON_STATUS_RED : ICON_STATUS_GREEN));

                    if (zen_not_null($footerInfo->col_image)) {
                      $contents[] = array('text' => '<br>' . zen_image(DIR_WS_CATALOG_IMAGES . $footerInfo->col_image, $footerInfo->page_title) . '<br />' . $footerInfo->page_title);
                    } else {
                      $contents[] = array('text' => '<br>' . BOX_INFO_NO_IMAGE);
                    }

                    $contents[] = array('text' => '<br>' . BOX_INFO_TEXT . '<br> ' . $footerInfo->col_html_text);
                  }
                  break;
              }

              if ((zen_not_null($heading)) && (zen_not_null($contents))) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
              ?>
          </div>
        </div>
        <div class="row">
          <table class="table">
            <tr>
              <td><?php echo $pages_split->display_count($pages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_EZPAGE, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PAGES); ?></td>
              <td class="text-right"><?php echo $pages_split->display_links($pages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_EZPAGE, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'info', 'x', 'y', 'ezID'))); ?></td>
            </tr>
            <tr>
              <td class="text-right" colspan="2"><a href="<?php echo zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU, 'action=new'); ?>" class="btn btn-primary" role="button"><?php echo IMAGE_INSERT; ?></a></td>
            </tr>
          </table>
        </div>
        <?php
      }
      ?>
      <!-- body_text_eof //-->
    </div>
    <!-- body_eof //-->

    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>