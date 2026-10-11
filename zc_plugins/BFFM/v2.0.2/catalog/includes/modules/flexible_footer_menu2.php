<?php
/**
 * Flexible Footer Menu Multilingual (for Bootstrap)
 *
 * Last updated: v2.0.0
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 *
 * @added for version 1.0 by ZCAdditions.com (rbarbour) 4-17-2013 $
 * @updated for version 1.1 by Zen4All.nl (design75) 6-24-2015 $
 */
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

global $db, $lng;

$languages = $lng->get_languages_by_code();
foreach ($languages as $code => $lang_info) {
    if ($code === DEFAULT_LANGUAGE) {
        $default_language_id = (int)$lang_info['id'];
        break;
    }
}
$session_language_id = (int)$_SESSION['languages_id'];

$footer_query = $db->Execute(
    "SELECT f.col_id, f.col_sort_order AS `sort`, fc.page_url AS `link`, f.col_image AS `image`,
            fc.page_title AS `title`, fc.col_header AS `header`, fc.col_html_text AS `text`,
            f.page_id AS `id`, fc.language_id
       FROM " . TABLE_FLEXIBLE_FOOTER_MENU2 . " f
            INNER JOIN " . TABLE_FLEXIBLE_FOOTER_CONTENT2 . " fc
                ON fc.page_id = f.page_id
               AND fc.language_id IN ($default_language_id, $session_language_id)
      WHERE f.status = 1
      ORDER BY f.col_id, f.col_sort_order, fc.col_header");
$ffmm_content = [];
if ($footer_query->EOF) {
    return;
}

// -----
// First, create an array keyed on each menu entry's unique page_id so that
// any entry that doesn't have a value for the current session's language can
// default to the entry contents for the site's default language.
//
$ffmm_content_by_id = [];
foreach ($footer_query as $footer) {
    // -----
    // Any link is inserted into an href="{link}". If the link starts
    // with https://, add a target="_blank to that link so it opens in a
    // new window. The starting double-quote ends the href=" attribute and
    // the closing double-quote gets added by the original insertion.
    //
    if (strpos($footer['link'], 'https://') === 0) {
        $footer['link'] .= '" target="_blank';
    }

    // -----
    // If the language-id for the current record matches the session's
    // value, use this record for the content.
    //
    if ($session_language_id === (int)$footer['language_id']) {
        $ffmm_content_by_id[$footer['id']] = $footer;
        continue;
    }
    
    // -----
    // If we got here, the site's multi-lingual and the current record
    // isn't for the session's language, so it's for the site's default language.
    //
    // If the current footer-menu page_id hasn't yet been set, set to the
    // default-language's content. Note that the session-based language will
    // overwrite this content, if present.
    //
    if (!isset($ffmm_content_by_id[$footer['id']])) {
        $ffmm_content_by_id[$footer['id']] = $footer;
    }
}

// -----
// Now, convert the 'by-id' array into an array of arrays that's keyed on the menu's column
// number.
//
foreach ($ffmm_content_by_id as $content) {
    $ffmm_content[$content['col_id']][] = $content;
}
