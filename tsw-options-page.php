<?php
/*
* Plugin Name: TSW Options Page
* Plugin URI: https://github.com/tradesouthwest/basic-info-tool
* Description: Display website information on your WordPress administrator screen.
* Version: 1.0.0
* Requires PHP: 7.4
* Requires CP: 2.2
* Author: Tradesouthwest
* Author URI: https://classicpress-themes.com
* Text Domain: basic-info-tool
*
*/
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {	exit; }

/**
 * Add pertinent functions | Hooks from inc/functions file
 * 
 * @since 1.0
 */

// Start the plugin when it is loaded.
register_activation_hook(   __FILE__, 'tsw_options_page_plugin_activation' );
register_deactivation_hook( __FILE__, 'tsw_options_page_plugin_deactivation' );
//register_uninstall_hook(    __FILE__, 'uninstall' );

/**
 * Activate/deactivate hooks
 * 
 */
function tsw_options_page_plugin_activation() 
{

    // Create transient data 
    //set_transient( 'tswop-admin-notice-startup', true, 5 );
   
    return false;
}
function tsw_options_page_plugin_deactivation() 
{
    return false;
}

/**
 * Define the locale for this plugin for internationalization.
 * Set the domain and register the hook with WordPress.
 *
 * @uses slug `tsw_options_page`
 */
add_action( 'plugins_loaded', 'tsw_options_page_load_plugin_textdomain' );

function tsw_options_page_load_plugin_textdomain() 
{

    $plugin_dir = basename( dirname(__FILE__) ) .'/languages';
                  load_plugin_textdomain( 'tsw-options-page', false, $plugin_dir );
}

/**
 * Load settings page in admin 
 * 
 * @since 1.0.0
 */
require_once( plugin_dir_path(__FILE__) . 'includes/class-wp-options-page.php' );
require ( plugin_dir_path(__FILE__) . 'includes/with_tabs.php' );
require ( plugin_dir_path(__FILE__) . 'includes/tabs.php' );

 