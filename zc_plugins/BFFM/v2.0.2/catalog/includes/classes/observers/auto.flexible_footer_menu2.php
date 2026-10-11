<?php
/**
 * Flexible Footer Menu Multilingual (for Bootstrap)
 *
 * Last updated: v2.0.0 (new)
 */
use Zencart\Traits\InteractsWithPlugins;

class zcObserverFlexibleFooterMenu2 extends \base
{
    use InteractsWithPlugins;

    // -----
    // On construction, register for the footer's content-injection notification.
    //
    public function __construct()
    {
        // -----
        // Check to see that the active template is one based on the ZCA
        // Bootstrap Template.  If not, nothing further to do.
        //
        if (!function_exists('zca_bootstrap_active') || zca_bootstrap_active() === false) {
            return;
        }

        // -----
        // Use the base trait to determine this plugin's directory location.
        //
        $this->detectZcPluginDetails(__DIR__);

        // -----
        // Watch for the common footer content notification, added in the
        // Bootstrap template's v3.7.4.
        //
        $this->attach(
            $this,
            [
                //- From /includes/templates/bootstrap/common/tpl_footer.php
                'NOTIFY_FOOTER_AFTER_NAVSUPP',
            ]
        );
    }

    // -----
    // Insert the flexible footer-menu into the footer section of the current
    // page.
    //
    public function notify_footer_after_navsupp(&$class, string $e, array $p1)
    {
        // -----
        // Pull in the module file that's created the array of footer-menu columns
        // and content. If the site's got a template-override version, use that; otherwise,
        // use the version shipped with the plugin.
        //
        $override_file = DIR_WS_MODULES . zen_get_module_directory('flexible_footer_menu2.php');
        if (is_file($override_file)) {
            require $override_file;
        } else {
            require $this->pluginManagerInstalledVersionDirectory . 'catalog/' . DIR_WS_MODULES . 'flexible_footer_menu2.php';
        }

        // -----
        // Now, pull in the template file to format the menu's content for display. If a site's got
        // a template-override version, that'll be used instead of the version shipped with the
        // plugin.
        //
        global $template, $current_page_base;
        require $template->get_template_dir('tpl_flexible_footer_menu2.php', DIR_WS_TEMPLATE, $current_page_base, 'templates') . '/tpl_flexible_footer_menu2.php';
    }
}
