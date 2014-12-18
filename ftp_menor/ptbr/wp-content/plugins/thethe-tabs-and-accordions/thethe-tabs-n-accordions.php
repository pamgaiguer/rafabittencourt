<?php 
/*
Plugin Name: TheThe Tabs and Accordions
Plugin URI: http://thethefly.com/wp-plugins/thethe-tabs-and-accordions/
Description: TheThe Tabs and Accordions plugin provides Web 2.0 tools - Tabs, Vertical and Horizontal Accordions and Toggles - to your WordPress blog.
Version: 1.0.6
Author: TheThe Fly
Author URI: http://thethefly.com
License: GNU GPL v2
*/

$TheTheTS = array(
    'wp_plugin_dir' => dirname(__FILE__),
	'wp_plugin_dir_url' => WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)),
	'wp_plugin_base_name' => plugin_basename(__FILE__),	
    'wp_plugin_name' => 'TheThe Tabs and Accordions',
    'wp_plugin_version' => '1.0.6'
);

/**
 * Require
 */
require_once $TheTheTS['wp_plugin_dir'] . '/lib/lib.php';
require_once $TheTheTS['wp_plugin_dir'] . '/lib/class.js-accord.php';
require_once $TheTheTS['wp_plugin_dir'] . '/lib/class.js-tabs.php';
require_once $TheTheTS['wp_plugin_dir'] . '/lib/class.js-toggle.php';
require_once $TheTheTS['wp_plugin_dir'] . '/lib/class.js-haccord.php';

/**
 * Install
 */
register_activation_hook( __FILE__, 'TheTheTS_Activate' );
register_deactivation_hook( __FILE__, 'TheTheTS_deActivate' );

if (is_admin()) {
    require_once $TheTheTS['wp_plugin_dir'] . '/thethe-admin.php';
    add_filter('admin_menu','TheTheTS_Menu');
} else {
	
    add_filter('init', 'TheTheTS_Init');

    $jsEffects_Accord = new jsEffects_Accord();
    $jsEffects_Accord->initRegisterShortcode();
    $jsEffects_Accord->initHooks();

    $jsEffects_Tabs = new jsEffects_Tabs();
    $jsEffects_Tabs->initRegisterShortcode();
    $jsEffects_Tabs->initHooks();

    $jsEffects_Toggle = new jsEffects_Toggle();
    $jsEffects_Toggle->initRegisterShortcode();
    $jsEffects_Toggle->initHooks();

    $jsEffects_HAccord = new jsEffects_HAccord();
    $jsEffects_HAccord->initRegisterShortcode();
    $jsEffects_HAccord->initHooks();
}