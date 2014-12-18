<?php

class SrizonFBDB{
	static function SaveCommonOpt(){
		$optvar = array();
		$optvar['loadlightbox'] = $_POST['loadlightbox'];
		$optvar['lightboxattrib'] = $_POST['lightboxattrib'];
		update_option('srzfbcomm',$optvar);
		return $optvar;
	}
	
	static function GetCommonOpt(){
		$optvar = get_option('srzfbcomm');
		if(!empty($optvar)) return $optvar;
		else{
			$optvardef = array();
			$optvardef['loadlightbox'] = 'yes';
			$optvardef['lightboxattrib'] = 'class="lightbox" rel="lightbox"';
			add_option('srzfbcomm',$optvardef,'',true);
			return $optvardef;
		}
	}
	
	static function SaveAlbum($new=false){
		global $wpdb;
		$table = $wpdb->prefix.'srzfb_albums';
		$data['title'] = $_POST['title'];
		$data['albumid'] = $_POST['albumid'];
		$data['options'] = serialize($_POST['options']);
		if($new){	
			$wpdb->insert($table,$data);
			return $wpdb->insert_id;			
		}
		else{
			$wpdb->update($table,$data,array('id'=>$_POST['id']));
			return $_POST['id'];
		}
	}
	static function SaveGallery($new=false){
		global $wpdb;
		$table = $wpdb->prefix.'srzfb_galleries';
		$data['title'] = $_POST['title'];
		$data['pageid'] = trim($_POST['pageid']);
		$data['options'] = serialize($_POST['options']);
		if($new){	
			$wpdb->insert($table,$data);
			return $wpdb->insert_id;			
		}
		else{
			$wpdb->update($table,$data,array('id'=>$_POST['id']));
			return $_POST['id'];
		}
	}
	static function GetAlbum($id){
		global $wpdb;
		$table = $wpdb->prefix.'srzfb_albums';
		$q = $wpdb->prepare("SELECT * FROM $table WHERE id = %d",$id);
		$album = $wpdb->get_row($q);
		if(!$album) return false;
		$ret = array();
		$ret['id'] = $id;
		$ret['title'] = $album->title;
		$ret['albumid'] = $album->albumid;
		$options = unserialize($album->options);
		foreach($options as $key=>$value){
			$ret[$key] = $value;
		}
		return $ret;
	}
	
	static function GetGallery($id){
		global $wpdb;
		$table = $wpdb->prefix.'srzfb_galleries';
		$q = $wpdb->prepare("SELECT * FROM $table WHERE id = %d",$id);
		$album = $wpdb->get_row($q);
		$ret = array();
		$ret['id'] = $id;
		$ret['title'] = $album->title;
		$ret['pageid'] = $album->pageid;
		$options = unserialize($album->options);
		foreach($options as $key=>$value){
			$ret[$key] = $value;
		}
		return $ret;
	}
	
	static function GetAllAlbums(){
		global $wpdb;
		$table = $wpdb->prefix.'srzfb_albums';
		$albums = $wpdb->get_results( "SELECT id, title FROM $table" );
		return $albums;
	}
	
	static function GetAllGalleries(){
		global $wpdb;
		$table = $wpdb->prefix.'srzfb_galleries';
		$albums = $wpdb->get_results( "SELECT id, title FROM $table" );
		return $albums;
	}
	
	static function DeleteAlbum($id){
		global $wpdb;
		$table = $wpdb->prefix.'srzfb_albums';
		$q = $wpdb->prepare("delete from $table where id = %d",$id);
		$wpdb->query($q);
	}
	
	static function SyncAlbum($id){
		$album = SrizonFBDB::GetAlbum($id);
		$ids = SrizonFBDB::srz_fb_extract_ids($album['albumid']);
		foreach($ids as $albumid){
			delete_transient(md5($albumid));
		}
	}
	
	static function SyncGallery($id){
		$page = SrizonFBDB::GetGallery($id);
		$contents = get_transient(md5($page['pageid']));
		$json = json_decode($contents);
		if(is_array($json->data)){
			foreach( $json->data as $obj ){
				delete_transient(md5($obj->id));
			}
		}
		delete_transient(md5($page['pageid']));
	}
	
	static function srz_fb_extract_ids($lines){
		$lines = str_replace(' ', "\n", $lines);
		$lines_arr = explode("\n",$lines);
		$id_arr = array();
		foreach($lines_arr as $line){
			if(strlen(trim($line))<5) continue;
			if(strpos($line, 'set=a.')){
				$line = substr($line, strpos($line, 'set=a.')+6);
				$line = substr($line, 0, strpos($line, '.'));
			}
			$id_arr[] = trim($line);
		}
		if(isset($_GET['debugjfb'])){
			echo 'Dumping IDs<pre>';
			print_r($id_arr);
			echo '</pre>';
		}
		return $id_arr;
	}
	
	static function DeleteGallery($id){
		global $wpdb;
		$table = $wpdb->prefix.'srzfb_galleries';
		$q = $wpdb->prepare("delete from $table where id = %d", $id);
		$wpdb->query($q);
	}
	
	static function CreateDBTables(){
		global $wpdb;
		$t_albums = $wpdb->prefix.'srzfb_albums';
		$t_galleries = $wpdb->prefix.'srzfb_galleries';
		$sql = '
CREATE TABLE '.$t_albums.' (
  id int(11) NOT NULL AUTO_INCREMENT,
  title text CHARACTER SET utf8,
  albumid text CHARACTER SET utf8,
  options text CHARACTER SET utf8,
  PRIMARY KEY (id)
);
CREATE TABLE '.$t_galleries.' (
  id int(11) NOT NULL AUTO_INCREMENT,
  title text CHARACTER SET utf8,
  pageid varchar(512) DEFAULT NULL,
  options text CHARACTER SET utf8,
  PRIMARY KEY (id)
);	
';
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	static function DeleteDBTables(){
		global $wpdb;
		$t_albums = $wpdb->prefix.'srzfb_albums';
		$t_galleries = $wpdb->prefix.'srzfb_galleries';
		$sql = 'drop table '.$t_albums.', '.$t_galleries.';';
		$wpdb->query($sql);
	}
}
