<?php
/*
Plugin Name: Facebook Public Photo Slider lite
Version: v1.0.0
Plugin URI: http://www.binnash.com/facebook-public-photo-slider/
Author: Binnash
Author URI: http://www.binnash.com
Description: This Wordpress Plugin Enables User To Show Publicly Shared Photo Albums On Wordpress Site.
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
include_once('class.facebook-photo.php');
define('FACEBOOKPHOTO_VER', '1.0.0');
add_action('plugins_loaded', 'load_wp_facebook_photo_object');
function load_wp_facebook_photo_object(){
	new WPFacebookPhoto();
}
