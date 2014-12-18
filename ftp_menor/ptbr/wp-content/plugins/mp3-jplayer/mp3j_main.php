<?php

/**	Main functions */
if ( !class_exists("MP3j_Main") )
{
	class MP3j_Main
	{
	// ------------------------- Update Me 
		var $version_of_plugin = "1.7.3"; 
	// -----------------------------------
		var $WPinstallpath;
		var $Rooturl;
		var $mp3jPath;
		var $adminOptionsName = "mp3FoxAdminOptions";
		var $textdomain = "mp3-jplayer";
		var $newCSScustom = "/wp-content/plugins/mp3-jplayer/css/player-silverALT.css";
		var $theSettings = array();
		var $Colours = array();
		var $stylesheet = "";
		//
		var $iscompat = false;
		var $scriptsflag = "false";
		var $excerptCalled = false;
		var $JPdiv = false;
		var $external_call = false;
		var $tag_call = false;
		//	
		var $postMetaKeys = array();
		var $postMetaValues = array();
		var $feedKeys = array();
		var $feedValues = array();
		var $mp3LibraryWP = array();
		var $mp3LibraryI = array();
		var $PlayerPlaylist = array( 'artists' => array(), 'titles' => array(), 'files' => array(), 'order' => array(), 'count' => 0 );
		var $InlinePlaylist = array( 'artists' => array(), 'titles' => array(), 'files' => array(), 'order' => array(), 'count' => 0 );
		var $NewPlaylist;
		//
		var $jsInfo = array();
		var $jsFields = array();
		var $Footerjs = "";
		//
		var $debugCount = "0";
		var $debug_string;
		var $Player_ID = 0;
		var $NewList_num = 0;
		var $FieldsList_num = 0;
		var $has_fields = false;
		var $putTag_runCount = 0;	
		var $single_autocount = 0;
		var $activeWidgets = array();
		var $activeWidgetSettings;
		
		function MP3j_Main () { //constructor
			$this->WPinstallpath = get_bloginfo('wpurl');
			$this->mp3jPath = dirname(__FILE__);
			$this->theSettings = $this->getAdminOptions();
			$Srooturl = $_SERVER['HTTP_HOST'];
			$this->Rooturl = str_replace("www.", "", $Srooturl);
		}
	
	
	/**
	*	Returns updated compatible options    
	*/
		function getAdminOptions() {
			
			$colour_keys = array( // init colour keys
							'screen_colour' => '',
							'screen_opacity' => '',
							'loadbar_colour' => '',
							'loadbar_opacity' => '',
							'posbar_colour' => '',
							'posbar_opacity' => '',
							'posbar_tint' => '',
							'playlist_colour' => '',
							'playlist_opacity' => '',
							'playlist_tint' => '',
							'list_divider' => '',
							'screen_text_colour' => '', 
							'list_text_colour' => '',
							'list_current_colour' => '',
							'list_hover_colour' => '',
							'listBGa_current' => '',
							'listBGa_hover' => '',
							'indicator' => '',
							'volume_grad' => '' );
			
			$theOptions = get_option($this->adminOptionsName);
			if ( !empty($theOptions) ) { // run backwards compatibility
				// styles
				$xfer = $this->transfer_old_colours( $theOptions['player_theme'], $colour_keys, $theOptions['custom_stylesheet'] ); 
				if ( $xfer[0] ) {
					$theOptions['player_theme'] = $xfer[0];
					$colour_keys = $xfer[1];
					 $theOptions['custom_stylesheet'] = $xfer[2];
				}
			}
			
			$mp3FoxAdminOptions = array( // default settings
							'initial_vol' => '100',
							'auto_play' => 'false',
							'mp3_dir' => '/',
							'player_theme' => 'styleF',
							'allow_remoteMp3' => 'true',
							'playlist_AtoZ' => 'false',
							'player_float' => 'none',
							'player_onblog' => 'true',
							'playlist_UseLibrary' => 'false',
							'playlist_show' => 'true',
							'remember_settings' => 'true',
							'hide_mp3extension' => 'false',
							'show_downloadmp3' => 'false',
							'disable_template_tag' => 'false',
							'db_plugin_version' => $this->version_of_plugin,
							'custom_stylesheet' => $this->newCSScustom,
							'echo_debug' => 'false',
							'add_track_numbering' => 'true',
							'enable_popout' => 'true',
							'playlist_repeat' => 'false',
							'player_width' => '40%',
							'popout_background' => '',
							'popout_background_image' => '',
							'colour_settings' => $colour_keys,
							'use_fixed_css' => 'false',
							'paddings_top' => '5px',
							'paddings_bottom' => '40px',
							'paddings_inner' => '35px',
							'popout_max_height' => '600',
							'popout_width' => '400',
							'popout_button_title' => '',
							'max_list_height' => '',
							'encode_files' => 'true',
							'animate_sliders' => 'false' );
							
							// DEPRECIATED
							//'force_scripts_from_admin' => 'false',
							//'give_shortcode_priority' => 'true',
							//'use_small_player' => 'true',
							
			
			if ( !empty($theOptions) ) { // swap/add in the existing
				foreach ( $theOptions as $key => $option ){
					$mp3FoxAdminOptions[$key] = $option;
				}
				$mp3FoxAdminOptions['db_plugin_version'] = $this->version_of_plugin; // set last!
			}
			update_option($this->adminOptionsName, $mp3FoxAdminOptions);
			return $mp3FoxAdminOptions;
		}
		
		
	/**
	* 	translates colour style from old options 
	*	to the new format prior to saving them.
	*/
		function transfer_old_colours ( $s, $keys, $path = "" ) {
		
			$csspath = "/wp-content/plugins/mp3-jplayer/css/mp3jplayer-cyanALT.css"; // the prev custom css that preserved orig ALT style
			$path = ( $path == $csspath || $path == "" ) ? $this->newCSScustom : $path;
			
			if ( $s == "styleA" ) { // orig 'neutral'
				$keys['loadbar_colour'] = "#bababa";
				$keys['posbar_colour'] = "#a8a8a8";
				$keys['list_hover_colour'] = "#888";
				$keys['list_current_colour'] = "b8a47e";
				$s = "styleF";
			}
			elseif ( $s == "styleB" ) { // orig 'green'
				$keys['loadbar_colour'] = "#a3baa5";
				$keys['posbar_colour'] = "#73ed7b";
				$keys['list_hover_colour'] = "#888";
				$keys['list_current_colour'] = "b8a47e";
				$s = "styleF";
			}
			elseif ( $s == "styleC" ) { // orig 'blu'
				$keys['screen_colour'] = "#c0c0c0";
				$keys['loadbar_colour'] = "#90a0b7";
				$keys['posbar_colour'] = "#61a5ff";
				$keys['list_hover_colour'] = "#888";
				$keys['list_current_colour'] = "b8a47e";
				$s = "styleF";
			}
			elseif ( $s == "styleD" ) { // orig 'cyanALT', or custom css
				if ( $path == $newALTpath ) { 
					$keys['screen_colour'] = "#ededed";
					$keys['loadbar_colour'] = "#77ccff";
					$keys['posbar_colour'] = "#77ccff";
					$keys['screen_text_colour'] = "#4f9ad4";
					$keys['list_hover_colour'] = "#00c0f0";
					$keys['list_current_colour'] = "#77c0f0";
				}					
				$s = "styleI";
			}
			elseif ( $s == "styleE" ) { // orig 'text'
				$keys['loadbar_colour'] = "";
				$keys['posbar_colour'] = "";
				$keys['list_hover_colour'] = "";
				$s = "styleH";
			}
			else { 
				$s = false; 
			}
			return array( $s, $keys, $path );
		}


	/**
	*	Returns library mp3 filenames, titles, excerpts, content, uri's, id's 
	*	in indexed arrays.
	*/
		function grab_library_info() {		
			
			global $wpdb;
			$audioInLibrary = $wpdb->get_results("SELECT DISTINCT guid, post_title, post_excerpt, post_content, ID FROM $wpdb->posts WHERE post_mime_type = 'audio/mpeg'");
			//$Lcount = count($audioInLibrary);
			$this->mp3LibraryWP = $audioInLibrary;
			
			$j=0;
			foreach ( $audioInLibrary as $obj ) {
			
				if ( preg_match("!\.mp3$!i", $obj->guid) ) { // audio/mpeg has multiple file associations so grab just mp3's
					$Titles[$j] = $obj->post_title;
					$Excerpts[$j] = $obj->post_excerpt;
					$Descriptions[$j] = $obj->post_content;
					$PostIDs[$j] = $obj->ID;
					$URLs[$j] = $obj->guid;
					$Filenames[$j] = strrchr( $URLs[$j], "/");
					$Filenames[$j] = str_replace( "/", "", $Filenames[$j]);
					$j++;
				}
			
			}		
			if ( $Filenames ) { 
				natcasesort($Filenames); 
			}
			$Lcount = count($Filenames);
			$theLibrary = array(	'filenames' => $Filenames,
									'titles' => $Titles,
									'urls' => $URLs,
									'excerpts' => $Excerpts,
									'descriptions' => $Descriptions,
									'postIDs' => $PostIDs,
									'count' => $Lcount );
			$this->mp3LibraryI = $theLibrary;
			return $theLibrary;
		}


	/**
	*	Reads mp3's from a local directory.
	*	Returns array of their uri's.
	*/			
		function grab_local_folder_mp3s( $folder ) {
			$Srooturl = $_SERVER['HTTP_HOST'];
			$rooturl = str_replace("www.", "", $Srooturl);				
			$items = array();
			if ( ($lp = strpos($folder, $rooturl)) || preg_match("!^/!", $folder) ) {
				if ( $lp !== false ) {
					$folderpath = str_replace($rooturl, "", $folder);
					$folderpath =  str_replace("www.", "", $folderpath);
					$folderpath =  str_replace("http://", "", $folderpath);
				}
				else {
					$folderpath = $folder;
				}
				$path = $_SERVER['DOCUMENT_ROOT'] . $folderpath;
				if ($handle = @opendir($path)) {
					$j=0;
					while (false !== ($file = readdir($handle))) {
						if ( $file != '.' && $file != '..' && filetype($path.'/'.$file) == 'file' && preg_match("!\.mp3$!i", $file) ) {
							$items[$j++] = $file;
						}
					}
					closedir($handle);
					if ( ($c = count($items)) > 0 ) {
						natcasesort($items);
						$folderpath = preg_replace( "!/+$!", "", $folderpath );
						foreach ( $items as $i => $mp3 ) {
							$items[$i] = "http://" . $Srooturl . $folderpath . "/" . $mp3;
						}
					}
					$this->debug_string .= "\nRead folder: Done\nmp3's in folder: " . $c . "\nhttp://" . $Srooturl . $folderpath;
					return $items;
				}
				else {
					$this->debug_string .= "\nRead folder: Failed to open local folder, check path and permissions.\nhttp://" . $Srooturl . $folderpath;
					return true;
				}
			}
			else {
				$this->debug_string .= "\nRead folder: Folder path is either remote or unreadable, didn't attempt to read it.\n" . $folderpath;
				return false;
			}
		}


	/**
	* 	Gets custom field keys and values from a post or page. 
	*	Checks for any 'FEED:' values and adds the contents of them to the end of the meta arrays.
	*	Returns number of tracks.
	*/
		function grab_Custom_Meta( $id = "" ) {
			
			$this->postMetaKeys = array();
			$this->postMetaValues = array();
			global $wpdb;
			global $post;
			
			if ( $id == "" ) { $id = $post->ID;	}
			$pagesmeta = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id =" .$id. " AND meta_value!='' ORDER BY meta_key ASC");
			
			$i = 0;
			$metacount = 0;
			foreach ( $pagesmeta as $obj ) { 
				$flag = 0;
				foreach ( $obj as $k => $value ) {
					if ( $k == "meta_key" ){
						if ( preg_match('/^([0-9]+(\s)?)?mp3(\..*)?$/', $value) == 1 ) { // Grab keys from meta 
							$this->postMetaKeys[$i] = $value;
							$metacount++;
							$flag = 1;
						}
					}
					if ( $k == "meta_value" ){
						if ( $flag == 1 ) { // Grab values
							$this->postMetaValues[$i++] = $value; 
						}
					}
				}
			}
			
			foreach ( $this->postMetaValues as $i => $val ) { // Store any 'FEED' values/keys and delete them 
				if ( preg_match( "!^FEED:(DF|ID|LIB|/.*)$!i", $val ) == 1 ) {
					$sources[$i] = strstr( $val, ":" );
					$sources[$i] = str_replace( ":", "", $sources[$i] );
					$sourcekeys[$i] = $this->postMetaKeys[$i];
					unset( $this->postMetaValues[$i], $this->postMetaKeys[$i] );
					$metacount--;
				}	
			}		
			if ( !empty($sources) ) { // Add feeds to postMeta arrays 
				$this->extend_metakeys( $sources, $sourcekeys );
				$metacount = count($this->postMetaKeys);
			}
			$this->postMetaValues = array_values( $this->postMetaValues );
			$this->postMetaKeys = array_values( $this->postMetaKeys );	
			return $metacount;
		}
	
	
	/**
	* 	Stores any 'FEED' values/keys and deletes them 
	*/
		function collect_delete_jobs( $values, $keys ){
			
			$sources = array();
			$sourcekeys = array();
			foreach ( $values as $i => $val ) {  
				if ( preg_match( "!^FEED:(DF|ID|LIB|/.*)$!i", $val ) == 1 ) { // ID Depreciated, keep in for backwards compat
					$sources[$i] = strstr( $val, ":" );
					$sources[$i] = str_replace( ":", "", $sources[$i] );
					$sourcekeys[$i] = ( !empty($keys[$i]) ) ? $keys[$i] : "";
					unset( $values[$i], $keys[$i] );
				}	
			}
			return array( $values, $keys, $sources, $sourcekeys );
		}


	/**
	* 	Pushes new tracks onto postmeta arrays according 
	*	to FEED:LIB or FEED:/folder entries made by user
	*/
   		function extend_metakeys( $feeds, $keys ) {
			if ( empty($feeds) ) { return; }
			foreach ( $feeds as $i => $val )
			{
				if ( $val == "ID" ) { // Depreciated
					//do nothing since 1.5
				}
				elseif ( $val == "LIB" ) {
					$library = ( empty($this->mp3LibraryI) ) ? $this->grab_library_info() : $this->mp3LibraryI;
					if ( $library['count'] >= 1 ) {
						$counter = count($this->postMetaValues);
						foreach ( $library['filenames'] as $k => $fn ) {
							$captions[$k] = $library['excerpts'][$k];
						} 
						$this->new_feed_keys( $library['filenames'], $captions, ++$counter );
						foreach ( $this->feedKeys as $j => $x ) {
							array_push( $this->postMetaValues, $this->feedValues[$j] );
							array_push( $this->postMetaKeys, $x );
						}
					}
				}
				else { // a folder
					if ( $val == "DF" ) { 
						$val = $this->theSettings['mp3_dir'];
					}
					$tracks = $this->grab_local_folder_mp3s( $val ); 
					if ( $tracks !== true && $tracks !== false && count($tracks) > 0 ) {
						foreach ( $tracks as $k => $fn ) {
							$captions[$k] = $keys[$i];
						}
						$counter = count($this->postMetaValues);
						$this->new_feed_keys( $tracks, $captions, ++$counter );
						foreach ( $this->feedKeys as $j => $x ) {
							array_push( $this->postMetaValues, $this->feedValues[$j] );
							array_push( $this->postMetaKeys, $x );
						}
					}
				}
			}
			return;
		}


	/**
	*	Creates new meta arrays.
	*	$startnum is the track number offset to use when numbering the metakeys 
	*/			
		function new_feed_keys( $tracks, $captions = "", $startnum = 1 ) {
			$this->feedKeys = array();
			$this->feedValues = array();
			if ( empty($tracks) || !is_array($tracks) ) { 
				return; 
			}
			$j = 1;
			if ( empty($captions) ) {
				foreach ( $tracks as $i => $file ) {
					$this->feedKeys[$i] = $startnum++ . " mp3";
					$this->feedValues[$i] = $file;
				}
			}
			else {
				foreach ( $tracks as $i => $file ) {
					if ( !empty($captions[$i]) ) {
						if ( preg_match('/^([0-9]+(\s)?)?mp3(\..*)?$/', $captions[$i]) == 1 ) {	
							$this->feedKeys[$i] = $captions[$i];
						}
						else { $this->feedKeys[$i] = $startnum++ . " mp3." . $captions[$i]; }
					}
					else { 
						$this->feedKeys[$i] = $startnum++ . " mp3"; 
					}
					$this->feedValues[$i] = $file;
				}
			}
			return;
		}
			
		
	/**
	*	Adds/makes playlist from comma separated lists of tracks/captions.
	*/			
		function string_pushto_playlist( $tracks, $captions = "", $plist = "" ) {
			
			$separator = ";"; // captions separator
			$caps = array();
			if ( $tracks == "" ) { return false; }
			
			$tracks = str_replace( array("</p>", "<p>", "<br />", "<br>"), "", $tracks );
			//$tracks = str_replace( array("</p>", "<p>"), "", $tracks );
			
			$shortlist = trim( $tracks );
			$shortlist = trim( $shortlist, "," );
			if ( !empty($shortlist) ) 
			{
				$names = explode( ",", $shortlist );
				foreach ( $names as $i => $file ) { 
					$names[$i] = trim($file); 
				}
				if ( $captions != "" ) { 
					$captions = str_replace( array("</p>", "<p>", "<br />"), "", $captions );
					$shortcaps = trim( $captions );
					//$shortcaps = trim( $shortcaps, "," );
					$shortcaps = trim( $shortcaps, $separator );
					if ( !empty($shortcaps) ) {
						//$caps = explode( ",", $shortcaps );
						$caps = explode( $separator, $shortcaps );
						foreach ( $caps as $i => $file ) { $caps[$i] = trim($file); }
					}
				}
				$woof = $this->collect_delete_jobs( $names, $caps ); 
				if ( !empty($this->postMetaValues) ) {
					$this->postMetaKeys = array();
					$this->postMetaValues = array();
				}
				$this->extend_metakeys( $woof[2], $woof[3] );
				$this->new_feed_keys( $woof[0], $woof[1] );
				foreach ( $this->feedKeys as $j => $x ) {
					array_push( $this->postMetaValues, $this->feedValues[$j] );
					array_push( $this->postMetaKeys, $x );
				}
				$this->postMetaValues = array_values($this->postMetaValues);
				$this->postMetaKeys = array_values($this->postMetaKeys);
				
				$theShortlist = $this->generate_playlist( $this->postMetaKeys, $this->postMetaValues, 1 );
				if ( $theShortlist['count'] > 0 ) { // add tracks to either playlist
					if ( $plist == "" ) {
						foreach ( $theShortlist['order'] as $i => $val ) {
							array_push( $this->PlayerPlaylist['order'], $i + $this->PlayerPlaylist['count'] );
							array_push( $this->PlayerPlaylist['artists'], $theShortlist['artists'][$i] );
							array_push( $this->PlayerPlaylist['titles'], $theShortlist['titles'][$i] );
							array_push( $this->PlayerPlaylist['files'], $theShortlist['files'][$i] );
						}
						$this->PlayerPlaylist['count'] += $theShortlist['count'];
					}
					elseif ( $plist == "1" ) {
						foreach ( $theShortlist['order'] as $i => $val ) {
							array_push( $this->InlinePlaylist['order'], $i + $this->InlinePlaylist['count'] );
							array_push( $this->InlinePlaylist['artists'], $theShortlist['artists'][$i] );
							array_push( $this->InlinePlaylist['titles'], $theShortlist['titles'][$i] );
							array_push( $this->InlinePlaylist['files'], $theShortlist['files'][$i] );
						}
						$this->InlinePlaylist['count'] += $theShortlist['count'];	
					}
					else {
						$this->NewPlaylist = $theShortlist;
					}	
				}
				else { return false; }
			}					
			return true;
		}
		
			
	/*	Not used since 1.6
	*	Pushes playlist from another page's fields onto current.
	*/			
		function id_pushto_playlist( $id ) {
			
			if ( $id == "" ) { return false; }
			$id = trim($id);
			if ( $this->grab_Custom_Meta($id) > 0 ) {
				$thePlayList = $this->generate_playlist( $this->postMetaKeys, $this->postMetaValues, 1 );
				if ( $thePlayList['count'] > 0 ) { // add tracks to current playlist
					foreach ( $thePlayList['order'] as $i => $val ) {
						array_push( $this->PlayerPlaylist['order'], $i + $this->PlayerPlaylist['count'] );
						array_push( $this->PlayerPlaylist['artists'], $thePlayList['artists'][$i] );
						array_push( $this->PlayerPlaylist['titles'], $thePlayList['titles'][$i] );
						array_push( $this->PlayerPlaylist['files'], $thePlayList['files'][$i] );
					}
					$this->PlayerPlaylist['count'] += $thePlayList['count'];
				}		
			}
			return true;
		}
		
		
	/**
	*	Puts playlist data through sorting/filtering routine 
	*	and returns a playlist ready to be written as js.
	*/	

		function generate_playlist( $customkeys, $customvalues, $method = 1 ) {
			
			if ( count($customkeys) == 0 ) { return; }
			
			$theSplitMeta = $this->splitup_meta( $customkeys, $customvalues );
			$theAssembledMeta = $this->compare_swap( $theSplitMeta, $customkeys, $customvalues );
			$theTrackLists = $this->sort_tracks( $theAssembledMeta, $customkeys );
			$thePlayList = $this->remove_mp3remote( $theTrackLists );
			return $thePlayList;
		}

	/**	
	* 	Splits up the custom keys/values into artists, titles, files  arrays. if there's 
	*	no title then uses the filename.
	*/
		function splitup_meta($customkeys, $customvalues) {		
			
			// Captions
			foreach ( $customkeys as $i => $ckvalue ) {
				$splitkey = explode('.', $ckvalue, 2);
				$customArtists[$i] = ( empty($splitkey[1]) ) ? "" : $splitkey[1];
			}
			// Titles & Filenames 
			foreach ( $customvalues as $i => $cvvalue ) {	
				$checkfortitle = strpos($cvvalue, '@');
				if ( $checkfortitle === false ) {
					$customTitles[$i] = preg_replace( '/\.mp3$/i', "", $cvvalue );
					$customFilenames[$i] = $cvvalue;
					if ( $this->theSettings['hide_mp3extension'] == "false" ) {
						$customTitles[$i] .= ".mp3";
					}
				}
				else {
					$reversevalue = strrev($cvvalue);
					$splitvalue = explode('@', $reversevalue, 2);
					$customTitles[$i] = strrev($splitvalue[1]);
					$customFilenames[$i] = strrev($splitvalue[0]);
				}
				
				if ( preg_match('/^www\./i', $customFilenames[$i]) ) {
					$customFilenames[$i] = str_replace("www.", "", $customFilenames[$i]);
					if ( strpos($customFilenames[$i], "http://") === false ) {
						$customFilenames[$i] = "http://" .$customFilenames[$i];
					}
				}
			}
			$theSplitMeta = array(	'artists' => $customArtists, 
									'titles' => $customTitles,
									'files' => $customFilenames );
			return $theSplitMeta;
		}
		
			
	/**	
	*	Returns prepared arrays ready for playlist. Looks for $customFilenames that exist in the library and grabs their full uri's, 
	*	otherwise adds default path or makes sure has an http when remote. Cleans up titles that are uri's, swaps titles and/or artists 
	*	for the library ones when required.
	*/
		function compare_swap($theSplitMeta, $customkeys, $customvalues) {
			
			$library = ( empty($this->mp3LibraryI) ) ? $this->grab_library_info() : $this->mp3LibraryI;
			foreach ( $theSplitMeta['files'] as $i => $cfvalue ) 
			{
				$inLibraryID = ( $library['count'] == 0 ) ? false : array_search( $cfvalue, $library['filenames'] );
				$mp3haswww = strpos($cfvalue, 'http://');
				
				if ( $mp3haswww === false && $inLibraryID === false ) { // File is presumed default folder 
					$theSplitMeta['files'][$i] = ( $this->theSettings['mp3_dir'] == "/" ) ? $this->theSettings['mp3_dir'] . $theSplitMeta['files'][$i] :  $this->theSettings['mp3_dir'] . "/" . $theSplitMeta['files'][$i];
				}
				if ( $inLibraryID !== false ) { // File is in library
					$theSplitMeta['files'][$i] = $library['urls'][$inLibraryID];
					if ( $this->theSettings['playlist_UseLibrary'] == "true" ) { // Always use titles and captions
						$theSplitMeta['titles'][$i] = $library['titles'][$inLibraryID];
						$theSplitMeta['artists'][$i] = $library['excerpts'][$inLibraryID];
					}					
					else { // prioritise meta titles and captions
						if ( preg_match('/^([0-9]+(\s)?)?mp3$/', $customkeys[$i]) == 1 ) {
							$theSplitMeta['artists'][$i] = $library['excerpts'][$inLibraryID];
						}
						if ( preg_match('/^([0-9]+(\s)?)?mp3\.$/', $customkeys[$i]) == 1 ) {
							$theSplitMeta['artists'][$i] = "";
						}
						if ( strpos($customvalues[$i], '@') === false ) {
							$theSplitMeta['titles'][$i] = $library['titles'][$inLibraryID];
						}
					}
				}
				if ( $mp3haswww !== false && $inLibraryID === false ) { // File is remote or user is over-riding default path
					if ( strpos($theSplitMeta['titles'][$i], 'http://') !== false || strpos($theSplitMeta['titles'][$i], 'www.') !== false ) {
						$theSplitMeta['titles'][$i] = strrchr($theSplitMeta['titles'][$i], "/");
						$theSplitMeta['titles'][$i] = str_replace( "/", "", $theSplitMeta['titles'][$i]);
					}
				}
			}
			
			$theAssembledMeta = array(	'artists' => $theSplitMeta['artists'], 
										'titles' => $theSplitMeta['titles'],
										'files' => $theSplitMeta['files'] );
			return $theAssembledMeta;
		}
		
			
	/**	
	*	Sorts tracks by either the titles (if a-z ticked) or by the keys (only if there's
	*	any numbering in them) and adds an ordering array
	*/
		function sort_tracks($theAssembledMeta, $customkeys) {		
			
			$x = 0;
			if ( $this->theSettings['playlist_AtoZ'] == "true" ) {
				natcasesort($theAssembledMeta['titles']);
				foreach ($theAssembledMeta['titles'] as $kt => $vt) {
					$indexorder[$x++] = $kt;
				} 
			}
			else {
				$numberingexists = 0;
				foreach ( $customkeys as $ki => $val ) {
					if ( preg_match('/^[0-9]/', $val) ) {
						$numberingexists++;
						break;
					}
				}
				if ( $numberingexists > 0 ) {
					natcasesort($customkeys);
					foreach ( $customkeys as $kf => $vf ) {
						$indexorder[$x++] = $kf;
					}
				}
				else {
					foreach ( $theAssembledMeta['titles'] as $kt => $vt ) {
						$indexorder[$x++] = $kt;
					}
				} 
			}
			
			$theTrackLists = array(	'artists' => $theAssembledMeta['artists'], 
									'titles' => $theAssembledMeta['titles'],
									'files' => $theAssembledMeta['files'],
									'order' => $indexorder );
			return $theTrackLists;
		}
		
			
	/**
	*	Removes remote tracks from the playlist arrays if 'allow remote' is unticked. 
	*/
		function remove_mp3remote( $theTrackLists ) {	
			
			if ( $this->theSettings['allow_remoteMp3'] == "false" ) {
				
				foreach ( $theTrackLists['order'] as $ik => $i ) {
					if ( strpos($theTrackLists['files'][$i], $this->Rooturl) !== false || strpos($theTrackLists['files'][$i], "http://") === false || (strpos($this->theSettings['mp3_dir'], "http://") !== false && strpos($theTrackLists['files'][$i], $this->theSettings['mp3_dir']) !== false) ) {
						$playlistFilenames[$i] = $theTrackLists['files'][$i];
						$playlistTitles[$i] = $theTrackLists['titles'][$i];
						$playlistArtists[$i] = $theTrackLists['artists'][$i];
						$indexorderAllowed[$x++] = $i;
					}
				}
			}
			else {
				$playlistFilenames = $theTrackLists['files'];
				$playlistTitles = $theTrackLists['titles'];
				$playlistArtists = $theTrackLists['artists'];
				$indexorderAllowed = $theTrackLists['order'];
			}
			$playlistTitles = str_replace('"', '\"', $playlistTitles); // Escapes quotes for the js array
			$nAllowed = count($playlistFilenames);
			
			$thePlayList = array(	'artists' => $playlistArtists, 
									'titles' => $playlistTitles,
									'files' => $playlistFilenames,
									'order' => $indexorderAllowed,
									'count' => $nAllowed );
			return $thePlayList;
		} 
		
		
	/**
	*	Picks a random selection of x tracks from the playlist
	*	while preserving track running order
	*/
		function take_playlist_slice( $slicesize, $plist ) {
			
			if ( ($n = $plist['count']) < 1 ) { return; }
			$slicesize = trim($slicesize);
			if ( !empty($slicesize) && $slicesize >= 1 ) {
				if ( $n > 1 ) {
					if ( $slicesize > $n ) { $slicesize = $n; }
					$picklist = array();
					for ( $i = 0; $i < $n; $i++ ) { // make a numbers array
						$picklist[$i] = $i;
					} 
					shuffle( $picklist );
					$picklist = array_slice( $picklist, 0, $slicesize ); // take a shuffled slice
					natsort( $picklist ); // reorder it 
					$j=0;
					foreach ( $picklist as $i => $num ) { // use it to pick the random tracks
						$Ptitles[$j] = $plist['titles'][$num];
						$Partists[$j] = $plist['artists'][$num];
						$Pfiles[$j] = $plist['files'][$num];
						$Porder[$j] = $j;
						$j++;
					}
					$thePlayList = array(	'artists' => $Partists, 
											'titles' => $Ptitles,
											'files' => $Pfiles,
											'order' => $Porder,
											'count' => $j );
					return $thePlayList;
				}
			}
		}

		
	/**
	* 	Looks for any active widget that isn't ruled out by the page filter.
	*	Returns true if finds a widget that will be building.
	*/		
		function has_allowed_widget( $widgetname ) {
			
			$activeplayerwidgets = array();
			$needScripts = false;
			$name = "sidebars_widgets";
			$sidebarsettings = get_option($name);
			$n = 0;
			
			if ( empty($sidebarsettings) || is_null($sidebarsettings) ) {
				return false;
			}
			foreach ( $sidebarsettings as $key => $arr ) { 
				if ( is_array($arr) && $key != "wp_inactive_widgets" ) {
					foreach ( $arr as $i => $widget ) {
						if ( strchr($widget, $widgetname) ) {
							$activeplayerwidgets[$n++] = $widget;
						} 
					}
				}
			}
			$this->activeWidgets[] = $activeplayerwidgets; // Debug 
			
			if ( !empty($activeplayerwidgets) ) { 
				$name = "widget_". $widgetname;
				$widgetoptions = get_option($name);
				foreach ( $activeplayerwidgets as $i => $playerwidget ) {
					$widgetID = strrchr( $playerwidget, "-" );
					$widgetID = str_replace( "-", "", $widgetID );
					foreach ( $widgetoptions as $j => $arr ) {
						if ( $j == $widgetID ) { //echo "\n<!-- checking: mode ". $arr['widget_mode'] ."-->\n";
							if ( !$this->page_filter($arr['restrict_list'], $arr['restrict_mode']) ) {
								$needScripts = true;
								break 2;
							}
						}	
					}
				}
			}
			return $needScripts;
		}

		
	/**
	*	Builds mode-3 widget playlist
	*/		
		function make_widget_playlist( $instance ) {
							
			if ( $instance['widget_mode'] == "3" ) { // Play ID/Library/Folder slice
				
				// Grab meta from ID
				$customvalues = array();
				$customkeys = array();
				if ( !empty($instance['id_to_play']) && $instance['play_page'] == "true" ) {
					$id = trim($instance['id_to_play']);
					if ( $this->grab_Custom_Meta($id) > 0 ) {
						$customvalues = $this->postMetaValues;
						$customkeys = $this->postMetaKeys;
					}
				}
				// Add library
				$customvaluesB = array();
				$customkeysB = array();
				if ( $instance['play_library'] == "true" ) {
					$library = ( empty($this->mp3LibraryI) ) ? $this->grab_library_info() : $this->mp3LibraryI;
					if ( $library['count'] >= 1 ) {
						$counter = count($customvalues);
						$this->new_feed_keys( $library['filenames'], $library['excerpts'], ++$counter );
						$customvaluesB = $this->feedValues;
						$customkeysB = $this->feedKeys;
					}
				}
				foreach ( $customkeysB as $i => $v ) {
					array_push( $customvalues, $customvaluesB[$i] );
					array_push( $customkeys, $v );
				}
				// Add a local folder
				$customvaluesC = array();
				$customkeysC = array();
				if ( $instance['play_folder'] == "true" ) {
					$folder = ( $instance['folder_to_play'] == "" ) ? $this->theSettings['mp3_dir'] : $instance['folder_to_play'];
					$tracks = $this->grab_local_folder_mp3s( $folder );
					if ( $tracks !== true && $tracks !== false && count($tracks) > 0 ) {
						$counter = count($customvalues);
						$this->new_feed_keys( $tracks, '', ++$counter );
						$customvaluesC = $this->feedValues;
						$customkeysC = $this->feedKeys;
						foreach ( $customkeysC as $i => $v ) {
							array_push( $customvalues, $customvaluesC[$i] );
							array_push( $customkeys, $v );
						}
					}
				}
				if ( count($customvalues) < 1 ) { return false; }
			}
			
			// Make the playlist
			$thePlayList = $this->generate_playlist( $customkeys, $customvalues, 1 );
			if ( $thePlayList['count'] < 1 ) { 
				return false; 
			}
			return $thePlayList;
		}
		
		
	/**
	*	Checks current page against widget page-filter settings.
	*	returns true if widget should be filtered out.
	*/	
		function page_filter( $list, $mode ) {
			
			$f = false;
			if ( !empty($list) ) {
				$pagelist = explode( ",", $list );
				if ( !empty($pagelist) ) {
					foreach ( $pagelist as $i => $id ) { 
						$pagelist[$i] = str_replace( " ", "", $id ); 
					}
				}
				if ( !is_singular() ) { // Look for 'index' or 'archive' or 'search'
					if ( $mode == "include" ) {
						if ( is_home() ) {
							if ( strpos($list, "index") === false ) { $f = true; }
						}
						if ( is_archive() ) {
							if ( strpos($list, "archive") === false ) { $f = true; }
						}
						if ( is_search() ) {
							if ( strpos($list, "search") === false ) { $f = true; }
						}
					}
					if ( $mode == "exclude" ) {
						if ( is_home() ) {
							if ( strpos($list, "index") !== false ) { $f = true; }
						}
						if ( is_archive() ) {
							if ( strpos($list, "archive") !== false ) { $f = true; }
						}
						if ( is_search() ) {
							if ( strpos($list, "search") !== false ) { $f = true; }
						}
					}
				} else { // Check the id's against current page
					global $post;
					$thisID = $post->ID;
					if ( $mode == "include" ) {
						$f = true;
						foreach ( $pagelist as $i => $id ) {
							if ( $id == $thisID ) { $f = false; }
						}
					}
					if ( $mode == "exclude" ) {
						foreach ( $pagelist as $i => $id ) {
							if ( $id == $thisID ) { $f = true; }
						}
					}
				}
			}
			return $f;
		}		
	
			
	/**
	* 	Checks whether current post ID content contains a shortcode.
	*/
		function has_shortcodes ( $shtype = "" ) { 
			
			global $wpdb;
			global $post;
			if ( empty($post->ID) ) { return false; }
			
			$content = $wpdb->get_results("SELECT post_content FROM $wpdb->posts WHERE ID=" . $post->ID );
			$con = $content[0]->post_content;
			if ( $shtype != "" ) { // check for it
				if ( strpos($con, $shtype) !== false ) { return true; }
			}
			else { // check for all
				if ( strpos($con, "[mp3-jplayer") !== false || strpos($con, "[mp3j") !== false || strpos($con, "[mp3t") !== false || strpos($con, "[mp3-album") !== false ) {
					return true;
				}
			}
			return false;
		}

	
	/**
	* 	Enqueues js and css.
	*/
		function add_Scripts( $theme ) {
			
			$version = substr( get_bloginfo('version'), 0, 3);
			
			// jQuery and jQueryUI
			if ( $version >= 3.1 ) {
				wp_enqueue_script( 'jquery-ui-slider', '/wp-content/plugins/mp3-jplayer/js/ui.slider.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse' ), '1.8.10' );
			}
			else { // pre WP 3.1
				wp_enqueue_script( 'jquery-ui-widget', '/wp-content/plugins/mp3-jplayer/js/ui.widget.js', array( 'jquery', 'jquery-ui-core' ), '1.8.10' );
				wp_enqueue_script( 'jquery-ui-mouse', '/wp-content/plugins/mp3-jplayer/js/ui.mouse.js', false, '1.8.10' );
				wp_enqueue_script( 'jquery-ui-slider', '/wp-content/plugins/mp3-jplayer/js/ui.slider.js', false, '1.8.10' );
			}
			
			// jPlayer and Plugin js
			wp_enqueue_script( 'jquery.jplayer.min', '/wp-content/plugins/mp3-jplayer/js/jquery.jplayer.min.js', false, '1.2.0' );	
			wp_enqueue_script( 'mp3j-functions', '/wp-content/plugins/mp3-jplayer/js/mp3j-functions.js', false, '1.7.3' );
			wp_enqueue_script( 'mp3-jplayer', '/wp-content/plugins/mp3-jplayer/js/mp3-jplayer.js', false, '1.7.3' );
			
			// css
			if ( $theme == "styleF" ) { $themepath = "/wp-content/plugins/mp3-jplayer/css/player-silver.css"; }
			elseif ( $theme == "styleG" ) { $themepath = "/wp-content/plugins/mp3-jplayer/css/player-darkgrey.css"; }
			elseif ( $theme == "styleH" ) { $themepath = "/wp-content/plugins/mp3-jplayer/css/player-text.css"; }
			elseif ( $theme == "styleI" ) {	$themepath = ( $this->theSettings['custom_stylesheet'] == "/" ) ? $this->newCSScustom : $this->theSettings['custom_stylesheet']; }
			else { $themepath = $theme; }
			
			$name = strrchr( $themepath, "/");
			$name = str_replace( "/", "", $name);
			$name = str_replace( ".css", "", $name);
			wp_enqueue_style( $name, $themepath );
			
			// Write popout css and js vars to header
			$PPcsslink = ( strpos($themepath, "http://") === false ) ? $this->WPinstallpath . $themepath : $themepath;
			$this->write_user_style( $PPcsslink, $theme );
			return;
		}
	
	
	/**
	* 	Writes user colour settings
	*/
		function write_user_style( $PPcsslink, $theme ) {
			
			$settings = $this->theSettings;
			if ( $settings['use_fixed_css'] == "false" )
			{ 	
				$pluginpath = $this->WPinstallpath . "/wp-content/plugins/mp3-jplayer/";
				//$colours = $this->set_colours( $settings['colour_settings'], $settings['player_theme'], $pluginpath );
				//$colours = $this->set_colours( $settings['colour_settings'], $this->stylesheet, $pluginpath );
				$colours = $this->set_colours( $settings['colour_settings'], $theme, $pluginpath );
				
				$screen_opac = "; opacity:" . $colours['screen_opacity']*0.01 . "; filter:alpha(opacity=" . $colours['screen_opacity'] . ")";
				$loaderbar_opac = "; opacity:" . $colours['loadbar_opacity']*0.01 . "; filter:alpha(opacity=" . $colours['loadbar_opacity'] . ")";
				$posbar_opac = "; opacity:" . $colours['posbar_opacity']*0.01 . "; filter:alpha(opacity=" . $colours['posbar_opacity'] . ")";
				$playlist_opac = "; opacity:" . $colours['playlist_opacity']*0.01 . "; filter:alpha(opacity=" . $colours['playlist_opacity'] . ")";
				
				switch( $colours['posbar_tint'] ) {
					case "soften": $posbar_tint = " url('" . $pluginpath . "css/images/posbar-soften-2.png') repeat-y right top"; break;
					case "softenT":	$posbar_tint = " url('" . $pluginpath . "css/images/posbar-soften-tipped-2.png') repeat-y right top"; break;
					case "darken": $posbar_tint = " url('" . $pluginpath . "css/images/posbar-darken2-2.png') repeat-y right top"; break;
					case "none": $posbar_tint = "";
				}
				switch( $colours['playlist_tint'] ) {
					case "lighten1": $playlist_img = " url('" . $pluginpath . "css/images/pl-lighten1.png') repeat-x left 0px";	break;
					case "lighten2": $playlist_img = " url('" . $pluginpath . "css/images/pl-lighten2.png') repeat-x left 0px";	break;
					case "darken1": $playlist_img = " url('" . $pluginpath . "css/images/pl-gradlong10g.png') repeat-x left -130px"; break;
					case "darken2": $playlist_img = " url('" . $pluginpath . "css/images/pl-darken1.png') repeat-x left 0px"; break;
					case "none": $playlist_img = "transparent";
				}
				switch( $colours['list_divider'] ) {
					case "light": $playlist_divider = "transparent url('" . $pluginpath . "css/images/t60w.png') repeat-x left bottom";	break;
					case "med": $playlist_divider = "transparent url('" . $pluginpath . "css/images/t75e.png') repeat-x left bottom"; break;
					case "dark": $playlist_divider = "transparent url('" . $pluginpath . "css/images/t50g.png') repeat-x left bottom"; break;
					case "none": $playlist_divider = "transparent; background-image:none";
				}
				
				$listBGa = "none";
				$vol_grad = ( $colours['volume_grad'] == "light" ) ? "transparent url('" . $pluginpath . "css/images/vol-grad60w2.png') repeat-y -15px top" : "transparent url('" . $pluginpath . "css/images/vol-grad60b2.png') repeat-y 0px top";
				$opac = ( $colours['indicator'] == "tint" ) ? "35" : "100";
				$indicator = ( $colours['indicator'] == "tint" ) ? "#ccc" : $colours['posbar_colour'];
				$gif_opac = "opacity:" . $opac*0.01 . "; filter:alpha(opacity=" . $opac . ")";
								
				echo "\n\n<style type=\"text/css\">
	div.player-track-title, div.player-artist, div.jp-play-time, div.jp-total-time, div.statusMI { color:" . $colours['screen_text_colour'] . "; } div.loadMI_mp3j, span.loadB_mp3j, span.load_mp3j { background:" . $colours['loadbar_colour'] . $loaderbar_opac . "; } div.bars_holder .ui-widget-header { background:" . $colours['posbar_colour'] . $posbar_tint . $posbar_opac ."; } div.MIsliderVolume .ui-widget-header, div.vol_mp3j .ui-widget-header { background:" . $vol_grad . "; } div.innertab { background:" . $colours['screen_colour'] . $screen_opac . "; } div.playlist-colour { background:" . $colours['playlist_colour'] . $playlist_opac . "; }
	span.mp3-tint { background:" . $indicator . "; } span.mp3-finding, span.mp3-loading { " . $gif_opac . "; } ul.UL-MI_mp3j { background:" . $playlist_img . " !important; } ul.UL-MI_mp3j li { background:" . $playlist_divider . " !important; } ul.UL_mp3j li a, ul.UL-MI_mp3j li a { background-image:none !important; color:" . $colours['list_text_colour'] . " !important; } ul.UL-MI_mp3j li a:hover { background-image:none !important; color:" . $colours['list_hover_colour'] . " !important; background:" . $colours['listBGa_hover'] . " !important; } ul.UL-MI_mp3j li a.mp3j_A_current { background-image:none !important; color:" . $colours['list_current_colour'] . " !important; background:" . $colours['listBGa_current'] . " !important; } ul.UL_mp3j li a.mp3j_A_current { color:" . $colours['list_current_colour'] . " !important; }
	div.img_mp3j a:hover img { border-color:" . $colours['list_current_colour'] . "; } span.mp3j-link-play, span.textbutton_mp3j:hover, div.transport-MI div { color:" . $colours['list_hover_colour'] . "; } span.mp3j-link-play:hover, span.textbutton_mp3j, div.transport-MI div:hover { color:" . $colours['list_current_colour'] . "; }
</style>";
				
				if ( $settings['enable_popout'] == "true" ) {
					echo "\n<script type=\"text/javascript\">\n<!--
	var foxPP_indicator_tint = \"" . $indicator . "\"; var foxPP_screentext = \"" . $colours['screen_text_colour'] . "\"; var foxPP_playlist_img = \"" . $playlist_img . "\"; var foxPP_playlist_colour = \"" . $colours['playlist_colour'] . "\"; var foxPP_playlist_opac = \"" . $colours['playlist_opacity']*0.01 . "\"; var foxPP_playlist_text = \"" . $colours['list_text_colour'] . "\"; var foxPP_playlist_current = \"" . $colours['list_current_colour'] . "\"; var foxPP_playlist_hover = \"" . $colours['list_hover_colour'] . "\"; var foxPP_playlist_divider = \"" . $playlist_divider . "\";
	var foxPP_volume_grad = \"" . $vol_grad . "\"; var foxPP_screen_background = \"" . $colours['screen_colour'] . "\"; var foxPP_screen_opac = \"" . $colours['screen_opacity']*0.01 . "\"; var foxPP_loader_bar_colour = \"" . $colours['loadbar_colour'] . "\"; var foxPP_loader_bar_opac = \"" . $colours['loadbar_opacity']*0.01 . "\"; var foxPP_posbar_colour = \"" . $colours['posbar_colour'] . $posbar_tint . "\"; var foxPP_posbar_opac = \"" . $colours['posbar_opacity']*0.01 . "\"; var foxPP_listBGa_hover = \"" . $colours['listBGa_hover'] . "\"; var foxPP_listBGa_current = \"" . $colours['listBGa_current'] . "\";
//-->\n</script>";	
				}
			}// End if not fixed_css
			
			if ( $settings['enable_popout'] == "true" ) {
				$popout_bg = ( $settings['popout_background'] == "" ) ? "#fff" : $settings['popout_background'];
				echo "\n<script type=\"text/javascript\">\n<!--
	var popup_width = " . $settings['popout_width'] . "; var silence_mp3 = \"" . $pluginpath . "mp3/silence.mp3\"; var foxPP_bodycolour = \"" . $popout_bg . "\"; var foxPP_bodyimg = \"" . $settings['popout_background_image'] . "\"; var foxPP_stylesheet = \"" . $PPcsslink . "\"; var foxPP_fixedcss = \"" . $settings['use_fixed_css'] . "\"; var popup_maxheight = \"" . $settings['popout_max_height'] . "\";
//-->\n</script>\n\n";
			}
			
			return;
		}		
		
			
	/**
	* 	Sets up the colours array prior to writing
	*	according to style / user colours / defaults.
	*/
		function set_colours( $current, $style, $pluginpath ) {
		
			$silver = array( // defaults
							'screen_colour' => '#a7a7a7', 'screen_opacity' => '35',
							'loadbar_colour' => '#34A2D9', 'loadbar_opacity' => '70',
							'posbar_colour' => '#5CC9FF', 'posbar_opacity' => '80', 'posbar_tint' => 'softenT',
							'playlist_colour' => '#f1f1f1', 'playlist_opacity' => '100', 'playlist_tint' => 'darken1', 'list_divider' => 'med',
							'screen_text_colour' => '#525252', 
							'list_text_colour' => '#525252', 'list_current_colour' => '#47ACDE', 'list_hover_colour' => '#768D99',
							'listBGa_current' => '#f4f4f4', 'listBGa_hover' => '#f7f7f7',
							'indicator' => 'colour',
							'volume_grad' => 'light' );
		
			$darkgrey = array( // defaults
							'screen_colour' => '#333', 'screen_opacity' => '15',
							'loadbar_colour' => '#34A2D9', 'loadbar_opacity' => '70',
							'posbar_colour' => '#5CC9FF', 'posbar_opacity' => '100', 'posbar_tint' => 'darken',
							'playlist_colour' => '#fafafa', 'playlist_opacity' => '100', 'playlist_tint' => 'darken2', 'list_divider' => 'none',
							'screen_text_colour' => '#525252', 
							'list_text_colour' => '#525252', 'list_current_colour' => '#34A2D9', 'list_hover_colour' => '#768D99',
							'listBGa_current' => "transparent url('" . $pluginpath . "css/images/t40w.png') repeat", 'listBGa_hover' => "transparent url('" . $pluginpath . "css/images/t30w.png') repeat",
							'indicator' => 'colour',
							'volume_grad' => 'dark' );
			
			$text = array( // defaults
							'screen_colour' => 'transparent', 'screen_opacity' => '100',
							'loadbar_colour' => '#aaa', 'loadbar_opacity' => '20',
							'posbar_colour' => '#fff', 'posbar_opacity' => '58', 'posbar_tint' => 'none',
							'playlist_colour' => '#f6f6f6', 'playlist_opacity' => '100', 'playlist_tint' => 'lighten2', 'list_divider' => 'none',
							'screen_text_colour' => '#869399',
							'list_text_colour' => '#777', 'list_current_colour' => '#47ACDE', 'list_hover_colour' => '#829FAD',
							'listBGa_current' => 'transparent', 'listBGa_hover' => 'transparent',
							'indicator' => 'tint',
							'volume_grad' => 'dark' );
			
			switch( $style ) {
				case "styleG": $colours = $darkgrey; break;
				case "styleH": $colours = $text; break;
				default: $colours = $silver;
			}
			if ( !empty($current) ) {
				foreach ( $current as $key => $val ) {
					if ( $val != "" ) {
						$colours[$key] = $val;
					} 
				}
			}
			$this->Colours = $colours;
			return $colours;	
		}
		
	
	/**
	*	Writes player start-up js  
	*/
		function write_startup_vars() {
			
			echo "\n<script type=\"text/javascript\"><!--
	var foxpathtoswf = \"" . $this->WPinstallpath . "/wp-content/plugins/mp3-jplayer/js\";
	var foxpathtoimages = \"" . $this->WPinstallpath . "/wp-content/plugins/mp3-jplayer/css/images/\";
	var FoxAnimSlider = " . $this->theSettings['animate_sliders'] . ";
	var fox_playf = \"" . $this->theSettings['encode_files'] . "\";\n";
			echo "//--></script>\n";
			return;
		}
	
			
	/**
	* 	Writes js playlist array.
	*/
		function write_playlist( $thePlayList, $name = "noname" ) {
			
			if ( $thePlayList['count'] < 1 ) { return; }
			
			if ( $this->theSettings['encode_files'] == "true" ) {
				foreach ( $thePlayList['files'] as $k => $file ) { $thePlayList['files'][$k] = base64_encode($file); }
			}
			$tracknumber = 1;
			$addNo = $this->theSettings['add_track_numbering'];
			
			echo "\n<script type=\"text/javascript\"><!--\n var " . $name . " = [\n";
			foreach ( $thePlayList['order'] as $ik => $i ) {
				echo "{name: \"";
				if ( $addNo == "true" ) { echo $tracknumber . ". "; }
				echo $thePlayList['titles'][$i]. "\", mp3: \"" .$thePlayList['files'][$i]. "\", artist: \"" .$thePlayList['artists'][$i]. "\"}";
				if ( $tracknumber != $thePlayList['count'] ) { echo ","; }
				echo "\n";
				$tracknumber++;
			}
			echo "];\n//--></script>\n\n";
			return;
		}
   
   
	/**
	* 	Writes [mp3-jplayer] player html
	*/
   		function write_primary_player( $PlayerName, $pID, $pos, $width, $mods = false, $dload, $title = "", $play_h, $stop_h, $prevnext, $height = "" ) {
			
			// Set position, width 
			$pad_t = $this->theSettings['paddings_top'];
			$pad_b = $this->theSettings['paddings_bottom'];
			$pad_i = $this->theSettings['paddings_inner'];
			if ( $pos == "left" ) { $floater = "float:left; padding:" . $pad_t . " " . $pad_i . " " . $pad_b . " 0px;"; }
			else if ( $pos == "right" ) { $floater = "float:right; padding:" . $pad_t . " 0px " . $pad_b . " " . $pad_i . ";"; }
			else if ( $pos == "absolute" ) { $floater = "position:absolute;"; }
			else if ( $pos == "rel-C" ) { $floater = "position:relative; padding:" . $pad_t . " 0px " . $pad_b . " 0px; margin:0px auto 0px auto;"; }
			else if ( $pos == "rel-R" ) { $floater = "position:relative; padding:" . $pad_t . " 0px " . $pad_b . " 0px; margin:0px 0px 0px auto;"; }
			else { $floater = "position: relative; padding:" . $pad_t . " 0px " . $pad_b . " 0px; margin:0px;"; }
			$width = ( $width == "" ) ? " width:" . $this->theSettings['player_width'] . ";" : " width:" . $width . ";";
			
			// Prep other bits
			$height = ( !empty($height) && $height != "" ) ? " style=\"height:" . $height . ";\"" : ""; //will just use css sheet setting if empty
			$title = ( $title == "" ) ? "" : "<h2>" . $title . "</h2>";
			$Tpad = ( $this->theSettings['add_track_numbering'] == "false" ) ? " style=\"left:16px;\"" : "";
			$addclass = ( $mods ) ? " mp3j_widgetmods" : "";
			$showpopoutbutton = ( $this->theSettings['enable_popout'] == "true" ) ? "visibility: visible;" : "visibility: hidden;";
			$popouttext = ( $this->theSettings['player_theme'] == "styleH" && $this->theSettings['popout_button_title'] == "") ? "POP-OUT PLAYER" : $this->theSettings['popout_button_title'];
			$PLscroll = ( $this->theSettings['max_list_height'] != "" ) ? " style=\"overflow:auto; max-height:" . $this->theSettings['max_list_height'] . "px;\"" : "";
			$showMp3Link = ( $dload == "true" ) ? "visibility: visible;" : "visibility: hidden;";
			
			// Make playlist
			$nooplaylist = "
				<div class=\"listwrap_mp3j\" id=\"L_mp3j_" . $pID . "\"" . $PLscroll . ">
					<div class=\"playlist-wrap-MI\">
						<div class=\"playlist-colour\"></div>
						<div class=\"playlist-wrap-MI\">
								<ul class=\"UL-MI_mp3j" . $addclass . "\" id=\"UL_mp3j_" . $pID . "\"><li></li></ul>
						</div>
					</div>
				</div>";
						
			// Assemble player
			$player = "\n
			<div class=\"wrap-MI\" style=\"" . $floater . $width . "\">" . $title . "
				<div class=\"jp-innerwrap\">
					<div class=\"innerx\"></div>
					<div class=\"innerleft\"></div>
					<div class=\"innerright\"></div>
					<div class=\"innertab\"></div>\n
					<div class=\"jp-interface\"" . $height . ">
						<div id=\"T_mp3j_" . $pID . "\" class=\"player-track-title" . $addclass . "\"" . $Tpad . "></div>
						<div id=\"C_mp3j_" . $pID . "\" class=\"player-artist" . $addclass . "\"></div>
						
						<div class=\"MIsliderVolume\" id=\"vol_mp3j_" . $pID . "\"></div>
						<div class=\"bars_holder\">
							<div class=\"loadMI_mp3j\" id=\"load_mp3j_" . $pID . "\"></div>
							<div class=\"posbarMI_mp3j\" id=\"posbar_mp3j_" . $pID . "\"></div>
						</div>
						<div class=\"transport-MI\">" . $play_h . $stop_h . $prevnext . "</div>
						<div id=\"P-Time-MI_" . $pID . "\" class=\"jp-play-time\"></div>
						<div id=\"T-Time-MI_" . $pID . "\" class=\"jp-total-time\"></div>
						<div id=\"statusMI_" . $pID . "\" class=\"statusMI" . $addclass . "\"></div>
						<div id=\"download_mp3j_" . $pID . "\" class=\"dloadmp3-MI" . $addclass . "\" style=\"" . $showMp3Link . "\"></div>
						<div class=\"playlist-toggle-MI" . $addclass . "\" id=\"playlist-toggle_" . $pID. "\" onclick=\"javascript:MI_toggleplaylist('', " . $pID . ");\">HIDE PLAYLIST</div>
						<div class=\"mp3j-popout-MI" . $addclass . "\" style=\"" .$showpopoutbutton. "\" onclick=\"return launch_mp3j_popout('" . $this->WPinstallpath . "/wp-content/plugins/mp3-jplayer/popout-mp3j.php', " . $pID . ");\">" . $popouttext . "</div>
					</div>
				</div>
				" . $nooplaylist . "
			</div>\n";
						
			return $player;
		}
		
		
	/**
	* 	Writes jPlayer div if needed
	*/
		function write_jp_div() {
		
			if ( !$this->JPdiv ) {
				echo "\n<div style=\"position:relative;\"><div id=\"jquery_jplayer\"></div></div>\n";
				$this->JPdiv = true;
			}
		}   
   
	/**
	* 	ADMIN - Adds css to settings page.
	*/
		function mp3j_admin_header() {
			$pluginpath = $this->WPinstallpath . "/wp-content/plugins/mp3-jplayer/";
			echo "\n<link rel=\"stylesheet\" href=\"" .  $pluginpath . "css/mp3j-admin.css\" type=\"text/css\" media=\"screen\" />\n";			
		}
		
	/**
	* 	ADMIN - Adds js to settings page.
	*/
		function mp3j_admin_footer() {
			$pluginpath = $this->WPinstallpath . "/wp-content/plugins/mp3-jplayer/";
			echo "\n<script type=\"text/javascript\" src=\"" . $pluginpath . "js/mp3j-admin.js\"></script>";
		}

	/**
	* 	ADMIN - preps path/uri before saving.
	*/
		function prep_path ( $field ) {
			$option = preg_replace( "!^.*www*\.!", "http://www.", $field );
			if (strpos($option, "http://") === false) {
				if (preg_match("!^/!", $option) == 0) { 
					$option = "/" . $option; 
				} 
				else { $option = preg_replace("!^/+!", "/", $option); } 
			}
			if (preg_match("!.+/+$!", $option) == 1) 
				$option = preg_replace("!/+$!", "", $option); 
			if ($option == "")
				$option = "/"; 
			return $option;
		}
	
	/**
	*	Debug output, prints vars/arrays to browser source view. 
	*	Called via mp3j_debug() or admin settings.
	*/	
		function debug_info( $display = "" ) {	

			echo "\n\n<!-- *** MP3-jPlayer ** " . "version " . $this->version_of_plugin;
			if ( is_singular() ) { echo "\nTemplate: Singular "; }
			if ( is_single() ) { echo "Post"; }
			if ( is_page() ) { echo "Page"; }
			if ( is_search() ) { echo "\nTemplate: Search"; }
			if ( is_home() ) { echo "\nTemplate: Posts index"; }
			if ( is_front_page() ) { echo " (Home page)"; }
			if ( is_archive() ) { echo "\nTemplate: Archive"; }
			echo "\nUse tags: ";
			if ( $this->theSettings['disable_template_tag'] == "false" ) { echo "Yes"; }
			else { echo "No"; }
			echo "\nPlayer count: " . $this->Player_ID;
			echo "\n\nAll active widgets:\n"; print_r( $this->activeWidgets );
			if ( empty($this->mp3LibraryI) ) { $this->grab_library_info(); } 
			echo "\n\nMP3's in Media Library: " . $this->mp3LibraryI['count'];
			echo "\n\nAdmin Settings:\n"; print_r($this->theSettings);
			echo "\n\n" . $this->debug_string . "\n-->\n\n";
			return;	
		}			
	} //end class
}
?>