<?php
/**
 * Flexible Footer Menu Multilingual (for Bootstrap)
 *
 * Last updated: v2.0.0
 *
 * @package admin
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @updated for Flexible Footer Menu v1.0 4/17/2013 ZCadditions.com (Raymond A. Barbour) $
 * @updated for Multilingual 2018-03-17 Zen4All (design75) zen4all.nl$
 */
require 'includes/application_top.php';

$languages = zen_get_languages();

$page_id = (int)($_POST['page_id'] ?? $_GET['page_id'] ?? 0);
$page_id_parameter = ($page_id === 0) ? '' : "page_id=$page_id";
$action = $_GET['action'] ?? '';
switch ($action) {
    case 'setflag':
        if ($page_id !== 0 && isset($_GET['flag']) && in_array($_GET['flag'], ['0', '1'])) {
            $db->Execute(
                "UPDATE " . TABLE_FLEXIBLE_FOOTER_MENU2 . "
                    SET status = " . $_GET['flag'] . "
                  WHERE page_id = $page_id
                  LIMIT 1"
            );
            $messageStack->add_session(SUCCESS_PAGE_STATUS_UPDATED, 'success');
        }
        zen_redirect(zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, $page_id_parameter));
        break;

    case 'insert':
    case 'update':
        $page_url = zen_db_prepare_input($_POST['page_url']);
        $col_id = (int)$_POST['col_id'];
        $col_sort_order = (int)$_POST['col_sort_order'];

        $language_id = (int)$_SESSION['languages_id'];
        $sql_data_array = [
            'col_id' => $col_id,
            'col_sort_order' => $col_sort_order,
            'page_url' => $page_url,
        ];

        if ($action === 'insert') {
             $insert_sql_data = [
                'status' => '0',
                'date_added' => 'now()',
            ];
            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
            zen_db_perform(TABLE_FLEXIBLE_FOOTER_MENU2, $sql_data_array);
            $page_id = zen_db_insert_id();

            $page_title_array = zen_db_prepare_input($_POST['page_title']);
            $col_header_array = zen_db_prepare_input($_POST['col_header']);
            $col_html_text_array = zen_db_prepare_input($_POST['col_html_text']);
            foreach ($languages as $next_lang) {
                $language_id = $next_lang['id'];
                $sql_data_array = [
                    'page_title' => $page_title_array[$language_id],
                    'col_header' => $col_header_array[$language_id],
                    'col_html_text' => $col_html_text_array[$language_id],
                    'language_id' => $language_id,
                    'page_id' => $page_id
                ];
                zen_db_perform(TABLE_FLEXIBLE_FOOTER_CONTENT2, $sql_data_array);
            }
            $messageStack->add_session(SUCCESS_PAGE_INSERTED, 'success');
            zen_record_admin_activity('footer item with ID ' . (int)$page_id . ' added.', 'info');
        } else {
            $insert_sql_data = ['last_update' => 'now()'];
            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
            zen_db_perform(TABLE_FLEXIBLE_FOOTER_MENU2, $sql_data_array, 'update', 'page_id = ' . (int)$page_id);

            $page_title_array = zen_db_prepare_input($_POST['page_title']);
            $col_header_array = zen_db_prepare_input($_POST['col_header']);
            $col_html_text_array = zen_db_prepare_input($_POST['col_html_text']);
            foreach ($languages as $next_lang) {
                $language_id = $next_lang['id'];
                $sql_data_array = [
                    'page_title' => $page_title_array[$language_id],
                    'col_header' => $col_header_array[$language_id],
                    'col_html_text' => $col_html_text_array[$language_id]
                ];
                zen_db_perform(TABLE_FLEXIBLE_FOOTER_CONTENT2, $sql_data_array, 'update', 'page_id = ' . (int)$page_id . ' AND language_id = ' . (int)$language_id);
            }
            $messageStack->add_session(SUCCESS_PAGE_UPDATED, 'success');
        }

        if ($col_image = new upload('col_image')) {
            $col_image->set_destination(DIR_FS_CATALOG_IMAGES . 'footer_images/');
            $col_image_name = '';
            if ($col_image->parse() && $col_image->save()) {
                $col_image_name = 'footer_images/' . $col_image->filename;
            }
            if ($col_image_name !== '' && $col_image->filename !== 'none' && $col_image->filename !== '') {
                $db->Execute(
                    "UPDATE " . TABLE_FLEXIBLE_FOOTER_MENU2 . "
                        SET col_image = '" . $col_image_name . "'
                      WHERE page_id = " . (int)$page_id . "
                      LIMIT 1"
                );
            } elseif ($col_image->filename === 'none' || $_POST['image_delete'] === '1') {
                $db->Execute(
                    "UPDATE " . TABLE_FLEXIBLE_FOOTER_MENU2 . "
                        SET col_image = ''
                      WHERE page_id = " . (int)$page_id . "
                      LIMIT 1"
                );
            }
        }

        zen_redirect(zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, $page_id_parameter));
        break;

    case 'delete_confirm':
        $db->Execute(
            "DELETE FROM " . TABLE_FLEXIBLE_FOOTER_MENU2 . "
              WHERE page_id = " . (int)$page_id
        );
        $db->Execute(
            "DELETE FROM " . TABLE_FLEXIBLE_FOOTER_CONTENT2 . "
              WHERE page_id = " . (int)$page_id
        );
        $messageStack->add_session(SUCCESS_PAGE_REMOVED, 'success');
        zen_redirect(zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2));
        break;

    case 'new':
        $form_action = 'insert';

        $parameters = [
            'col_id' => 1,
            'col_sort_order' => 1,
            'status' => 0,
            'page_url' => '',
            'col_image' => '',
        ];
        $footerInfo = new objectInfo($parameters);

        if ($page_id !== 0) {
            $form_action = 'update';

            $page_query =
                "SELECT *
                   FROM " . TABLE_FLEXIBLE_FOOTER_MENU2 . "
                  WHERE page_id = $page_id
                  LIMIT 1";
            $page = $db->Execute($page_query);
            if ($page->EOF) {
                zen_redirect(zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2));
            }

            $footerInfo->updateObjectInfo($page->fields);
        }
        break;

    default:
        break;
}
?>
<!doctype html>
<html <?= HTML_PARAMS ?>>
<head>
    <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
