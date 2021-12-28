<?php
/*
Plugin Name: Micronalytics
Description: This plugin adds the Microanalytics script at the footer of every page.
Version: 2.2.1
Author: Wakonda
*/

    function microanalytics_call_after_install() {
        $fields              = array();
        $fields["s_section"] = 'plugin-microanalytics';
        $fields["s_name"]    = 'microanalytics_id';
        $fields["e_type"]    = 'STRING';
        Preference::newInstance()->insert($fields);
    }

    function microanalytics_call_after_uninstall() {
        Preference::newInstance()->delete( array("s_section" => "plugin-microanalytics", "s_name" => "microanalytics_id") );
    }

    function microanalytics_actions() {
        $dao_preference = new Preference();
        $option         = Params::getParam('option');

        if( Params::getParam('file') != 'microanalytics/admin.php' ) {
            return '';
        }

        if( $option == 'stepone' ) {
            $webid = Params::getParam('webid');
            Preference::newInstance()->update(
                array("s_value"   => $webid),
                array("s_section" => "plugin-microanalytics", "s_name" => "microanalytics_id")
            );

            osc_add_flash_ok_message(__('The tracking ID has been updated', 'microanalytics'), 'admin');
            osc_redirect_to(osc_admin_render_plugin_url('microanalytics/admin.php'));
        }
    }
    osc_add_hook('init_admin', 'microanalytics_actions');

    function microanalytics_admin() {
        osc_admin_render_plugin('microanalytics/admin.php');
    }

    // HELPER
    function osc_microanalytics_id() {
        return(osc_get_preference('microanalytics_id', 'plugin-microanalytics'));
    }

    /**
     * This function is called every time the page footer is being rendered
     */
    function microanalytics_footer() {
        if( osc_microanalytics_id() != '' ) {
            $id = osc_microanalytics_id();
            require_once(osc_plugins_path() . 'microanalytics/footer.php');
        }
    }

    function google_admin_menu() {
        osc_admin_menu_plugins('Microanalytics', osc_admin_render_plugin_url('microanalytics/admin.php'), 'microanalytics_submenu');
    }

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), 'microanalytics_call_after_install');
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'microanalytics_call_after_uninstall');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'microanalytics_admin');
    osc_add_hook('footer', 'microanalytics_footer');
    osc_add_hook('admin_menu_init', 'microanalytics_admin_menu');

?>