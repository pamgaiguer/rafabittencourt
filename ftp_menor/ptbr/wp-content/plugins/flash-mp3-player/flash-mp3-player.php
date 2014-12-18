<?php
/*
Plugin Name: Flash MP3 Player JW2.3
Plugin URI: http://sexywp.com/fmp
Description: This is a mp3 player made by flash. You can add this to your sidebar as a Widget, and you can edit the playlist through the options page. It's a very user friendly widget.
Version: 10.1.9
Author: Charles Tang
Author URI: http://sexywp.com/
*/
if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

if ( !defined('WP_PLUGIN_URL') )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );
if ( !defined('WP_PLUGIN_DIR') )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

$fmp_dir_name = plugin_basename(dirname(__FILE__));

$fmp_jw_dir = WP_PLUGIN_DIR . '/' . $fmp_dir_name;
$fmp_jw_url = WP_PLUGIN_URL . '/' . $fmp_dir_name;

if(function_exists('is_site_admin')){ //this is for wordpress mu
    //this was contributed by the webmaster of http://ixiezi.com
    $fmp_jw_files_dir = WP_CONTENT_DIR . '/blogs.dir/' . $wpdb->blogid . '/config/fmp-jw-files';
    $fmp_jw_files_url = WP_CONTENT_URL . '/blogs.dir/' . $wpdb->blogid . '/config/fmp-jw-files';
}else{
    //this is for indivisul wordpress users.
    $fmp_jw_files_dir = WP_CONTENT_DIR . '/fmp-jw-files';
    $fmp_jw_files_url = WP_CONTENT_URL . '/fmp-jw-files';
}

require_once($fmp_jw_dir . '/inc/class.widget.php');
require_once($fmp_jw_dir . '/inc/class.utils.php');
require_once($fmp_jw_dir . '/inc/class.config_editor.php');
require_once($fmp_jw_dir . '/inc/class.playlist_editor.php');

function flash_mp3_player_init(){
    global $fmp_jw_dir, $fmp_jw_files_dir;
    global $fmp_jw_util;

    if (!file_exists($fmp_jw_files_dir)) if (!wp_mkdir_p($fmp_jw_files_dir . '/')) return;
    if (!file_exists($fmp_jw_files_dir . '/configs/')) if (!wp_mkdir_p($fmp_jw_files_dir . '/configs/')) return;
    if (!file_exists($fmp_jw_files_dir . '/playlists/')) if (!wp_mkdir_p($fmp_jw_files_dir . '/playlists/')) return;

    if (!file_exists($fmp_jw_files_dir . '/configs/fmp_jw_widget_config.xml')){
        if (!copy($fmp_jw_dir . '/player/configs/config.xml', $fmp_jw_files_dir . '/configs/fmp_jw_widget_config.xml'))
            return;
    }
    if (!file_exists($fmp_jw_files_dir . '/playlists/fmp_jw_widget_playlist.xml')){
        if (!copy($fmp_jw_dir . '/player/playlists/playlist.xml', $fmp_jw_files_dir . '/playlists/fmp_jw_widget_playlist.xml'))
            return;
    }

    $fmp_jw_util = new FMP_Utils();

    add_action('wp_loaded', 'fmp_register_js');
    function fmp_register_js() {
        //wp_deregister_script("swfobject");
        wp_register_script("swfobject_original", "http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js", array(), "2.2");

        wp_enqueue_script("swfobject_original");
    }

    //register widget
    add_action('widgets_init', 'fmp_load_widget');
    function fmp_load_widget(){
        register_widget('Flash_MP3_Player_Widget');
    }
    
    add_action('media_buttons', array(&$fmp_jw_util, 'add_media_button'), 30);
    add_shortcode('mp3player', array(&$fmp_jw_util, 'player_shortcode'));

    if(is_admin()){
        add_action('admin_notices', 'safe_check');
        $fmp_config_editor = new FMP_Config_Editor();
        add_action('admin_menu', array(&$fmp_config_editor, 'add_menu_item'));
        $fmp_playlist_editor = new FMP_Playlist_Editor();
        add_action('admin_menu', array(&$fmp_playlist_editor, 'add_menu_item'));
    }
}
add_action('plugins_loaded', 'flash_mp3_player_init');