<?php
/*
Plugin Name: Facebook Album & Photos 
Plugin URI: http://www.gcodelabs.com/wp-plugin-facebook-photos.php
Description: This Plugin Allow to show all Facebook uploaded Album and their photos without INSTALL any APP. By this Plugin , Show the album and photos with the Light box jQuery Effects. For more details visit the <a href="http://www.gcodelabs.com/wp-plugin-facebook-photos.php">Facebook Photos Plugin / Support Page</a>
Author: gCodeLabs
Version: 1.3
Author URI: http://profiles.wordpress.org/gcodelabs/
*/
/*  Copyright 2012  gCodeLabs  (email : admin@gcodelabs.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function addjQueryLightBox() {
	if ( !is_admin() ) { 
	echo("
	<!-- START JLIGHTBOX -->
		<script type='text/javascript' src='".get_bloginfo('wpurl').'/wp-content/plugins/facebook-photos/jq-lightbx/js/jquery.js'."'></script>
		<script type='text/javascript' src='".get_bloginfo('wpurl').'/wp-content/plugins/facebook-photos/jq-lightbx/js/jquery.lightbox-0.5.js'."'></script>
		<link rel='stylesheet' type='text/css' href='".get_bloginfo('wpurl').'/wp-content/plugins/facebook-photos/jq-lightbx/css/jquery.lightbox-0.5.css'."' media='screen' />
		<script type='text/javascript'>
		$(function() {
			$('#gallery a').lightBox();
		});
		</script>
	<!-- END JLIGHTBOX --> 
	");
}
}
function showPhotos()
{
	$url=get_option("fb_page_link");
	$fbOwnerId=get_option("fb_owner_id");
 	require 'fb-sdk/src/facebook.php';
	//// default app, please don't change it///
	$facebook = new Facebook(array(
	  'appId'  => '229874527037340',
	  'secret' => 'fdeebae2b6a4f34d726e1c59d384b92d',
	  'cookie' => true, // enable optional cookie support dont change it  
	));
	
	isset( $_REQUEST['action'] ) ? $action = $_REQUEST['action'] : $action = "";
	if( $action == ''){
	$fql    =   "SELECT aid, cover_pid, name FROM album WHERE owner=$fbOwnerId";
	$param  =   array(
	 'method'    => 'fql.query',
	 'query'     => $fql,
	 'callback'  => ''
	);
	$fqlResult   =   $facebook->api($param);
	foreach( $fqlResult as $keys => $values ){

	
			//to get album cover
		$fql2    =   "select src from photo where pid = '" . $values['cover_pid'] . "'";
		$param2  =   array(
		 'method'    => 'fql.query',
		 'query'     => $fql2,
		 'callback'  => ''
		);
		$fqlResult2   =   $facebook->api($param2);
		foreach( $fqlResult2 as $keys2 => $values2){
			$album_cover = $values2['src'];
		}
		echo "<div style='padding: 20px; float: left;'>";
		echo "<a href='".$url."?action=list_pics&aid=" . $values['aid'] . "&album_name=" . $values['name'] . "'>";
		echo "<img src='$album_cover' border='1'>";
		echo "</a><br />";
		echo $values['name'];
		echo "</div>";
	}
}

if( $action == 'list_pics'){
	isset( $_GET['album_name'] ) ? $album_name = $_GET['album_name'] : $album_name = "";
	
	echo "<div style='padding: 20px; '><a href='".$url."'>Back To Albums</a> | Album Name: <b>" . $album_name . "</b></div>";
	$fql    =   "SELECT pid, src, src_small, src_big, caption FROM photo WHERE aid = '" . $_REQUEST['aid'] ."'  ORDER BY created DESC";
	$param  =   array(
	 'method'    => 'fql.query',
	 'query'     => $fql,
	 'callback'  => ''
	);
	$fqlResult   =   $facebook->api($param);
	
	echo "<div id='gallery'>";
	
	foreach( $fqlResult as $keys => $values ){
		
		if( $values['caption'] == '' ){ 
			$caption = "";
		}else{
		
			$caption = $values['caption'];
		}	
		
		echo "<div style='padding: 10px; width: 150px; height: 170px; float: left;'>";
			echo "<a href=\"" . $values['src_big'] . "\" title=\"" . $caption . "\">";
			echo "<img src='" . $values['src'] . "' style='border:medium solid #ffffff;' />";
			echo "</a>"; 
		echo "</div>";
	}
	echo "</div>";
}
//get_option("animated_account");
}
function fb_setting_menu() {
	add_options_page('Facebook Photos Options', 'Facebook Photos', 8, 'FacebookPhotos', 'fb_photos_options_page');
}

function fb_photos_options_page() {
	echo '<div class="wrap">';
	echo '<h2>Facebook Photos ' . __('Options', 'fbphotos') . '</h2>';
	echo '<form method="post" action="options.php">';
  
	wp_nonce_field('update-options');
  
	echo '<table class="form-table" style="width:900px;">';
	echo '<tr valign="top">';
	echo '<th scope="row">' . __('Facebook Account Owner ID :', 'fbphotos') . '</th>';
	echo '<td><input type="text" name="fb_owner_id" value="' . get_option('fb_owner_id') . '" /> <b> (example : 100002245703208)</b> <br /> you can find your fb id from here: <a href="http://findmyfacebookid.com/" target="_blank">http://findmyfacebookid.com/</a></td>';
	echo '</tr>';
	echo '<tr valign="top">';
	echo '<th scope="row">' . __('Page Link :', 'fbphotos') . '</th>';
	echo '<td><input type="text" name="fb_page_link" style="width:600px;"  value="' . get_option('fb_page_link') . '" /><b>(example : http://www.domain.com/sample-page/)</b> where you want to show the facebook gallery.</td>';
	echo '</tr>';
	echo '</table>';
	echo '<p class="submit">';	
	echo '<input type="submit" class="button-primary" value="' . __('Save Changes') . '" />';
	echo '</p>';
  	settings_fields('fbOwnerID');
    echo '</form>';
	echo '</div>';
	echo '<h2>Plugin Help</h2>';
	echo '<p>For the help you can contact on here :- <a href="http://www.gcodelabs.com/wp-plugin-facebook-photos.php" target="_blank">http://www.gcodelabs.com/wp-plugin-facebook-photos.php</a> by the comment.<br />
		<br /> <p >OR</p> <br />You can send mail at any time at <a href="mailto:gcodelabs@gmail.com">gcodelabs@gmail.com</a> <br /> Our team will try to contact you within 24 working hours.';
	echo '</p>';
	addjQueryLightBox();
	
	// jquery light box here///  
}

function fb_photos_register_settings() {
	register_setting('fbOwnerID', 'fb_owner_id');
	register_setting('fbOwnerID', 'fb_page_link');
	
	}
$plugin_dir = basename(dirname(__FILE__));
add_option("fb_owner_id");
add_option("fb_page_link");

add_action('wp_footer', 'addjQueryLightBox');
if(is_admin()){
	add_action('admin_menu', 'fb_setting_menu');
	add_action('admin_init', 'fb_photos_register_settings');
}


function facebookgalley_func( $atts ){
 return showPhotos();
}
add_shortcode('FBGALLERY', 'facebookgalley_func');

function our_plugin_action_links($links, $file) {
    static $this_plugin;
     if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
    if ($file == $this_plugin) {
        // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=FacebookPhotos">Settings</a>';
        // add the link to the list
        array_unshift($links, $settings_link);
    }
     return $links;
}
add_filter('plugin_action_links', 'our_plugin_action_links', 10, 2);
?>