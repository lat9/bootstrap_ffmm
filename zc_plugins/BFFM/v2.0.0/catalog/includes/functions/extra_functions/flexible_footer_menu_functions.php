<?php

/**
 *
 * Flexible Footer Menu Multilingual
 *
 * @package functions
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * Added by rbarbour (ZCAdditions.com), Flexible Footer Menu 1.1 (3)
 *
 */
function active_footer_page_class($pageid, $pageurl)
{
  global $this_is_home_page;
  $active = '';
  if ($_GET['main_page'] == 'page') {
    $active = ($_GET['id'] == $pageid) ? ' class="activePage"' : '';
  } elseif ($pageurl) {
    $alturl = htmlspecialchars_decode(str_replace(HTTP_SERVER . DIR_WS_CATALOG, '/', $pageurl));
    $active = ((strpos($_SERVER['REQUEST_URI'], $pageurl) !== false && strpos('/index.php?main_page=index', $pageurl) === false) || ( $this_is_home_page and strpos('/index.php?main_page=index', $pageurl) !== false)) ? ' class="activePage"' : '';
  }
  return $active;
}
