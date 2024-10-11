<?php
/**
 * Flexible Footer Menu Multilingual (for Bootstrap)
 *
 * Last updated: v2.0.0
 *
 * @package templateSystem
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 *
 * @added for version 1.0 by ZCAdditions.com (rbarbour) 4-17-2013 $
 */
if (count($ffmm_content) === 0) {
    return;
}

$footer_col_count = count($ffmm_content);
if ($footer_col_count === 0 || $footer_col_count > 12) {
    $footer_col_count = 1;
}
$footer_col_width = (int)ceil(12 / $footer_col_count);
?>
<div id="footer-menu" class="row text-center mt-2">
<?php
foreach ($ffmm_content as $col_id => $column_data) {
?>
    <div class="col-md-<?= $footer_col_width ?> centerBoxWrapper ffm-card">
        <ul class="list-group list-group-flush ffm-list">
<?php
    foreach ($column_data as $column_values) {
        if ($column_values['header'] !== '') {
            if (empty($column_values['link'])) {
?>
            <li class="list-group-item h4 centerBoxHeading ffm-header">
                <?= $column_values['header'] ?>
<?php
            } else {
?>
            <li class="list-group-item h4 centerBoxHeading ffm-header-link">
                <a href="<?= $column_values['link'] ?>"><?= $column_values['header'] ?></a>
<?php
            }
        } elseif ($column_values['title'] !== '') {
?>
            <li class="list-group-item ffm-title">
                <a href="<?= $column_values['link'] ?>">
                    <?= $column_values['title'] ?>
                </a>
<?php
        } elseif ($column_values['image'] !== '') {
            if (empty($column_values['link'])) {
?>
            <li class="list-group-item ffm-image">
                <?= zen_image(DIR_WS_IMAGES . $column_values['image'], '', '', '', 'class="mx-auto d-block img-fluid"') ?>
<?php
            } else {
?>
            <li class="list-group-item ffm-image-link">
                <a href="<?= $column_values['link'] ?>"><?= zen_image(DIR_WS_IMAGES . $column_values['image'], '', '', '', 'class="mx-auto d-block img-fluid"') ?></a>
<?php
            }
        }

        if (!empty($column_values['text'])) {
?>
                <div class="mt-2 ffm-text"><?= $column_values['text'] ?></div>
<?php
        }
?>
            </li>
<?php
    }
?>
        </ul>
    </div>
<?php
}
?>
</div>
