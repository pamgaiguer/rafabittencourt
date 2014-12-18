<?php
/*
Plugin Name: Srizon Facebook Album
Plugin URI: http://www.srizon.com/wordpress-plugin/srizon-facebook-album
Description: Show your Facebook Albums/Galleries on your WordPress Site
Version: 1.1.4
Author: Afzal
Author URI: http://www.srizon.com/contact
*/
require_once 'srizon-fb-ui.php';
require_once 'srizon-fb-db.php';
if(file_exists(dirname(__FILE__).'/srizon-fb-album-front-pro.php')){
	require_once 'srizon-fb-album-front-pro.php';
}
else{
	require_once 'srizon-fb-album-front.php';
}
if(file_exists(dirname(__FILE__).'/srizon-fb-gallery-front-pro.php')){
	require_once 'srizon-fb-gallery-front-pro.php';
}
else{
	require_once 'srizon-fb-gallery-front.php';
}

register_activation_hook(__FILE__,'srz_fb_install');
register_uninstall_hook(__FILE__,'srz_fb_uninstall');
add_action('admin_menu', 'srz_fb_menu');
add_action('wp_enqueue_scripts', 'srz_fb_enqueue_script');
add_shortcode('srizonfbalbum', 'srz_fb_album_shortcode');
add_shortcode('srizonfbgallery', 'srz_fb_gallery_shortcode');


function srz_fb_install(){
	SrizonFBDB::CreateDBTables();
}

function srz_fb_uninstall(){
	SrizonFBDB::DeleteDBTables();
	delete_option('srzfbcomm');
}

function srz_fb_menu() {
	add_menu_page( 'FB Album', "FB Album",  'manage_options', 'SrzFb', 'srz_fb_options_page', srz_fb_get_resource_url('images/srzfb-icon.png'));
	add_submenu_page( 'SrzFb', "FB Album", "Albums", 'manage_options', 'SrzFb-Albums', 'srz_fb_albums');
	add_submenu_page( 'SrzFb', "FB Album", "Galleris", 'manage_options', 'SrzFb-Galleries', 'srz_fb_galleries');
}

function srz_fb_options_page(){
	SrizonFBUI::PageWrapStart();
	if($_POST['submit']){
		if( wp_verify_nonce($_POST['srjfb_submit'],'SrjFb') == false ) die('Nice Try!');
		$optvar = SrizonFBDB::SaveCommonOpt();
	}
	else{
		$optvar = SrizonFBDB::GetCommonOpt();
	}
	echo '<div class="icon32" id="icon-tools"><br /></div><h2>Srizon FB Album Option Page</h2>';
	SrizonFBUI::OptionWrapperStart();
	
	SrizonFBUI::RightColStart();
	SrizonFBUI::BoxHeader('box1', "About This Plugin");
	echo '<p>This Plugin will show your Facebook fanpage album(s) into your wordpress site. Select "Albums" or "Galleries" from submenu and add a new album or gallery. Use the generated shortcode on your post/page to display the album/gallery</p>';
	SrizonFBUI::BoxFooter();
	SrizonFBUI::RightColEnd();
	
	SrizonFBUI::LeftColStart();
	include 'common-option-form.php';
	SrizonFBUI::LeftColEnd();
	
	SrizonFBUI::OptionWrapperEnd();
	SrizonFBUI::PageWrapEnd();
}

function srz_fb_albums(){
	SrizonFBUI::PageWrapStart();
	if(isset($_REQUEST['srzf'])){
		switch ($_REQUEST['srzf']){
			case 'edit':
				srz_fb_albums_edit();
				break;
			case 'save';
				srz_fb_albums_save();
				break;
			case 'delete':
				srz_fb_albums_delete();
				break;
			case 'sync':
				srz_fb_albums_sync();
				break;
			default:
				break;
		}
	}
	else{
		echo '<h2>Albums<a href="admin.php?page=SrzFb-Albums&srzf=edit" class="add-new-h2">Add New</a></h2>';
		$albums = SrizonFBDB::GetAllAlbums();
		include('album-table.php');
	}
	SrizonFBUI::PageWrapEnd();
}

function srz_fb_albums_sync(){
	if(isset($_GET['id'])){
		SrizonFBDB::SyncAlbum($_GET['id']);
	}
	echo '<h2>Cached Data Deleted! Album will be synced on next load.</h2>';
	echo '<a href="admin.php?page=SrzFb-Albums">Go Back to Albums</a>';
}

function srz_fb_galleries_sync(){
	if(isset($_GET['id'])){
		SrizonFBDB::SyncGallery($_GET['id']);
	}
	echo '<h2>Cached Data Deleted! Gallery and it\'s albums will be synced on next load.</h2>';
	echo '<a href="admin.php?page=SrzFb-Galleries">Go Back to Galleries</a>';
}