<?php
if ($editor_handler !== '') {
    include $editor_handler; 
}
?>
</head>
<body>
<!-- header //-->
    <?php require DIR_WS_INCLUDES . 'header.php'; ?>
<!-- header_eof //-->

    <div class="container-fluid">
        <h1><?= HEADING_TITLE ?></h1>
<?php
if ($action === 'new') {
?>
        <?= zen_draw_form('new_page', FILENAME_FLEXIBLE_FOOTER_MENU2, 'action=' . $form_action, 'post', 'enctype="multipart/form-data" class="form-horizontal"') ?>
<?php
    if ($form_action === 'update') {
        echo zen_draw_hidden_field('page_id', $page_id);
    }
?>
        <div class="form-group">
            <div class="col-sm-12"><?= (($form_action == 'insert') ? '<button type="submit" class="btn btn-primary">' . IMAGE_INSERT . '</button>' : '<button type="submit" class="btn btn-primary">' . IMAGE_UPDATE . '</button>') . ' <a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, $page_id_parameter) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>' ?></div>
        </div>

        <div class="form-group">
            <?= zen_draw_label(TEXT_COLUMN, 'col_id', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6">
                <span class="help-block"><?= TEXT_COLUMN_TIP ?></span>
                <?= zen_draw_input_field('col_id', $footerInfo->col_id, 'class="form-control"') ?>
            </div>
        </div>

        <div class="form-group">
            <?= zen_draw_label(TEXT_COLUMN_SORT, 'col_sort_order', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6">
                <span class="help-block"><?= TEXT_COLUMN_SORT_TIP ?></span>
                <?= zen_draw_input_field('col_sort_order', $footerInfo->col_sort_order, 'class="form-control"') ?>
            </div>
        </div>

        <div class="form-group">
            <?= zen_draw_label(TEXT_COLUMN_HEADER, 'col_header', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6">
                <span class="help-block"><?= TEXT_COLUMN_HEADER_TIP ?></span>
<?php
    $col_header = '';
    foreach ($languages as $next_lang) {
        if ($page_id === 0) {
            $col_header = '';
        } else {
            $colHeaderQuery =
                "SELECT col_header
                   FROM " . TABLE_FLEXIBLE_FOOTER_CONTENT2 . "
                  WHERE page_id = " . (int)$page_id . "
                    AND language_id = " . $next_lang['id'] . "
                  LIMIT 1";
            $colHeader = $db->Execute($colHeaderQuery);
            $col_header = $colHeader->fields['col_header'] ?? '';
        }
?>
                <div class="input-group">
                    <span class="input-group-addon"><?= zen_image(DIR_WS_CATALOG_LANGUAGES . $next_lang['directory'] . '/images/' . $next_lang['image'], $next_lang['name']) ?></span>
                    <?= zen_draw_input_field(
                        'col_header[' . $next_lang['id'] . ']',
                        htmlspecialchars($col_header, ENT_COMPAT, CHARSET, true),
                        zen_set_field_length(TABLE_FLEXIBLE_FOOTER_CONTENT2, 'col_header') . ' class="form-control"')
                    ?>
                </div>
<?php
    }
?>
            </div>
        </div>

        <div class="form-group">
            <?= zen_draw_label(TEXT_PAGES_NAME, 'page_title', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6">
                <span class="help-block"><?= TEXT_PAGES_NAME_TIP ?></span>
<?php
    foreach ($languages as $next_lang) {
        if ($page_id === 0) {
            $page_title = '';
        } else {
            $pageTitleQuery =
                "SELECT page_title
                   FROM " . TABLE_FLEXIBLE_FOOTER_CONTENT2 . "
                  WHERE page_id = " . (int)$page_id . "
                    AND language_id = " . (int)$next_lang['id'] . "
                  LIMIT 1";
            $pageTitle = $db->Execute($pageTitleQuery);
            $page_title = $pageTitle->fields['page_title'] ?? '';
        }
?>
                    <div class="input-group">
                        <span class="input-group-addon"><?= zen_image(DIR_WS_CATALOG_LANGUAGES . $next_lang['directory'] . '/images/' . $next_lang['image'], $next_lang['name']) ?></span>
                        <?= zen_draw_input_field(
                            'page_title[' . $next_lang['id'] . ']',
                            htmlspecialchars($page_title, ENT_COMPAT, CHARSET, true),
                            zen_set_field_length(TABLE_FLEXIBLE_FOOTER_CONTENT2, 'page_title') . ' class="form-control"')
                        ?>
                    </div>
<?php
    }
?>
            </div>
        </div>
<?php
    if ($footerInfo->col_image !== '') {
?>
        <div class="form-group">
            <?= zen_draw_label(TEXT_HAS_IMAGE, '', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6"><?= $footerInfo->col_image ?></div>
        </div>
<?php
    }
?>
        <div class="form-group">
            <?= zen_draw_label(TEXT_USE_IMAGE, 'col_image', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6">
                <span class="help-block"><?= TEXT_USE_IMAGE_TIP ?></span>
                <?= zen_draw_file_field('col_image') ?>
            </div>
        </div>

        <div class="form-group">
            <?= zen_draw_label(TEXT_DELETE_IMAGE, 'image_delete', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6">
                <label class="radio-inline"><?= zen_draw_radio_field('image_delete', '0', true) . TEXT_DELETE_IMAGE_NO ?></label>
                <label class="radio-inline"><?= zen_draw_radio_field('image_delete', '1', false) . TEXT_DELETE_IMAGE_YES ?></label>
            </div>
        </div>

        <div class="form-group">
                <?= zen_draw_label(TEXT_LINKAGE, 'page_url', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6">
                <span class="help-block"><?= TEXT_LINKAGE_TIP ?></span>
                <?= zen_draw_input_field('page_url', $footerInfo->page_url ?? '', zen_set_field_length(TABLE_FLEXIBLE_FOOTER_MENU2, 'page_url') . ' class="form-control"') ?>
            </div>
        </div>

        <div class="form-group">
            <?= zen_draw_label(TEXT_ADD_TEXT, 'col_html_text', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6">
                <span class="help-block"><?= TEXT_ADD_TEXT_TIP ?></span>
<?php
    $col_html_text = '';

    foreach ($languages as $next_lang) {
        if ($page_id === 0) {
            $col_html_text = '';
        } else {
            $colTextQuery =
                "SELECT col_html_text
                   FROM " . TABLE_FLEXIBLE_FOOTER_CONTENT2 . "
                  WHERE page_id = " . (int)$page_id . "
                    AND language_id = " . (int)$next_lang['id'] . "
                  LIMIT 1";
            $colText = $db->Execute($colTextQuery);
            $col_html_text = $colText->fields['col_html_text'] ?? '';
        }
?>
                <div class="input-group">
                    <span class="input-group-addon"><?= zen_image(DIR_WS_CATALOG_LANGUAGES . $next_lang['directory'] . '/images/' . $next_lang['image'], $next_lang['name']) ?></span>
                    <?= zen_draw_textarea_field(
                        'col_html_text[' . $next_lang['id'] . ']',
                        'soft',
                        '100%',
                        '20',
                        htmlspecialchars($col_html_text, ENT_COMPAT, CHARSET, true),
                        ' class="editorHook form-control"')
                    ?>
                </div>
<?php
    }
?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12"><?= (($form_action == 'insert') ? '<button type="submit" class="btn btn-primary">' . IMAGE_INSERT . '</button>' : '<button type="submit" class="btn btn-primary">' . IMAGE_UPDATE . '</button>') . ' <a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, $page_id_parameter) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>' ?></div>
        </div>
    <?= '</form>' ?>
<?php
} else {
?>
        <div class="row">
            <div class="col-md-9 text-right">
                <a href="<?= zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, 'action=new') ?>" class="btn btn-primary" role="button">
                    <?= IMAGE_INSERT ?>
                </a>
            </div>
            <div class="col-md-3 text-right">
                <?=
                    zen_draw_form('set_editor_form', FILENAME_FLEXIBLE_FOOTER_MENU2, '', 'get', 'class="form-horizontal"') .
                        zen_draw_label(TEXT_EDITOR_INFO, 'reset_editor', 'class="col-sm-6 col-md-4 control-label"') .
                        '<div class="col-sm-6 col-md-4">' .
                            zen_draw_pull_down_menu('reset_editor', $editors_pulldown, $current_editor_key, 'onchange="this.form.submit();" class="form-control"') .
                        '</div>' .
                        zen_hide_session_id() .
                        zen_draw_hidden_field('action', 'set_editor') .
                    '</form>'
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9 configurationColumnLeft">
                <table class="table table-hover">
                    <thead>
                        <tr class="dataTableHeadingRow">
                            <th class="dataTableHeadingContent"><?= TABLE_COLUMN_ID ?></th>
                            <th class="dataTableHeadingContent"><?= TABLE_SORT_ORDER ?></th>
                            <th class="dataTableHeadingContent"><?= FFM_TABLE_TITLE_HEADER ?></th>
                            <th class="dataTableHeadingContent"><?= FFM_TABLE_TITLE_PAGE_NAME ?></th>
                            <th class="dataTableHeadingContent"><?= FFM_TABLE_TITLE_IMAGE ?></th>
                            <th class="dataTableHeadingContent"><?= TABLE_HEADER_LINK ?></th>
                            <th class="dataTableHeadingContent text-center"><?= TABLE_STATUS ?></th>
                            <th class="dataTableHeadingContent">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
    $flexfooter_query_raw =
        "SELECT ffm.page_id, ffmc.language_id, ffm.col_image, ffm.status, ffm.page_url,
                ffm.col_sort_order, ffm.col_id, ffmc.page_title, ffmc.col_header, ffmc.col_html_text
           FROM " . TABLE_FLEXIBLE_FOOTER_MENU2 . " ffm
                INNER JOIN " . TABLE_FLEXIBLE_FOOTER_CONTENT2 . " ffmc
                    ON ffmc.page_id = ffm.page_id
                   AND ffmc.language_id = " . (int)$_SESSION['languages_id'] . "
          ORDER BY col_id ASC, col_sort_order ASC";
    $flexfooter = $db->Execute($flexfooter_query_raw);

    foreach ($flexfooter as $item) {
        if (($page_id === 0 || $page_id === (int)$item['page_id']) && $action !== 'new' && !isset($footerInfo)) {
            $footerInfo_array = array_merge($item);
            $footerInfo = new objectInfo($footerInfo_array);
        }
        if (isset($footerInfo) && is_object($footerInfo) && $item['page_id'] == $footerInfo->page_id) {
?>
                        <tr id="defaultSelected" class="dataTableRowSelected" onclick="document.location.href='<?= zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, $page_id_parameter) ?>'">
<?php
        } else {
?>
                        <tr class="dataTableRow" onclick="document.location.href='<?= zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, 'page_id=' . $item['page_id']) ?>'">
<?php
        }
?>
                            <td class="dataTableContent"><?= $item['col_id'] ?></td>
                            <td class="dataTableContent"><?= $item['col_sort_order'] ?></td>
                            <td class="dataTableContent"><?= $item['col_header'] ?></td>
                            <td class="dataTableContent"><?= $item['page_title'] ?></td>
                            <td class="dataTableContent">
                                <?= zen_image(HTTPS_SERVER . DIR_WS_HTTPS_CATALOG . DIR_WS_IMAGES . $item['col_image'], '', '', '', 'class="mx-auto d-block img-fluid"') ?>
                            </td>
                            <td class="dataTableContent"><?= $item['page_url'] ?></td>
                            <td class="dataTableContent text-center">
<?php
        if ($item['status'] === '1') {
?>
                                <a href="<?= zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, 'page_id=' . $item['page_id'] . '&action=setflag&flag=0') ?>">
                                    <?= zen_icon('enabled', '', '2x', false, true) ?>
                                </a>
<?php
        } else {
?>
                                <a href="<?= zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, 'page_id=' . $item['page_id'] . '&action=setflag&flag=1') ?>">
                                    <?= zen_icon('disabled', '', '2x', false, true) ?>
                                </a>
<?php
        }
?>
                            </td>
                            <td class="dataTableContent text-right">
<?php
        if (isset($footerInfo) && is_object($footerInfo) && $item['page_id'] == $footerInfo->page_id) {
            echo zen_icon('caret-right', '', '2x', true);
        } else {
            echo '<a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, 'page_id=' . $item['page_id']) . '">' . zen_icon('circle-info', '', '2x', true) . '</a>';
        }
?>
                            </td>
                        </tr>
<?php
    }
?>
                        <tr>
                            <td colspan="8" class="text-right">
                                <a href="<?= zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, 'action=new') ?>" class="btn btn-primary" role="button">
                                    <?= IMAGE_INSERT ?>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-3 configurationColumnRight">
<?php
    $heading = [];
    $contents = [];
    switch ($action) {
        case 'delete':
            $heading[] = ['text' => '<h4>' . $footerInfo->col_header . $footerInfo->page_title . '</h4>'];

            $contents = ['form' => zen_draw_form('pages', FILENAME_FLEXIBLE_FOOTER_MENU2, 'action=delete_confirm') . zen_draw_hidden_field('page_id', $footerInfo->page_id)];
            $contents[] = [
                'align' => 'center',
                'text' => TEXT_INFO_DELETE_INTRO,
            ];
            $contents[] = ['text' => '<br><b>' . $footerInfo->page_title . '</b>'];
            if ($footerInfo->col_image) {
                $contents[] = ['text' => '<br>' . zen_draw_checkbox_field('delete_image', 'on', true) . ' ' . FFM_TEXT_DELETE_IMAGE . '?'];
            }
            $contents[] = [
                'align' => 'center',
                'text' => '<br><button type="submit" class="btn btn-danger">' . IMAGE_DELETE . '</button> <a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, 'page_id=' . $page_id) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>'
            ];
            break;

        default:
            if (is_object($footerInfo)) {
                $heading[] = ['text' => '<h4>' . $footerInfo->col_header . $footerInfo->page_title . '</h4>'];

                $contents[] = [
                    'align' => 'center',
                    'text' => '<a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, 'page_id=' . $footerInfo->page_id . '&action=new') . '" class="btn btn-primary" role="button">' . IMAGE_EDIT . '</a> <a href="' . zen_href_link(FILENAME_FLEXIBLE_FOOTER_MENU2, 'page_id=' . $footerInfo->page_id . '&action=delete') . '" class="btn btn-warning">' . IMAGE_DELETE . '</a>'
                ];

                $contents[] = ['text' => '<br>' . BOX_INFO_STATUS . ' ' . ($footerInfo->status == 0 ? ICON_STATUS_RED : ICON_STATUS_GREEN)];

                if (!empty($footerInfo->col_image)) {
                    $contents[] = ['text' => '<br>' . zen_image(DIR_WS_CATALOG_IMAGES . $footerInfo->col_image, $footerInfo->page_title) . '<br>' . $footerInfo->page_title];
                } else {
                    $contents[] = ['text' => '<br>' . BOX_INFO_NO_IMAGE];
                }

                $contents[] = ['text' => '<br>' . BOX_INFO_TEXT . '<br> ' . $footerInfo->col_html_text];
            }
            break;
    }

    if (!empty($heading) && !empty($contents)) {
        $box = new box();
        echo $box->infoBox($heading, $contents);
    }
?>
            </div>
        </div>
<?php
}
?>
    </div>

    <!-- footer //-->
    <?php require DIR_WS_INCLUDES . 'footer.php'; ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require DIR_WS_INCLUDES . 'application_bottom.php'; ?>
