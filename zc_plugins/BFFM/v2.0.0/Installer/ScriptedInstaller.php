<?php
// -----
// Admin-level installation script for the "encapsulated" Flexible Footer Menu Multi-Lingual
// for the Bootstrap template, by lat9.
//
// Copyright (C) 2024, Vinos de Frutas Tropicales.
//
// Last updated: v2.0.0 (new)
//
use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected function executeInstall()
    {
        global $sniffer;

        // ----
        // Create each of the database tables for the flexible footer menu
        //
        define('TABLE_FLEXIBLE_FOOTER_MENU2', DB_PREFIX . 'flexible_footer_menu2');
        define('TABLE_FLEXIBLE_FOOTER_CONTENT2', DB_PREFIX . 'flexible_footer_content2');

        if (!$sniffer->table_exists(TABLE_FLEXIBLE_FOOTER_MENU2)) {
            $sql = "CREATE TABLE " . TABLE_FLEXIBLE_FOOTER_MENU2 . " (
                page_id int(11) NOT NULL AUTO_INCREMENT,
                col_id int(11) NOT NULL DEFAULT 0,
                col_sort_order int(11) NOT NULL DEFAULT 0,
                status int(1) NOT NULL DEFAULT 0,
                page_url varchar(191) DEFAULT NULL,
                col_image varchar(191) NOT NULL DEFAULT '',
                date_added datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
                last_update datetime DEFAULT NULL,
                PRIMARY KEY (page_id)
            )";
            $this->executeInstallerSql($sql);
        }

        if (!$sniffer->table_exists(TABLE_FLEXIBLE_FOOTER_CONTENT2)) {
            $sql = "CREATE TABLE " . TABLE_FLEXIBLE_FOOTER_CONTENT2 . " (
                page_id int(11) NOT NULL default 0,
                language_id int(11) NOT NULL default 1,
                page_title varchar(64) NOT NULL default '',
                col_header varchar(64) NOT NULL default '',
                col_html_text text,
                PRIMARY KEY (page_id,language_id)
            )";
            $this->executeInstallerSql($sql);
        }

        // -----
        // Record the plugin's tool in the admin menus.
        //
        if (!zen_page_key_exists('flexibleFooter2')) {
            zen_register_admin_page('flexibleFooter2', 'BOX_TOOLS_FLEXIBLE_FOOTER_MENU2', 'FILENAME_FLEXIBLE_FOOTER_MENU2', '', 'tools', 'Y');
        }

        // -----
        // If there's a pre-existing FFM or FFM-Multilingual installed, copy
        // that configuration to the FFM-2 tables.
        //
        if ($this->updateFromNonEncapsulatedVersion() === true) {
            return true;
        }

        // -----
        // Still here? It's an initial install and a set of default footer columns
        // are inserted into the generated tables.
        //
        $this->setDefaultValues();
        return true;
    }

    // -----
    // Not used, initially, but included for the possibility of future upgrades!
    //
    // Note: This (https://github.com/zencart/zencart/pull/6498) Zen Cart PR must
    // be present in the base code or a PHP Fatal error is generated due to the
    // function signature difference.
    //
    protected function executeUpgrade($oldVersion)
    {
    }

    protected function executeUninstall()
    {
        zen_deregister_admin_pages([
            'flexibleFooter2',
        ]);
    }

    // -----
    // If an older, non-encapsulated, version is already installed, copy
    // those settings into their new format.
    //
    protected function updateFromNonEncapsulatedVersion(): bool
    {
        global $sniffer;

        // -----
        // First, check to see if the original Flexible Footer Menu Multilingual
        // is installed.  If so, copy over the footer information from those two tables.
        //
        $table_flexible_footer_menu_content = DB_PREFIX . 'flexible_footer_menu_content';
        $table_flexible_footer_menu = DB_PREFIX . 'flexible_footer_menu';
        if ($sniffer->table_exists($table_flexible_footer_menu_content) && $sniffer->table_exists($table_flexible_footer_menu)) {
            $ffm = $this->executeInstallerSelectQuery(
                "SELECT *
                   FROM $table_flexible_footer_menu"
            );
            $ffm_install = [];
            foreach ($ffm as $next_ffm) {
                $ffm_install[$next_ffm['page_id']] = $next_ffm;
            }

            $ffm_content = $this->executeInstallerSelectQuery(
                "SELECT *
                   FROM $table_flexible_footer_menu_content"
            );
            foreach ($ffm_content as $next_ffmc) {
                $page_id = $next_ffmc['page_id'];
                if (isset($ffm_install[$page_id])) {
                    $ffm_install[$page_id]['lang'][$next_ffmc['language_id']] = $next_ffmc;
                }
            }

            foreach ($ffm_install as $page_id => $menu_content) {
                $ffm_entry = [
                    ['fieldName' => 'page_url', 'value' => $menu_content['page_url'], 'type' => 'string'],
                    ['fieldName' => 'col_image', 'value' => $menu_content['col_image'], 'type' => 'string'],
                    ['fieldName' => 'status', 'value' => $menu_content['status'], 'type' => 'integer'],
                    ['fieldName' => 'col_sort_order', 'value' => $menu_content['col_sort_order'], 'type' => 'integer'],
                    ['fieldName' => 'col_id', 'value' => $menu_content['col_id'], 'type' => 'integer'],
                    ['fieldName' => 'date_added', 'value' => $menu_content['date_added'], 'type' => 'date'],
                ];
                $this->executeInstallerDbPerform(TABLE_FLEXIBLE_FOOTER_MENU2, $ffm_entry);
                $ffm_page_id = $this->dbConn->InsertID();
                foreach ($menu_content['lang'] as $lang_id => $content) {
                    $ffm_entry = [
                        ['fieldName' => 'page_id', 'value' => $ffm_page_id, 'type' => 'integer'],
                        ['fieldName' => 'language_id', 'value' => $content['language_id'], 'type' => 'integer'],
                        ['fieldName' => 'page_title', 'value' => $content['page_title'], 'type' => 'string'],
                        ['fieldName' => 'col_header', 'value' => $content['colheader'], 'type' => 'string'],
                        ['fieldName' => 'col_html_text', 'value' => $content['col_html_text'], 'type' => 'string'],
                    ];
                    $this->executeInstallerDbPerform(TABLE_FLEXIBLE_FOOTER_CONTENT2, $ffm_entry);
                }
            }

            return true;
        }

        // -----
        // Next, see if the original Flexible Footer Menu is installed.  If not, nothing
        // to upgrade.
        //
        if (!$sniffer->table_exists($table_flexible_footer_menu)) {
            return false;
        }

        // -----
        // At this point, the original Flexible Footer Menu is installed. Split the values
        // from that single table into the base/content ones.
        //
        $ffm = $this->executeInstallerSelectQuery(
            "SELECT *
               FROM $table_flexible_footer_menu"
        );
        foreach ($ffm as $content) {
            $ffm_entry = [
                ['fieldName' => 'page_url', 'value' => $content['page_url'], 'type' => 'string'],
                ['fieldName' => 'col_image', 'value' => $content['col_image'], 'type' => 'string'],
                ['fieldName' => 'status', 'value' => $content['status'], 'type' => 'integer'],
                ['fieldName' => 'col_sort_order', 'value' => $content['col_sort_order'], 'type' => 'integer'],
                ['fieldName' => 'col_id', 'value' => $content['col_id'], 'type' => 'integer'],
                ['fieldName' => 'date_added', 'value' => $content['date_added'], 'type' => 'date'],
            ];
            $this->executeInstallerDbPerform(TABLE_FLEXIBLE_FOOTER_MENU2, $ffm_entry);
            $ffm_page_id = $this->dbConn->InsertID();

            $ffm_entry = [
                ['fieldName' => 'page_id', 'value' => $ffm_page_id, 'type' => 'integer'],
                ['fieldName' => 'language_id', 'value' => $content['language_id'], 'type' => 'integer'],
                ['fieldName' => 'page_title', 'value' => $content['page_title'], 'type' => 'string'],
                ['fieldName' => 'col_header', 'value' => $content['colheader'], 'type' => 'string'],
                ['fieldName' => 'col_html_text', 'value' => $content['col_html_text'], 'type' => 'string'],
            ];
            $this->executeInstallerDbPerform(TABLE_FLEXIBLE_FOOTER_CONTENT2, $ffm_entry);
        }
        return true;
    }

    protected function setDefaultValues()
    {
        $samples = [
            [
                'col_id' => 1,
                'col_sort_order' => 1,
                'page_url' => '',
                'col_image' => '',
                'page_title' => '',
                'col_header' => 'Quick Links',
                'col_html_text' => '',
            ],
            [
                'col_id' => 1,
                'col_sort_order' => 11,
                'page_url' => 'index.php',
                'col_image' => '',
                'page_title' => '',
                'col_header' => 'footer_images/Home.png',
                'col_html_text' => '',
            ],
            [
                'col_id' => 1,
                'col_sort_order' => 13,
                'page_url' => 'index.php?main_page=specials',
                'col_image' => '',
                'page_title' => 'Specials',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 1,
                'col_sort_order' => 14,
                'page_url' => 'index.php?main_page=products_new',
                'col_image' => '',
                'page_title' => 'New Products',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 1,
                'col_sort_order' => 15,
                'page_url' => 'index.php?main_page=products_all',
                'col_image' => '',
                'page_title' => 'All Products',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 2,
                'col_sort_order' => 2,
                'page_url' => '',
                'col_image' => '',
                'page_title' => '',
                'col_header' => 'Information',
                'col_html_text' => '',
            ],
            [
                'col_id' => 2,
                'col_sort_order' => 21,
                'page_url' => 'index.php?main_page=about_us',
                'col_image' => '',
                'page_title' => 'About Us',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 2,
                'col_sort_order' => 22,
                'page_url' => 'index.php?main_page=site_map',
                'col_image' => '',
                'page_title' => 'Site Map',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 2,
                'col_sort_order' => 23,
                'page_url' => 'index.php?main_page=gv_faq',
                'col_image' => '',
                'page_title' => 'Gift Certificate FAQ',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 2,
                'col_sort_order' => 24,
                'page_url' => 'index.php?main_page=discount_coupon',
                'col_image' => '',
                'page_title' => 'Discount Coupons',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 2,
                'col_sort_order' => 25,
                'page_url' => 'index.php?main_page=unsubscribe',
                'col_image' => '',
                'page_title' => 'Newsletter Unsubscribe',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 3,
                'col_sort_order' => 3,
                'page_url' => 'index.php?main_page=contact_us',
                'col_image' => '',
                'page_title' => '',
                'col_header' => 'Customer Service',
                'col_html_text' => '',
            ],
            [
                'col_id' => 3,
                'col_sort_order' => 31,
                'page_url' => 'index.php?main_page=shippinginfo',
                'col_image' => '',
                'page_title' => 'Shipping & Returns',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 3,
                'col_sort_order' => 32,
                'page_url' => 'index.php?main_page=contact_us',
                'col_image' => '',
                'page_title' => 'Contact Us',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 3,
                'col_sort_order' => 33,
                'page_url' => 'index.php?main_page=privacy',
                'col_image' => '',
                'page_title' => 'Privacy Notice',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 3,
                'col_sort_order' => 34,
                'page_url' => 'index.php?main_page=conditions',
                'col_image' => '',
                'page_title' => 'Conditions of Use',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 3,
                'col_sort_order' => 35,
                'page_url' => '',
                'col_image' => '',
                'page_title' => '',
                'col_header' => 'Account Links',
                'col_html_text' => '',
            ],
            [
                'col_id' => 3,
                'col_sort_order' => 36,
                'page_url' => 'index.php?main_page=account',
                'col_image' => '',
                'page_title' => 'My Account',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 3,
                'col_sort_order' => 37,
                'page_url' => 'index.php?main_page=order_status',
                'col_image' => '',
                'page_title' => 'Track Your Order',
                'col_header' => '',
                'col_html_text' => '',
            ],
            [
                'col_id' => 4,
                'col_sort_order' => 4,
                'page_url' => '',
                'col_image' => '',
                'page_title' => '',
                'col_header' => '',
                'col_html_text' =>
                    '<a href="https://twitter.com" target="_blank" class="mx-2"><i class="fa-brands fa-twitter"></i></a>' .
                    '<a href="https://www.instagram.com" target="_blank" class="mx-2"><i class="fa-brands fa-instagram"></i></a>' .
                    '<a href="https://www.facebook.com" target="_blank"><i class="fa-brands fa-facebook"></i></a>' .
            ],
        ];

        $langs = new language();
        $active_languages = array_keys($langs->get_languages_by_id());

        $ffm_sql =
            "INSERT INTO " . TABLE_FLEXIBLE_FOOTER_MENU2 . "
                (page_url, col_image, status, col_sort_order, col_id, date_added)
             VALUES
                (%s)";
        $ffm_content_sql =
            "INSERT INTO " . TABLE_FLEXIBLE_FOOTER_CONTENT2 . "
                (page_id, language_id, page_title, col_header, col_html_text)
             VALUES
                (%s)";

        foreach ($samples as $sample) {
            $ffm_record =
                $sample['page_url'] . ', ' .
                $sample['col_image'] . ', ' .
                '1, ',
                $sample['col_sort_order'] . ', ' .
                $sample['col_id'] . ', ' .
                'now()';
            $ffm_record_sql = sprintf($ffm_sql, $ffm_record);
            $this->executeInstallerSql($ffm_record_sql);
            $ffm_page_id = $this->dbConn->InsertID();

            foreach ($active_languages as $language_id) {
                $ffm_content =
                    "$ffm_page_id, $language_id, " .
                    $sample['page_title'] . ', ' .
                    $sample['col_header'] . ', ' .
                    $sample['col_html_text'];
                $ffm_content_record_sql = sprintf($ffm_content_sql, $ffm_content);
                $this->executeInstallerSql($ffm_content_record_sql);
            }
        }
    }
}
