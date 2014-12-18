<?php 
/* 
Plugin Name: MP3-jPlayer
Plugin URI: http://sjward.org/jplayer-for-wordpress
Description: Add mp3 players to posts, pages, and sidebars. HTML5 / Flash. Shortcodes, widgets, and template tags. See the help on the settings page for a full list of options. 
Version: 1.7.3
Author: Simon Ward
Author URI: http://www.sjward.org
License: GPL2
  	
	Copyright 2011  Simon Ward  (email: sinomward@yahoo.co.uk)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
	
	
/* grab class definitions */
$mp3jpath = dirname(__FILE__);
include_once( $mp3jpath . '/mp3j_main.php');
include_once( $mp3jpath . '/mp3j_frontend.php'); // extends main
include_once( $mp3jpath . '/mp3j_widget.php'); // extends WP's widget class

/* Create instance of front */
if ( class_exists("MP3j_Front") ) {
	$mp3_fox = new MP3j_Front();
}

/* Register and init with WP */
if ( isset($mp3_fox) )
{
	if ( !function_exists("mp3Fox_ap") ) {
		function mp3Fox_ap() { 
			global $mp3_fox;
			if ( !isset($mp3_fox) ) {
				return;
			}
			if ( function_exists('add_options_page') ) {
				// settings menu page
				//$pluginpage = add_options_page('MP3 jPlayer', 'MP3 jPlayer', 9, basename(__FILE__), array(&$mp3_fox, 'printAdminPage'));  
				$pluginpage = add_options_page('MP3 jPlayer', 'MP3 jPlayer', 'manage_options', basename(__FILE__), array(&$mp3_fox, 'printAdminPage'));  
				add_action( 'admin_head-'. $pluginpage, array(&$mp3_fox, 'mp3j_admin_header') ); 
				add_action( 'admin_footer-'. $pluginpage, array(&$mp3_fox, 'mp3j_admin_footer') );
			}
		}
	}
	
	// add scripts
	function mp3j_addscripts( $style = "" ) {
		do_action('mp3j_addscripts', $style);
	}
	
	// Depreciated since 1.6
	function mp3j_flag( $set = 1 ) {
		do_action('mp3j_flag', $set);
	}
	
	// write player
	function mp3j_put( $id = "", $pos = "", $dload = "", $play = "", $list = "" ) {
		do_action( 'mp3j_put', $id, $pos, $dload, $play, $list );
	}
	
	// write plugin info
	function mp3j_debug( $display = "" ) {
		do_action('mp3j_debug', $display);
	}
	
	// retrieve library
	function mp3j_grab_library( $format = 1 ) { 
		
		$thereturn = array();
		if ( $format == 1 ) {
			$library = apply_filters('mp3j_grab_library', $thereturn );
			return $library;
		}
		if ( $format == 0 ) {
			$library = apply_filters('mp3j_grab_library_wp', $thereturn );
			return $library;
		}
		else {
			return;
		}
	}
	
	// Depreciated since 1.7 
	function mp3j_set_meta( $tracks, $captions = "", $startnum = 1 ) {
		if ( empty($tracks) || !is_array($tracks) ) {
			return;
		}  
		do_action('mp3j_set_meta', $tracks, $captions, $startnum);
	}
	
	// register widgets
	function mp3jplayer_widget_init() {
		register_widget( 'MP3_jPlayer' );
	}
	if ( class_exists('MP3_jPlayer') ) {	
		add_action( 'widgets_init', 'mp3jplayer_widget_init' );
	}
	
	function mp3jsingle_widget_init() {
		register_widget( 'MP3j_single' );
	}
	if ( class_exists('MP3j_single') ) {	
		add_action( 'widgets_init', 'mp3jsingle_widget_init' );
	}
	
	// register shortcodes
	add_shortcode('mp3t', array(&$mp3_fox, 'inline_play_handler'));
	add_shortcode('mp3j', array(&$mp3_fox, 'inline_play_graphic'));
	add_shortcode('mp3-jplayer', array(&$mp3_fox, 'primary_player'));
	add_shortcode('mp3-link', array(&$mp3_fox, 'link_plays_track'));
	//add_shortcode('mp3-album', array(&$mp3_fox, 'album_player'));
	
	// admin hooks
	add_action('activate_mp3-jplayer/mp3jplayer.php',  array(&$mp3_fox, 'initFox'));
	add_action('deactivate_mp3-jplayer/mp3jplayer.php',  array(&$mp3_fox, 'uninitFox'));
	add_action('admin_menu', 'mp3Fox_ap');
	
	// template hooks
	add_action('wp_head', array(&$mp3_fox, 'header_scripts_handler'), 2);
	//add_action('wp_enqueue_scripts', array(&$mp3_fox, 'header_scripts_handler'));
	
	
	add_filter('the_content', array(&$mp3_fox, 'content_handler'));
	
	//add_filter('get_the_excerpt', array(&$mp3_fox, 'get_excerpt_handler'), 1);
	add_action('wp_footer', array(&$mp3_fox, 'footercode_handler'));
	add_action('mp3j_put', array(&$mp3_fox, 'template_tag_handler'), 10, 5 );
	add_action('mp3j_addscripts', array(&$mp3_fox, 'scripts_tag_handler'), 1, 1 );
	add_filter('mp3j_grab_library', array(&$mp3_fox, 'grablibrary_handler'), 10, 1 );
	add_filter('mp3j_grab_library_wp', array(&$mp3_fox, 'grablibraryWP_handler'), 10, 1 );
	add_action('mp3j_debug', array(&$mp3_fox, 'debug_info'), 10, 1 );
	
	//not used anymore
	add_action('mp3j_set_meta', array(&$mp3_fox, 'set_meta_handler'), 10, 3 );
	add_action('mp3j_flag', array(&$mp3_fox, 'flag_tag_handler'), 10, 1 );
}
?>