function srz_fb_albums_edit(){
	if(isset($_GET['id'])){
		echo '<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Edit Album</h2>';
		$value_arr = SrizonFBDB::GetAlbum($_GET['id']);
	}
	else{
		echo '<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Add New Album</h2>';
		$value_arr = array(
			'title'=>'',
			'albumid'=>'',
			'updatefeed'=>'600',
			'shuffle_images'=>'no',
			'thumbwidth'=>'150',
			'thumbheight'=>'150',
			'totalimg'=>'18',
			'liststyle'=>'slidergridv',
			'tpltheme'=>'white',
			'paginatenum'=>'18',
			'gridrow'=>'3',
			'gridcol'=>'2',
			'grid_margin_top'=>'5',
			'grid_margin_right'=>'5',
			'autoplay'=>'1',
			'showbutton'=>'1',
			'slide_interval'=>'5000',
			'slide_speed'=>'1000',
			'slide_style'=>'easeOutBack',
			);
	}
	SrizonFBUI::OptionWrapperStart();
	
	SrizonFBUI::RightColStart();
	SrizonFBUI::BoxHeader('box1', "About Single Album");
	echo '<div><div class="misc-pub-section">As the name suggests, It\'s a single Facebook fanpage album.</div><div class="misc-pub-section">You need to put the ID (or IDs) of the fanpage album(s). If multiple IDs are used, they will be merged into a single album.</div></div>';
	SrizonFBUI::BoxFooter();
	SrizonFBUI::RightColEnd();
	
	
	SrizonFBUI::LeftColStart();
	include 'album-option-form.php';
	SrizonFBUI::LeftColEnd();
	
	SrizonFBUI::OptionWrapperEnd();
}

function srz_fb_albums_save(){
	if( wp_verify_nonce($_POST['srjfb_submit'],'srz_fb_albums') == false ) die('Nice Try!');
	if(!isset($_POST['id'])){
		SrizonFBDB::SaveAlbum(true);
		echo '<h2>New Album Created</h2>';
	}
	else {
		SrizonFBDB::SaveAlbum(false);
		echo '<h2>Album Updated</h2>';
	}
	
	echo '<a href="admin.php?page=SrzFb-Albums">Go Back to Albums</a>';
}

function srz_fb_albums_delete(){
	if(isset($_GET['id'])){
		SrizonFBDB::DeleteAlbum($_GET['id']);
	}
	echo '<h2>Album deleted</h2>';
	echo '<a href="admin.php?page=SrzFb-Albums">Go Back to Albums</a>';
}

function srz_fb_galleries(){
	SrizonFBUI::PageWrapStart();
	if(isset($_REQUEST['srzf'])){
		switch ($_REQUEST['srzf']){
			case 'edit':
				srz_fb_galleries_edit();
				break;
			case 'save':
				srz_fb_galleries_save();
				break;
			case 'delete':
				srz_fb_galleries_delete();
				break;
			case 'sync':
				srz_fb_galleries_sync();
				break;
			default:
				break;
		}
	}
	else{
		echo '<h2>Galleries<a href="admin.php?page=SrzFb-Galleries&srzf=edit" class="add-new-h2">Add New</a></h2>';
		$galleries = SrizonFBDB::GetAllGalleries();
		include('gallery-table.php');
	}
	SrizonFBUI::PageWrapEnd();
}

function srz_fb_galleries_edit(){
	if(isset($_REQUEST['id'])){
		echo '<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Edit Gallery</h2>';
		$value_arr = SrizonFBDB::GetGallery($_GET['id']);
	}
	else{
		echo '<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Add New Gallery</h2>';
		$value_arr = array(
			'title'=>'',
			'pageid'=>'',
			'excludeids'=>'',
			'updatefeed'=>'600',
			'shuffle_images'=>'no',
			'thumbwidth'=>'150',
			'thumbheight'=>'150',
			'totalimg'=>'20',
			'liststyle'=>'slidergridv',
			'tpltheme'=>'white',
			'paginatenum'=>'18',
			'shuffle_albums'=>'no',
			'showtitlethumb'=>'yes',
			'titlethumb_height'=>'50',
			'truncate_len'=>'',
			'gridrow'=>'3',
			'gridcol'=>'2',
			'grid_margin_top'=>'5',
			'grid_margin_right'=>'5',
			'autoplay'=>'1',
			'showbutton'=>'1',
			'slide_interval'=>'5000',
			'slide_speed'=>'1000',
			'slide_style'=>'easeOutBack',
			);
	}
	SrizonFBUI::OptionWrapperStart();
	
	SrizonFBUI::RightColStart();
	SrizonFBUI::BoxHeader('box1', "About Gallery");
	echo '<div><div class="misc-pub-section">Gallery is a 2 level view of your Facebook fanpage albums</div><div class="misc-pub-section">It just takes the page ID and gets all the albums from the page</div><div class="misc-pub-section">You can exclude/remove some albums by using their album IDs on the exclusion list</div><div class="misc-pub-section">First level shows the album covers. Clicking on the cover of an album will take you to the second level listing the thumbs of that album</div></div>';
	SrizonFBUI::BoxFooter();
	SrizonFBUI::RightColEnd();
	
	SrizonFBUI::LeftColStart();
	include 'gallery-option-form.php';
	SrizonFBUI::LeftColEnd();
	
	SrizonFBUI::OptionWrapperEnd();
}

function srz_fb_galleries_save(){
	if( wp_verify_nonce($_POST['srjfb_submit'],'SrzFbGalleries') == false ) die('Nice Try!');
	if(!isset($_POST['id'])){
		SrizonFBDB::SaveGallery(true);
		echo '<h2>New Gallery Created</h2>';
	}
	else {
		SrizonFBDB::SaveGallery(false);
		echo '<h2>Gallery Updated</h2>';
	}
	
	echo '<a href="admin.php?page=SrzFb-Galleries">Go Back to Galleries</a>';
}

function srz_fb_galleries_delete(){
	if(isset($_GET['id'])){
		SrizonFBDB::DeleteGallery($_GET['id']);
	}
	echo '<h2>Gallery deleted</h2>';
	echo '<a href="admin.php?page=SrzFb-Galleries">Go Back to Galleries</a>';	
}

function srz_fb_get_resource_url($relativePath){
	return plugins_url($relativePath, plugin_basename(__FILE__));
}