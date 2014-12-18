<?php

/**	Frontend functions */
if ( !class_exists("MP3j_Front") && class_exists("MP3j_Main") )
{
	class MP3j_Front extends MP3j_Main
	{
	/**
	*	called when plugin is activated.
	*/
		function initFox() { 
			$this->getAdminOptions();
		}
	
	/**
	*	called when plugin is deactivated, keeps settings if option was ticked.
	*/
		function uninitFox() { 
			$theOptions = get_option($this->adminOptionsName);
			if ( $theOptions['remember_settings'] == "false" ) {
				delete_option($this->adminOptionsName);
			}
		}

	/**
	*	Makes sure options array is up-to-date with code.
	*/
		function make_compatible() {
			if ( $this->iscompat == true ) { 
				return;
			}
			$options = get_option($this->adminOptionsName);			
			if ( !empty($options) ) {
				if ( !isset($options['db_plugin_version']) || $options['db_plugin_version'] != $this->version_of_plugin ) {
					$options = $this->getAdminOptions(); // does the compatibiliy
				}
			}
			$this->theSettings = $options;
			$this->iscompat = true;
			return;
		}

  	/**
	*	Flags for scripts to be added. Called via mp3j_addscripts().
	*/	
		function scripts_tag_handler( $style = "" ) {
			
			// Since 1.7 - convert old style name to new settings
			if ( $style == "styleA" || $style == "styleE" ) {	$style = "styleF"; }
			if ( $style == "styleB" ) { $style = "styleG"; }
			if ( $style == "styleC" ) { $style = "styleH"; }
			if ( $style == "styleD" ) { $style = "styleI"; }
			
			$this->stylesheet = ( $style == "" ) ? $this->theSettings['player_theme'] : $style;
			$this->scriptsflag = "true";
			return;
		}
			
  	/**
	*	Returns mp3's in library in preped arrays. Called with mp3j_grab_library().
	*/			
		function grablibrary_handler( $thereturn ) {
			if ( empty($this->mp3LibraryI) ) { 
				$this->grab_library_info(); 
			}
			$thereturn = $this->mp3LibraryI;			
			return $thereturn;
		}

  	/**
	*	Returns mp3's in library as returned from the select query. Called with mp3j_grab_library().
	*/			
		function grablibraryWP_handler( $thereturn ) {
			if ( empty($this->mp3LibraryWP) ) { 
				$this->grab_library_info(); 
			}
			$thereturn = $this->mp3LibraryWP;
			return $thereturn;
		}

  	/**
	*	** Depreciated 
	*	Flags to ignore player addition via content/shortcode/widget. Called via mp3j_flag().
	*/	
		function flag_tag_handler($set = 1) {
			// Do nothing since 1.6
			return;
		}
		
  	/**
	*	** Depreciated 
	*	Creates meta arrays using mp3j_set_meta() tag.
	*/			
		function set_meta_handler( $tracks, $captions, $startnum = "" ) {
			// Do nothing since 1.6
			return;
		}
		
  	/**
	*	* Not adding players directly via content hook anymore so not used.
	*	Flags not to add player if it's going to be an excerpt generated from the content. Reset by content handler.
	*/	
		function get_excerpt_handler( $stored = "" ) { 
			if ( $stored == "" ) { $this->excerptCalled = true;	}
			return $stored;
		}
	
  	/**
	* 	Checks whether js and css scripts are needed on the page, and which css sheet to use if they are.
	*	singular pages - added if there's shortcodes, or if there's any widget to go down.
	*	index/archive pages - added if there's a widget, or if 'show player on index' option is ticked.
	*/
		function header_scripts_handler() {
						
			$this->make_compatible();
			$scripts = false;
			
			// Request via template function
			if ( $this->scriptsflag == "true" && $this->theSettings['disable_template_tag'] == "false" ) {
				$scripts = true;
			}
			
			// Check widgets
			$allowed_widget = $this->has_allowed_widget( "mp3-jplayer-widget" ); //echo "<br />is allowed widget: " . $allowed_widget . "<br />";
			$allowed_widget_B = $this->has_allowed_widget( "mp3mi-widget" ); //echo "<br />is allowed widgetB: " . $allowed_widget_B . "<br />"; 
			
			// On multi-post pages
			//if ( is_home() || is_archive() ) {
			if ( is_home() || is_archive() || is_search() ) {
				if ( $allowed_widget || $allowed_widget_B || $this->theSettings['player_onblog'] == "true" ) {
					$scripts = true;
				}
			}
			// On singulars
			if ( is_singular() ) {	
				if ( $this->grab_Custom_Meta() > 0 ) { // mode 1 widget 
					$this->PlayerPlaylist = $this->generate_playlist( $this->postMetaKeys, $this->postMetaValues, 1 );
				}
				if ( $allowed_widget || $allowed_widget_B || $this->has_shortcodes() ) {
					$scripts = true;
				}				
			}
			
			// On search pages
			//if ( is_search() ) {
			//	if ( $allowed_widget || $allowed_widget_B ) {
			//		$scripts = true;
			//	}
			//}
			
			// Add the scripts
			if ( $scripts ) {
				$style = ( $this->stylesheet == "" ) ? $this->theSettings['player_theme'] : $this->stylesheet;
				$this->add_Scripts( $style );
			}
			return;
		}
	
	
	/**
	*	Checks whether to write js playlists and startup, and diagnostic. 
	*	Called by wp_footer().
	*/	
		function footercode_handler() {
			
			// Write the inline players playlist js
			if ( $this->InlinePlaylist['count'] > 0 ) {
				$this->write_playlist( $this->InlinePlaylist, "foxInline" );
			}
			
			// Write js vars needed at startup
			if ( $this->PlayerPlaylist['count'] > 0 || $this->InlinePlaylist['count'] > 0 || !empty($this->jsInfo) ) {
				$this->write_startup_vars();
			}
			// Write js to add the inline player titles
			if ( !empty($this->Footerjs) ) {
				echo "\n<!-- MP3 jPlayer -->\n<script type=\"text/javascript\"><!--\nfunction mp3j_footerjs() {\n" . $this->Footerjs . "\n}\n//--></script>";
			}
			// Write players info array js
			if ( !empty($this->jsInfo) ) {
				$c = count($this->jsInfo);
				echo "\n<script type=\"text/javascript\"><!--\nvar mp3j_info = [";
				foreach ( $this->jsInfo as $k => $v ) { 
					echo $v;
					if ( $k < $c-1 ) { echo ","; }
				}
				echo "\n];\n//--></script>";
			}
			// Write fields listnames array js
			if ( !empty($this->jsFields) ) {
				$c = count($this->jsFields);
				echo "\n<script type=\"text/javascript\"><!--\nvar mp3j_fields = [";
				foreach ( $this->jsFields as $k => $v ) { 
					echo $v;
					if ( $k < $c-1 ) { echo ","; }
				}
				echo "\n];\n//--></script>";
			}
			// Write debug
			if ( $this->theSettings['echo_debug'] == "true" ) { 
				$this->debug_info(); }
			return;	
		}


	/**
	* 	Writes a js playlist from the custom fields based on the currently running content id.
	*/
		function content_handler( $content = '' ) {
			
			//if ( !is_singular() && !is_home() && !is_archive() ) { 
			if ( !is_singular() && !is_home() && !is_archive() && !is_search() ) { 
				return $content;
			}
			$this->has_fields = false;
			$this->single_autocount = 0;
			if ( $this->grab_Custom_Meta() > 0 ) { 	
				$fields_playlist = $this->generate_playlist( $this->postMetaKeys, $this->postMetaValues, 1 );
				if ( $fields_playlist['count'] > 0 ) {
					$playlist_name = "fieldsList_" . $this->FieldsList_num;
					$this->write_playlist( $fields_playlist, $playlist_name );
					
					$this->jsFields[] = "\n { list:" . $playlist_name . " }";
					$this->FieldsList_num++;
					$this->has_fields = $playlist_name;
				}
			}
			return $content;
		}
		

	/**
	*	Handles [mp3t] shortcodes single in-line (text) players.
	*	TODO: download
	*/	
		function inline_play_handler( $atts, $content = null ) {
			
			//if ( !$this->external_call && (is_home() || is_archive()) && $this->theSettings['player_onblog'] == "false" ) { 
			if ( !$this->external_call && (is_home() || is_archive() || is_search()) && $this->theSettings['player_onblog'] == "false" ) { 
				return; 
			}
			$id = $this->Player_ID;			
			extract(shortcode_atts(array( // Defaults
				'bold' => 'y',
				'play' => 'Play',
				'track' => '',
				'caption' => '',
				'flip' => 'l',
				'title' => '#USE#',
				'stop' => 'Stop',
				'ind' => 'y',
				'autoplay' => $this->theSettings['auto_play'],
				'loop' => 'false',
				'vol' => $this->theSettings['initial_vol'],
				'flow' => 'n'
			), $atts));
					
			if ( $track == "" ) { // Auto increment 
				if ( !$this->has_fields || $this->external_call ) { return; }
				$track = ++$this->single_autocount;
				$arb = "";
			} 
			elseif ( is_numeric($track) ) { // Has a track number
				if ( !$this->has_fields || $this->external_call ) { return; }
				$arb = "";
			} 
			else { // Has arbitrary file/uri				
				if ( !$this->string_pushto_playlist( $track, $caption, "1" ) ) { return; }
				$track = $this->InlinePlaylist['count'];					
				$arb = "arb";
			}
			
			$divO = "";
			$divC = "";
			if ( $flow == "n" || $this->external_call ) {
				$divO = "<div style=\"font-size:14px; line-height:22px !important; margin:0 !important;\">";
				$divC = "</div>";
			}
			
			$playername = ( $arb != "" ) ? "foxInline" : $this->has_fields;
			
			// Set font weight
			$b = ( $bold == "false" || $bold == "0" || $bold == "n" ) ? " style=\"font-weight:500;\"" : " style=\"font-weight:700;\"";
			
			// Set spacer between elements depending on play/stop/title
			if ( $play != "" && $title != "" ){	
				$spacer = "&nbsp;"; 
			} else {
				$spacer = "";
				if ( $play == "" && $stop != "" ) { $stop = " " . $stop; }
			}
			// Prep title
			$customtitle = ( $title == "#USE#" ) ? "" : $title;
			
			// Make id'd span elements
			$openWrap = $divO . "<span id=\"playpause_wrap_mp3j_" . $id . "\" class=\"wrap_inline_mp3j\"" . $b . ">";
			//$pos = "<span class=\"bars_mp3j\"><span class=\"load_mp3j\" id=\"load_mp3j_" . $id . "\" style=\"background:" . $this->Colours['loadbar_colour'] . ";\"></span><span class=\"posbar_mp3j\" id=\"posbar_mp3j_" . $id. "\"></span></span>";
			$pos = "<span class=\"bars_mp3j\"><span class=\"load_mp3j\" id=\"load_mp3j_" . $id . "\"></span><span class=\"posbar_mp3j\" id=\"posbar_mp3j_" . $id. "\"></span></span>";
			//$play_h = "<span class=\"textbutton_mp3j\" id=\"playpause_mp3j_" . $id . "\" style=\"color:" . $this->Colours['list_current_colour'] . ";\">" . $play . "</span>";
			$play_h = "<span class=\"textbutton_mp3j\" id=\"playpause_mp3j_" . $id . "\">" . $play . "</span>";
			$title_h = ( $title == "#USE#" || $title != "" ) ? "<span class=\"T_mp3j\" id=\"T_mp3j_" . $id . "\">" . $customtitle . "</span>" : "";
			$closeWrap = ( $ind != "y" ) ? "<span style=\"display:none;\" id=\"indi_mp3j_" . $id . "\"></span></span>" . $divC : "<span class=\"indi_mp3j\" id=\"indi_mp3j_" . $id . "\"></span></span>" . $divC;
			
			// SHOULD THIS GO SOMEWHERE IN SPAN FORMAT??
			$vol_h = "<div class=\"vol_mp3j\" id=\"vol_mp3j_" . $id . "\"></div>";
			
			// Assemble them		
			$html = ( $flip != "l" ) ? $openWrap . $pos . $title_h . $spacer . $play_h . $closeWrap : $openWrap . $pos . $play_h . $spacer . $title_h . $closeWrap;
			
			// Add title to js footer string if needed 
			if ( $title_h != "" && $title == "#USE#" ) {
				$this->Footerjs .= "jQuery(\"#T_mp3j_" . $id . "\").append(" . $playername . "[" . ($track-1) . "].name);\n";
				//$this->Footerjs .= "jQuery(\"#T_mp3j_" . $id . "\").append('<span style=\"font-size:.7em;\"> - '+" . $playername . "[" . ($track-1) . "].artist+'</span>');\n";
				$this->Footerjs .= "if (" . $playername . "[" . ($track-1) . "].artist !==''){ jQuery(\"#T_mp3j_" . $id . "\").append('<span style=\"font-size:.75em;\"> - '+" . $playername . "[" . ($track-1) . "].artist+'</span>'); }\n";
			}
			// Add info to js info array
			$autoplay = ( $autoplay == "true" || $autoplay == "y" || $autoplay == "1" ) ? "true" : "false";
			$loop = ( $loop == "true" || $loop == "y" || $loop == "1" ) ? "true" : "false";
			$this->jsInfo[] = "\n { list:" . $playername . ", type:'single', tr:" . ($track-1) . ", lstate:'', loop:" . $loop . ", play_txt:'" . $play . "', pause_txt:'" . $stop . "', pp_title:'', autoplay:" . $autoplay . ", has_ul:0, transport:'playpause', status:'basic', download:false, vol:" . $vol . ", height:'' }";
			
			$this->write_jp_div();
			$this->Player_ID++;
			return $html;
		}
			

	/**
	*	Handles [mp3j] shortcodes.
	*	TODO: download
	*/	
		function inline_play_graphic( $atts, $content = null ) {
			
			//if ( !$this->external_call && (is_home() || is_archive()) && $this->theSettings['player_onblog'] == "false" ) { 
			if ( !$this->external_call && (is_home() || is_archive() || is_search()) && $this->theSettings['player_onblog'] == "false" ) { 
				return; 
			}
			$id = $this->Player_ID;			
			extract(shortcode_atts(array( // Defaults
				'bold' => 'y',
				'track' => '',
				'caption' => '',
				'flip' => 'r',
				'title' => '#USE#',
				'ind' => 'y',
				'autoplay' => $this->theSettings['auto_play'],
				'loop' => 'false',
				'vol' => $this->theSettings['initial_vol'],
				'flow' => 'n'
			), $atts));
					
			if ( $track == "" ) { // Auto increment 
				if ( !$this->has_fields || $this->external_call ) { return; }
				$track = ++$this->single_autocount;
				$arb = "";
			}
			elseif ( is_numeric($track) ) { // Has a track number
				if ( !$this->has_fields || $this->external_call ) { return; }
				$arb = "";
			}
			else { // Has arbitrary file/uri				
				if ( !$this->string_pushto_playlist( $track, $caption, "1" ) ) { return; }
				$track = $this->InlinePlaylist['count'];					
				$arb = "arb";
			}
			
			$divO = "";
			$divC = "";
			if ( $flow == "n" || $this->external_call ) {
				$divO = "<div style=\"font-size:14px; line-height:22px !important; margin:0 !important;\">";
				$divC = "</div>";
			}
			
			$playername = ( $arb != "" ) ? "foxInline" : $this->has_fields;
			
			// Set font weight
			$b = ( $bold == "false" || $bold == "N" || $bold == "n" ) ? " style=\"font-weight:500;\"" : " style=\"font-weight:700;\"";
			// Prep title
			$customtitle = ( $title == "#USE#" ) ? "" : $title;
			// tell js it's graphics buttons
			$play = "#USE_G#";
			
			// Make id'd span elements
			$openWrap = $divO . "<span id=\"playpause_wrap_mp3j_" . $id . "\" class=\"wrap_inline_mp3j\"" . $b . ">";
			$pos = "<span class=\"bars_mp3j\"><span class=\"loadB_mp3j\" id=\"load_mp3j_" . $id . "\"></span><span class=\"posbarB_mp3j\" id=\"posbar_mp3j_" . $id . "\"></span></span>";
			$play_h = "<span class=\"buttons_mp3j\" id=\"playpause_mp3j_" . $id . "\">&nbsp;</span>";
			$spacer = "";
			$title_h = ( $title == "#USE#" || $title != "" ) ? "<span class=\"T_mp3j\" id=\"T_mp3j_" . $id . "\">" . $customtitle . "</span>" : "";
			$indi_h = ( $ind != "y" ) ? "<span style=\"display:none;\" id=\"indi_mp3j_" . $id . "\"></span>" : "<span class=\"indi_mp3j\" id=\"indi_mp3j_" . $id . "\"></span>";
			
			// TODO: SHOULD THIS GO SOMEWHERE IN SPAN FORMAT??
			$vol_h = "<div class=\"vol_mp3j\" id=\"vol_mp3j_" . $id . "\"></div>";

			// Assemble them		
			$html = ( $flip == "r" ) ? $openWrap . "<span class=\"group_wrap\">" . $pos . $title_h . $indi_h . "</span>" . $play_h . "</span>" . $divC : $openWrap . $play_h . "&nbsp;<span class=\"group_wrap\">" . $pos . $title_h . $indi_h . "</span></span>" . $divC;
			
			// Add title to js footer string if needed 
			if ( $title_h != "" && $title == "#USE#" ) {
				$this->Footerjs .= "jQuery(\"#T_mp3j_" . $id . "\").append(" . $playername . "[" . ($track-1) . "].name);\n";
				//$this->Footerjs .= "jQuery(\"#T_mp3j_" . $id . "\").append('<span style=\"font-size:.7em;\"> - '+" . $playername . "[" . ($track-1) . "].artist+'</span>');\n";
				$this->Footerjs .= "if (" . $playername . "[" . ($track-1) . "].artist !==''){ jQuery(\"#T_mp3j_" . $id . "\").append('<span style=\"font-size:.75em;\"> - '+" . $playername . "[" . ($track-1) . "].artist+'</span>'); }\n";
			}
			// Add info to js info array
			$autoplay = ( $autoplay == "true" || $autoplay == "y" || $autoplay == "1" ) ? "true" : "false";
			$loop = ( $loop == "true" || $loop == "y" || $loop == "1" ) ? "true" : "false";
			$this->jsInfo[] = "\n { list:" . $playername . ", type:'single', tr:" . ($track-1) . ", lstate:'', loop:" . $loop . ", play_txt:'" . $play . "', pause_txt:'', pp_title:'', autoplay:" . $autoplay . ", has_ul:0, transport:'playpause', status:'basic', download:false, vol:" . $vol . ", height:'' }";
			
			$this->write_jp_div();
			$this->Player_ID++;
			return $html;
		}
			
						
	/**
	*	Handles [mp3-jplayer] shortcodes.
	*/	
		function primary_player ( $atts, $content = null ) {
			
			//if ( !$this->external_call && (is_home() || is_archive()) && $this->theSettings['player_onblog'] == "false" ) { 
			if ( !$this->external_call && (is_home() || is_archive() || is_search()) && $this->theSettings['player_onblog'] == "false" ) { 
				return; 
			}
			$pID = $this->Player_ID;
			extract(shortcode_atts(array( // Defaults
				'tracks' => '',
				'captions' => '',
				'dload' => $this->theSettings['show_downloadmp3'],
				'flip' => 'r',
				'title' => '',
				'ind' => 'y',
				'list' => $this->theSettings['playlist_show'],
				'pn' => 'y',
				'width' => '',
				'pos' => $this->theSettings['player_float'],
				'stop' => 'y',
				'shuffle' => false,
				'slice' => '',
				'pick' => '',
				'mods' => false,
				'id' => '',
				'loop' => $this->theSettings['playlist_repeat'],
				'autoplay' => $this->theSettings['auto_play'],
				'vol' => $this->theSettings['initial_vol'],
				'height' => ''
			), $atts));
			
			// Build 'tracks' playlist, if no tracks then try fields from 'id' or else from this id.		
			if ( !$this->string_pushto_playlist( $tracks, $captions, "new" ) ) { 
				
				//if ( $this->external_call && (is_home() || is_archive()) ) { return; }
				if ( (is_home() || is_archive() || is_search()) && $this->external_call && !$this->tag_call ) { return; } //allow tags but not widgets to use fields on multi-post pages
			
				if ( $this->grab_Custom_Meta($id) > 0 ) {
					$this->NewPlaylist = $this->generate_playlist( $this->postMetaKeys, $this->postMetaValues, 1 );
					if ( $this->NewPlaylist['count'] < 1 ) {
						return;
					}
				} else {
					return;
				}				 
			}					
			if ( $slice != "" && $slice > 0 ) { $this->NewPlaylist = $this->take_playlist_slice( $slice, $this->NewPlaylist ); }
			if ( $pick != "" && $pick > 0 ) { $this->NewPlaylist = $this->take_playlist_slice( $pick, $this->NewPlaylist ); }
			if ( $shuffle ) { if ( $this->NewPlaylist['count'] > 1 ) { shuffle( $this->NewPlaylist['order'] ); } }
			
			// Write it
			$PlayerName = "mp3jNew_" . $this->NewList_num; 
			$this->write_playlist( $this->NewPlaylist, $PlayerName );
			
			// Add info to js info array
			$pp_height = (int)$height;
			$pp_height = ( empty($pp_height) || $pp_height === 0 ) ? 100 : $pp_height;
			$play = "#USE_G#";
			$pp_title = ( $title == "" ) ? get_bloginfo('name') : $title;
			$list = ( $list == "true" || $list == "y" || $list == "1" ) ? "true" : "false";
			$dload_info = ( $dload == "true" || $dload == "y" || $dload == "1" ) ? "true" : "false";
			$autoplay = ( $autoplay == "true" || $autoplay == "y" || $autoplay == "1" ) ? "true" : "false";
			$loop = ( $loop == "true" || $loop == "y" || $loop == "1" ) ? "true" : "false";
			$this->jsInfo[] = "\n { list:" . $PlayerName . ", type:'MI', tr:0, lstate:" . $list . ", loop:" . $loop . ", play_txt:'" . $play . "', pause_txt:'', pp_title:'" . $pp_title . "', autoplay:" . $autoplay . ", has_ul:1, transport:'playpause', status:'full', download:" . $dload_info . ", vol:" . $vol . ", height:" . $pp_height . " }";
			
			// Make transport buttons
			$prevnext = ( $this->NewPlaylist['count'] > 1 && $pn == "y" ) ? "<div class=\"Next_mp3j\" id=\"Next_mp3j_" . $pID . "\">Next&raquo;</div><div class=\"Prev_mp3j\" id=\"Prev_mp3j_" . $pID . "\">&laquo;Prev</div>" : "";
			$play_h = "<div class=\"buttons_mp3j\" id=\"playpause_mp3j_" . $pID . "\">Play Pause</div>";
			$stop_h = ( $stop == "y" ) ? "<div class=\"stop_mp3j\" id=\"stop_mp3j_" . $pID . "\">Stop</div>" : "";
			
			// Build player html
			if ( $this->external_call && $width == "" ) { $width = "100%"; } //set default width when called by shortcode-widget (or tag) and it wasn't specified
			$player = $this->write_primary_player( $PlayerName, $pID, $pos, $width, $mods, $dload_info, $title, $play_h, $stop_h, $prevnext, $height );
			
			$this->write_jp_div();
			$this->NewList_num++;
			$this->Player_ID++;
			return $player;
		}


	/**
	*	Handles [mp3-link] shortcodes.
	*/	
		function link_plays_track( $atts, $content = null ) {
			
			if ( is_home() || is_archive() || is_search() ) { // can't really use links on multi-post pages! 
				return; 
			}
			extract(shortcode_atts(array( // Defaults
				'player' => '',
				'track' => '',
				'text' => 'Play',
				'bold' => 'n'
			), $atts));
			
			if ( $player == "" ) { return; }
			if ( $track == "" ) { $track = "0"; }
			if ( $bold != "n" ) { 
				$O_tag = "<strong>"; 
				$C_tag = "</strong>"; 
			} else {
				$O_tag = ""; 
				$C_tag = "";
			}
			$the_link = "<span class=\"mp3j-link-play\" onclick=\"javascript:link_plays_track(" . $player .", " . $track . ");\">" . $O_tag . $text . $C_tag . "</span>";
			return $the_link;
		}


	/**
	*	Called via mp3j_put() in template to run shortcodes.
	*/
		function template_tag_handler( $id = "", $pos = "", $dload = "", $play = "", $list = "" ) {
			
			$this->putTag_runCount++;
			if ( $this->theSettings['disable_template_tag'] == "true" ) { return; }
			
			if ( !empty($id) && !is_numeric($id) ) {
				
				$this->external_call = true;
				$this->tag_call = true; // patch to allow tags to run 'mode 1' on index/archive/search pages
				$shortcodes_return = do_shortcode( $id );
				$this->external_call = false;
				$this->tag_call = false;
			
			}
			echo $shortcodes_return;
			return;			
		}
	

	/**
	* 	Displays and updates the admin options on settings page.
	*/
		function printAdminPage() { 
			
			$theOptions = $this->getAdminOptions();
			$colours_array = array();
			
			if (isset($_POST['update_mp3foxSettings']))
			{
				if (isset($_POST['mp3foxVol'])) {
					$theOptions['initial_vol'] = preg_replace("/[^0-9]/","", $_POST['mp3foxVol']); 
					if ($theOptions['initial_vol'] < 0 || $theOptions['initial_vol']=="") { $theOptions['initial_vol'] = "0"; }
					if ($theOptions['initial_vol'] > 100) { $theOptions['initial_vol'] = "100"; }
				}
				if (isset($_POST['mp3foxPopoutMaxHeight'])) {
					$theOptions['popout_max_height'] = preg_replace("/[^0-9]/","", $_POST['mp3foxPopoutMaxHeight']); 
					if ( $theOptions['popout_max_height'] == "" ) { $theOptions['popout_max_height'] = "750"; }
					if ( $theOptions['popout_max_height'] < 200 ) { $theOptions['popout_max_height'] = "200"; }
					if ( $theOptions['popout_max_height'] > 1200 ) { $theOptions['popout_max_height'] = "1200"; }
				}
				
				if (isset($_POST['mp3foxPopoutWidth'])) {
					$theOptions['popout_width'] = preg_replace("/[^0-9]/","", $_POST['mp3foxPopoutWidth']); 
					if ( $theOptions['popout_width'] == "" ) { $theOptions['popout_width'] = "400"; }
					if ( $theOptions['popout_width'] < 250 ) { $theOptions['popout_width'] = "250"; }
					if ( $theOptions['popout_width'] > 1600 ) { $theOptions['popout_width'] = "1600"; }
				}
				
				if (isset($_POST['mp3foxMaxListHeight'])) {
					$theOptions['max_list_height'] = preg_replace("/[^0-9]/","", $_POST['mp3foxMaxListHeight']); 
					if ( $theOptions['max_list_height'] < 0 ) { $theOptions['max_list_height'] = ""; }
				}
				if (isset($_POST['mp3foxfolder'])) { $theOptions['mp3_dir'] = $this->prep_path( $_POST['mp3foxfolder'] ); }
				if (isset($_POST['mp3foxCustomStylesheet'])) { $theOptions['custom_stylesheet'] = $this->prep_path( $_POST['mp3foxCustomStylesheet'] ); }
				if (isset($_POST['mp3foxTheme'])) { $theOptions['player_theme'] = $_POST['mp3foxTheme']; }			
				if (isset($_POST['mp3foxFloat'])) { $theOptions['player_float'] = $_POST['mp3foxFloat']; }
				if (isset($_POST['mp3foxPlayerWidth'])) { $theOptions['player_width'] = $_POST['mp3foxPlayerWidth']; }
				if (isset($_POST['mp3foxPopoutBackground'])) { $theOptions['popout_background'] = $_POST['mp3foxPopoutBackground']; }
				if (isset($_POST['mp3foxPopoutBGimage'])) { $theOptions['popout_background_image'] = $_POST['mp3foxPopoutBGimage']; }
				if (isset($_POST['mp3foxPluginVersion'])) { $theOptions['db_plugin_version'] = $_POST['mp3foxPluginVersion']; }
				if (isset($_POST['mp3foxPopoutButtonText'])) { $theOptions['popout_button_title'] = $_POST['mp3foxPopoutButtonText']; }
				
				$theOptions['paddings_top'] = ( $_POST['mp3foxPaddings_top'] == "" ) ? "0px" : $_POST['mp3foxPaddings_top'];
				$theOptions['paddings_bottom'] = ( $_POST['mp3foxPaddings_bottom'] == "" ) ? "0px" : $_POST['mp3foxPaddings_bottom'];
				$theOptions['paddings_inner'] = ( $_POST['mp3foxPaddings_inner'] == "" ) ? "0px" : $_POST['mp3foxPaddings_inner'];
				$theOptions['auto_play'] = (isset($_POST['mp3foxAutoplay'])) ? $_POST['mp3foxAutoplay'] : "false";
				$theOptions['allow_remoteMp3'] = (isset($_POST['mp3foxAllowRemote'])) ? $_POST['mp3foxAllowRemote'] : "false";
				$theOptions['playlist_AtoZ'] = (isset($_POST['mp3foxAtoZ'])) ? $_POST['mp3foxAtoZ'] : "false";
				$theOptions['player_onblog'] = (isset($_POST['mp3foxOnBlog'])) ? $_POST['mp3foxOnBlog'] : "false";
				$theOptions['playlist_UseLibrary'] = (isset($_POST['mp3foxUseLibrary'])) ? $_POST['mp3foxUseLibrary'] : "false";
				$theOptions['playlist_show'] = (isset($_POST['mp3foxShowPlaylist'])) ? $_POST['mp3foxShowPlaylist'] : "false";
				$theOptions['remember_settings'] = (isset($_POST['mp3foxRemember'])) ? $_POST['mp3foxRemember'] : "false";
				$theOptions['hide_mp3extension'] = (isset($_POST['mp3foxHideExtension'])) ? $_POST['mp3foxHideExtension'] : "false";
				$theOptions['show_downloadmp3'] = (isset($_POST['mp3foxDownloadMp3'])) ? $_POST['mp3foxDownloadMp3'] : "false";
				$theOptions['disable_template_tag'] = (isset($_POST['disableTemplateTag'])) ? $_POST['disableTemplateTag'] : "false";
				//$theOptions['use_small_player'] = (isset($_POST['mp3foxSmallPlayer'])) ? $_POST['mp3foxSmallPlayer'] : "false";
				//$theOptions['force_scripts_from_admin'] = (isset($_POST['mp3foxForceScripts'])) ? $_POST['mp3foxForceScripts'] : "false";
				//$theOptions['give_shortcode_priority'] = (isset($_POST['giveShortcodePriority'])) ? $_POST['giveShortcodePriority'] : "false";
				$theOptions['echo_debug'] = (isset($_POST['mp3foxEchoDebug'])) ? $_POST['mp3foxEchoDebug'] : "false";
				$theOptions['add_track_numbering'] = (isset($_POST['mp3foxAddTrackNumbers'])) ? $_POST['mp3foxAddTrackNumbers'] : "false";
				$theOptions['enable_popout'] = (isset($_POST['mp3foxEnablePopout'])) ? $_POST['mp3foxEnablePopout'] : "false";
				$theOptions['playlist_repeat'] = (isset($_POST['mp3foxPlaylistRepeat'])) ? $_POST['mp3foxPlaylistRepeat'] : "false";
				$theOptions['use_fixed_css'] = (isset($_POST['mp3foxUseFixedCSS'])) ? $_POST['mp3foxUseFixedCSS'] : "false";
				$theOptions['encode_files'] = (isset($_POST['mp3foxEncodeFiles'])) ? $_POST['mp3foxEncodeFiles'] : "false";
				$theOptions['animate_sliders'] = (isset($_POST['mp3foxAnimSliders'])) ? $_POST['mp3foxAnimSliders'] : "false";
				
				// Colours array//
				if (isset($_POST['mp3foxScreenColour'])) { $colours_array['screen_colour'] = $_POST['mp3foxScreenColour']; }
				if (isset($_POST['mp3foxScreenOpac'])) { $colours_array['screen_opacity'] = $_POST['mp3foxScreenOpac']; }
				if (isset($_POST['mp3foxLoadbarColour'])) { $colours_array['loadbar_colour'] = $_POST['mp3foxLoadbarColour']; }
				if (isset($_POST['mp3foxLoadbarOpac'])) { $colours_array['loadbar_opacity'] = $_POST['mp3foxLoadbarOpac']; }
				if (isset($_POST['mp3foxPosbarColour'])) { $colours_array['posbar_colour'] = $_POST['mp3foxPosbarColour']; }
				if (isset($_POST['mp3foxPosbarTint'])) { $colours_array['posbar_tint'] = $_POST['mp3foxPosbarTint']; }
				if (isset($_POST['mp3foxPosbarOpac'])) { $colours_array['posbar_opacity'] = $_POST['mp3foxPosbarOpac']; }
				if (isset($_POST['mp3foxScreenTextColour'])) { $colours_array['screen_text_colour'] = $_POST['mp3foxScreenTextColour']; }
				if (isset($_POST['mp3foxPlaylistColour'])) { $colours_array['playlist_colour'] = $_POST['mp3foxPlaylistColour']; }
				if (isset($_POST['mp3foxPlaylistTint'])) { $colours_array['playlist_tint'] = $_POST['mp3foxPlaylistTint']; }
				if (isset($_POST['mp3foxPlaylistOpac'])) { $colours_array['playlist_opacity'] = $_POST['mp3foxPlaylistOpac']; }
				if (isset($_POST['mp3foxListTextColour'])) { $colours_array['list_text_colour'] = $_POST['mp3foxListTextColour']; }
				if (isset($_POST['mp3foxListCurrentColour'])) { $colours_array['list_current_colour'] = $_POST['mp3foxListCurrentColour']; }
				if (isset($_POST['mp3foxListHoverColour'])) { $colours_array['list_hover_colour'] = $_POST['mp3foxListHoverColour']; }
				if (isset($_POST['mp3foxListBGaHover'])) { $colours_array['listBGa_hover'] = $_POST['mp3foxListBGaHover']; }
				if (isset($_POST['mp3foxListBGaCurrent'])) { $colours_array['listBGa_current'] = $_POST['mp3foxListBGaCurrent']; }
				if (isset($_POST['mp3foxVolGrad'])) { $colours_array['volume_grad'] = $_POST['mp3foxVolGrad']; }
				if (isset($_POST['mp3foxListDivider'])) { $colours_array['list_divider'] = $_POST['mp3foxListDivider']; }
				if (isset($_POST['mp3foxIndicator'])) { $colours_array['indicator'] = $_POST['mp3foxIndicator']; }
				$theOptions['colour_settings'] = $colours_array;
				
				update_option($this->adminOptionsName, $theOptions);
			?>
				<!-- Settings saved message -->
				<div class="updated"><p><strong><?php _e("Settings Updated.", $this->textdomain );?></strong></p></div>
			
			<?php 
			}
			// Pick up current colours
			$current_colours = $theOptions['colour_settings'];
			?>
			
			<div class="wrap">
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					<div style="padding: 0px; margin: 0px 120px 0px 0px; border-bottom: 1px solid #ddd;"><h2 style="margin-top: 4px; margin-bottom: -6px;">Mp3<span style="font-size: 15px;"> - </span>jPlayer<span class="description" style="font-size: 10px;">&nbsp; <?php echo $this->version_of_plugin; ?></span></h2></div> 
					<h5 style="margin: 10px 120px 20px 0px; color:#888;"><a href="javascript:mp3jp_listtoggle('fox_help1','help');" id="fox_help1-toggle">Show help</a></h5>
					<div id="fox_help1-list" style="display:none;margin:15px 0px 30px 0px; border-bottom:1px solid #ddd;">
								<h5 class="description" style="margin: 10px 120px 0px 10px;">Add players using<code>[mp3j]</code> <code>[mp3t]</code> <code>[mp3-jplayer]</code> <code>[mp3-link]</code> shortcodes and/or <a href="widgets.php">widgets</a>.</h5>
								
								<p style="margin: 10px 120px 15px 10px;"><span class="description">Eg.<br />
									Play a single file:</span> <code>[mp3j track="myfile.mp3"]</code><br />
									<span class="description">Playlist files:</span> <code>[mp3-jplayer tracks="myfileA.mp3, myfileB.mp3, myfileC.mp3"]</code>  &nbsp;(use commas to separate files)<br />
									<span class="description">Add titles:</span> <code>[mp3-jplayer tracks="MyTitle@myfileA.mp3, MyTitle@myfileB.mp3, MyTitle@myfileC.mp3"]</code><br />
									<span class="description">Add captions:</span> <code>[mp3-jplayer tracks="fileA.mp3, fileB.mp3" captions="Caption A; Caption B"]</code> &nbsp;(use semicolons to separate captions)
								</p>
									
								
								<h4 class="description" style="margin:0px 0px 0px 10px; color:#606060;">Shortcode Parameters</h4>
								<div style="margin:0px 0px 0px 15px;">
									<h5 style="margin:10px 0px 5px 0px;"><code>[mp3j]</code> &amp; <code>[mp3t]</code> add a single-track player</h5>
									<p style="margin: 0px 120px 20px 10px;">
										<code>track</code> <span class="description" >filename or URI. Add title using '@' as separator eg. Mytitle@filename.mp3</span><br /><code>caption</code> <span class="description" >caption text. Appears to the right of title</span><br /><code>vol</code> <span class="description" >0 - 100</span><br /><code>autoplay</code> <span class="description" >y/n</span><br /><code>loop</code> <span class="description" >y/n, repeat play track (overides subsequent autoplay)</span><br />
										<code>title</code> <span class="description" >replaces both title and caption</span><br /><code>bold</code> <span class="description" >y/n, makes font bold</span><br /><code>flip</code> <span class="description" >y/n, move play/pause button to other side</span><br /><code>ind</code> <span class="description" >y/n, hide indicator and time</span><br /><code>flow</code> <span class="description" >y/n, set to 'y' to put players within paragraphs without line-breaking (works ok for line heights around 22px)</span></p>
									<h5 style="margin: 0px 120px 1px 10px;">Also for <code>[mp3t]</code></h5>
									<p style="margin: 0px 120px 1px 10px;"><code>play</code> <span class="description">play button text</span><br /><code>stop</code> <span class="description">pause button text</span></p>
									<h5 style="margin:15px 0px 5px 0px;"><code>[mp3-jplayer]</code> adds a playlist player</h5>
									<p style="margin: 0px 120px 1px 10px;">
										<code>tracks</code> <span class="description">Comma separated list of filenames/URI's/folders. Add titles using '@' as separator eg. Mytitle@filename.mp3</span><br /><code>captions</code> <span class="description">Semi-colon separated list eg. "caption 1; caption 2;"</span><br /><code>vol</code> <span class="description">0 - 100</span><br /><code>autoplay</code> <span class="description">y/n</span><br /><code>loop</code> <span class="description">y/n, repeat plays track(s)</span><br /><code>dload</code> <span class="description">y/n, show/hide download link</span><br /><code>list</code> <span class="description">y/n, show/hide playlist</span><br /><code>pick</code> <span class="description">number, picks random selection</span><br /><code>shuffle</code> <span class="description">y/n, shuffle track order</span><br />
										<code>title</code> <span class="description">appears above player</span><br /><code>pos</code> <span class="description">rel-L, rel-C, rel-R, left, right</span><br /><code>width</code> <span class="description">px or %</span><br /><code>height</code> <span class="description">px only, player height excluding list</span><br /><code>pn</code> <span class="description">y/n, hide prev/next buttons</span><br /><code>stop</code> <span class="description">y/n, hide stop button</span><br /><code>mods</code> <span class="description">y/n, add css mods (makes fonts smaller as standard)</span><br /><code>id</code> <span class="description">a page id to read the custom fields from (ignored if 'tracks' is used in same shortcode, or id has no tracks)</span></p>
									
									<h5 style="margin: 10px 120px 5px 10px;">Use these instead of a filename to playlist folders or the library:</h5>
									<p style="margin: 0px 120px 20px 10px;"><code>FEED:LIB</code> - <span class="description">Play entire library</span><br /><code>FEED:DF</code> - <span class="description">Play the default folder (this will only work if your default folder setting is local)</span><br /><code>FEED:/mymusic</code> - <span class="description">Play the local folder 'mymusic' (folder paths must be local and are relative to the root of your site, NOT the Wordpress install)</span></p>
								
									<h5 style="margin:15px 0px 5px 0px;"><code>[mp3-link]</code> plays a track from a playlist player on the same page.</h5>
									<p style="margin: 0px 120px 1px 10px;">
										<code>player</code> <span class="description">number of the player (including single file players in the count)</span><br /><code>track</code> <span class="description">the track number</span><br /><code>text</code> <span class="description">link text, defaults to 'Play'</span><br /><code>bold</code> <span class="description">y/n</span></p>
								</div>
							
								<h4 class="description" style="margin:20px 0px 2px 10px; color:#606060;">Custom Fields (optional way of setting a playlist for a page/post)</h4>
								<p class="description" style="margin: 0px 120px 10px 10px;">Use custom fields when you want to add tracks to a post/page that you want to be picked up by a widget or by template tags in the theme (you can think of them as attachments but they're not really attached to anything). Fields can also be useful if you want to keep the edit window free of playlist clutter, and they make it easier to manage the track ordering (especially if you're adding captions in the playlist). Just use a shortcode in the content with no tracks specified to play custom fields, eg. <code>[mp3-jplayer]</code></p>
								<p class="description" style="margin: 0px 120px 10px 10px;">Custom fields are available on page/post edit screens (check your 'screen options' at top-right if they're not visible) for writing playlist as follows:</p> 
								<p class="description" style="margin: 0px 120px 6px 10px;">1. Enter <code>mp3</code> into the left hand box.<br />2. Write the filename, URI, or 'FEED' (see above) into the right hand box and hit 'add custom field'</p>
								<p class="description" style="margin: 0px 120px 10px 10px;">Add each track in a new field pair.</p>
								<h5 class="description" style="margin: 12px 120px 2px 10px;"><strong>Title and caption</strong></h5>
								<p class="description" style="margin: 0px 120px 5px 10px;">1. Add a dot, then the caption in the left hand box, eg: <code>mp3.Mycaption</code><br />2. Add the title, then an '@' before the filename in the right box, eg: <code>Mytitle@filename</code></p>
								<p class="description" style="margin: 10px 120px 20px 10px;">The keys (left boxes) can be numbered, eg:<code>1 mp3</code> will be first on the playlist.</p>
								
								<h4 class="description" style="margin:0px 0px 2px 10px; color:#606060;">Widgets</h4>
								<p class="description" style="margin: 0px 120px 20px 10px;">MP3j-ui - <span class="description">Adds a playlist player using tick boxes and modes (mode 1 automatically plays the custom fields).</span><br />MP3j-sh - <span class="description">Adds players by writing shortcodes.</span></p>
								
								<h4 class="description" style="margin:0px 0px 0px 10px; color:#606060;">Template Tags</h4>
								<p class="description" style="margin: 0px 120px 5px 10px;">For use in theme files:</p>
								<p style="margin: 10px 120px 20px 10px; line-height:22px;"><code style="font-size:13px;">mp3j_addscripts( $style )<br />mp3j_put( $shortcodes )<br />mp3j_grab_library( $format )<br/>mp3j_debug()</code></p>
						<?php
						echo '<p class="description" style="margin: 15px 120px 20px 10px;">See the <a href="' . get_bloginfo('wpurl') . '/wp-content/plugins/mp3-jplayer/template-tag-help.htm">Template tag help</a> for more info.</p>';
						?>
						<h4 class="description" style="margin:55px 0px -22px 0px; color:#555;">Settings</h4>
					</div>
					
					<p style="margin:0 0 8px 0px;">&nbsp; Initial volume &nbsp; <input type="text" style="text-align:center;" size="2" name="mp3foxVol" value="<?php echo $theOptions['initial_vol']; ?>" /> &nbsp; <span class="description">(0 - 100)</span></p>
					<p style="margin:0 0 4px 0px;">&nbsp; <input type="checkbox" name="mp3foxAutoplay" value="true" <?php if ($theOptions['auto_play'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Autoplay</p>
					<p style="margin:0 0 4px 0px;">&nbsp; <input type="checkbox" name="mp3foxPlaylistRepeat" value="true" <?php if ($theOptions['playlist_repeat'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Repeat</p>
					<p style="margin:0 0 4px 0px;">&nbsp; <input type="checkbox" name="mp3foxShowPlaylist" value="true" <?php if ($theOptions['playlist_show'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Start with playlists showing</p>
					<p style="margin:0 0 16px 0px;">&nbsp; <input type="checkbox" name="mp3foxDownloadMp3" value="true" <?php if ($theOptions['show_downloadmp3'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Display a 'Download mp3' link</p>
					<div style="height:65px"><p style="width:55px; padding-left:35px; margin:0px; line-height:27px;">Width:<br />Align:</p></div>
					<p style="margin:-67px 0px 0px 90px;">
						<input type="text" style="width:75px;" name="mp3foxPlayerWidth" value="<?php echo $theOptions['player_width']; ?>" />
						&nbsp; <span class="description" style="line-height:32px;">pixels (px) or percent (%)</span></p>
					<p style="margin:0px 0px 15px 90px; line-height:32px;"><select name="mp3foxFloat" style="width:94px; font-size:11px; line-height:16px;">
							<option value="none" <?php if ( 'none' == $theOptions['player_float'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Left</option>
							<option value="rel-C" <?php if ( 'rel-C' == $theOptions['player_float'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Centre</option>
							<option value="rel-R" <?php if ( 'rel-R' == $theOptions['player_float'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Right</option>
							<option value="left" <?php if ( 'left' == $theOptions['player_float'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Float left</option>
							<option value="right" <?php if ( 'right' == $theOptions['player_float'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Float right</option>
						</select></p>
					<p style="margin:0 0 8px 0px;">&nbsp; <input type="checkbox" name="mp3foxOnBlog" value="true" <?php if ($theOptions['player_onblog'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Show players in posts on index, archive, and search pages
						<span class="description">(doesn't affect widgets)</span></p>
					
			<?php
			$greyout_field = ( $theOptions['player_theme'] != "styleI" ) ? "background:#fcfcfc; color:#d6d6d6; border-color:#f0f0f0;" : "background:#fff; color:#000; border-color:#dfdfdf;";
			$greyout_text = ( $theOptions['player_theme'] != "styleI" ) ? "color:#d6d6d6;" : "color:#444;";
			?>
					<!-- COLOUR / STYLE -->
					<div style="margin: 0px 120px 20px 0px; border-bottom: 1px solid #e8e8e8; height: 10px;"></div>
					<div style="height:35px"><p style="width:55px; padding-left:35px; margin:0px; line-height:32px;">Players:</p></div>
					<p style="margin:-35px 0px 0px 90px; line-height:32px;"><select name="mp3foxTheme" id="player-select" style="width:94px; font-size:11px; line-height:19px;">
							<option value="styleF" <?php if ( 'styleF' == $theOptions['player_theme'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Silver</option>
							<option value="styleG" <?php if ( 'styleG' == $theOptions['player_theme'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Dark</option>
							<option value="styleH" <?php if ( 'styleH' == $theOptions['player_theme'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Text</option>
							<option value="styleI" <?php if ( 'styleI' == $theOptions['player_theme'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Custom</option>
						</select>&nbsp;
						<span id="player-csssheet" style=" <?php echo $greyout_text; ?>"> uri:</span><input type="text" id="mp3fcss" style="width:420px; <?php echo $greyout_field; ?>" name="mp3foxCustomStylesheet" value="<?php echo $theOptions['custom_stylesheet']; ?>" /></p>
					
					<p class="description" style="margin:1px 0px 0px 35px;"><a href="javascript:mp3jp_listtoggle('fox_styling','colour settings');" id="fox_styling-toggle">Colour settings</a></p>
					<div id="fox_styling-list" style="position:relative; display:none; margin: 30px 120px 15px 25px; min-width:579px;">
							
							<div style="position:relative; width:579px; height:20px; padding-top:2px; border-top:1px solid #eee; border-bottom:1px solid #eee;">
								<div style="float:left; width:90px; margin-left:9px;"><p class="description" style="margin:0px;"><strong>AREA</strong></p></div> 
								<div style="float:left; width:390px;"><p class="description" style="margin:0px;">&nbsp;Opacity&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;Colour</p></div>
							</div>
							
							<div style="position:relative; width:579px; padding-top:6px;">
								<div style="float:left; width:90px; margin-left:9px; border:0px solid #aaa;"><p style="margin:0px;line-height:32px;">Screen:<br />Loading bar:<br />Position bar:<br />Playlist:</p></div> 
								<div style="float:left; width:390px; border:0px solid #aaa;">
									<p style="margin:0px;line-height:32px;">
										<input type="text" size="4" name="mp3foxScreenOpac" value="<?php echo $current_colours['screen_opacity']; ?>" />
										&nbsp;&nbsp;<input type="text" id="opA" onkeyup="udfcol('opA','blA');" size="10" name="mp3foxScreenColour" value="<?php echo $current_colours['screen_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opA','blA');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opA');" id="blA" style="background:<?php echo $current_colours['screen_colour']; ?>;">&nbsp;&nbsp;</span>
										<br />
										<input type="text" size="4" name="mp3foxLoadbarOpac" value="<?php echo $current_colours['loadbar_opacity']; ?>" />
										&nbsp;&nbsp;<input type="text" id="opB" onkeyup="udfcol('opB','blB');" size="10" name="mp3foxLoadbarColour" value="<?php echo $current_colours['loadbar_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opB','blB');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opB');" id="blB" style="background:<?php echo $current_colours['loadbar_colour']; ?>;">&nbsp;&nbsp;</span>
										<br />
										<input type="text" size="4" name="mp3foxPosbarOpac" value="<?php echo $current_colours['posbar_opacity']; ?>" />
										&nbsp;&nbsp;<input type="text" id="opC" onkeyup="udfcol('opC','blC');" size="10" name="mp3foxPosbarColour" value="<?php echo $current_colours['posbar_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opC','blC');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opC');" id="blC" style="background:<?php echo $current_colours['posbar_colour']; ?>;">&nbsp;&nbsp;</span>
										&nbsp; &nbsp;<select name="mp3foxPosbarTint" style="width:115px; font-size:11px;">
											<option value="" <?php if ( '' == $current_colours['posbar_tint'] ) { _e('selected="selected"', $this->textdomain ); } ?>>(default)</option>
											<option value="soften" <?php if ( 'soften' == $current_colours['posbar_tint'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Light grad</option>
											<option value="softenT" <?php if ( 'softenT' == $current_colours['posbar_tint'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Tip</option>
											<option value="darken" <?php if ( 'darken' == $current_colours['posbar_tint'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Dark grad</option>
											<option value="none" <?php if ( 'none' == $current_colours['posbar_tint'] ) { _e('selected="selected"', $this->textdomain ); } ?>>None</option>
										</select>
										<br />
										<input type="text" size="4" name="mp3foxPlaylistOpac" value="<?php echo $current_colours['playlist_opacity']; ?>" />
										&nbsp;&nbsp;<input type="text" id="opD" onkeyup="udfcol('opD','blD');" size="10" name="mp3foxPlaylistColour" value="<?php echo $current_colours['playlist_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opD','blD');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opD');" id="blD" style="background:<?php echo $current_colours['playlist_colour']; ?>;">&nbsp;&nbsp;</span>
										&nbsp; &nbsp;<select name="mp3foxPlaylistTint" style="width:115px; font-size:11px;">
											<option value="" <?php if ( '' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $this->textdomain ); } ?>>(default)</option>
											<option value="lighten2" <?php if ( 'lighten2' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Light grad</option>
											<option value="lighten1" <?php if ( 'lighten1' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Soft grad</option>
											<option value="darken1" <?php if ( 'darken1' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Dark grad</option>
											<option value="darken2" <?php if ( 'darken2' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Darker grad</option>
											<option value="none" <?php if ( 'none' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $this->textdomain ); } ?>>None</option>
										</select>
									</p>
								</div>
								<br clear="all" />
							</div>
							
							<div id="pickerwrap">
								<div id="plugHEX"></div>
								<div id="plugCUR"></div>
								<div id="plugin" onmousedown="HSVslide('drag','plugin',event); return false;"><div id="SV" onmousedown="HSVslide('SVslide','plugin',event)"><div id="SVslide" style="top:-4px; left:-4px;"><br /></div></div><div id="H" onmousedown="HSVslide('Hslide','plugin',event)"><div id="Hslide" style="top:-7px; left:-8px;"><br /></div><div id="Hmodel"></div></div></div>
							</div>
							
							<div style="position:relative;width:175px; height:150px; margin:-200px 0px 28px 405px; padding:50px 0px 0px 0px; border:0px solid #666;">
								<p style="margin:0px 0px 8px 0px; text-align:right;">Indicator:&nbsp;
									<select name="mp3foxIndicator" style="width:80px; font-size:11px;">
										<option value="" <?php if ( '' == $current_colours['indicator'] ) { _e('selected="selected"', $this->textdomain ); } ?>>(default)</option>
										<option value="tint" <?php if ( 'tint' == $current_colours['indicator'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Greyscale</option>
										<option value="colour" <?php if ( 'colour' == $current_colours['indicator'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Colour</option>
									</select></p>
								<p style="margin:0px 0px 8px 0px; text-align:right;">Volume bar:&nbsp;
									<select name="mp3foxVolGrad" style="width:80px; font-size:11px;">
										<option value="" <?php if ( '' == $current_colours['volume_grad'] ) { _e('selected="selected"', $this->textdomain ); } ?>>(default)</option>
										<option value="light" <?php if ( 'light' == $current_colours['volume_grad'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Light</option>
										<option value="dark" <?php if ( 'dark' == $current_colours['volume_grad'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Dark</option>
									</select></p>
								<p style="margin:0px 0px 0px 0px; text-align:right;">Dividers:&nbsp;
									<select name="mp3foxListDivider" style="width:80px; font-size:11px;">
										<option value="" <?php if ( '' == $current_colours['list_divider'] ) { _e('selected="selected"', $this->textdomain ); } ?>>(default)</option>
										<option value="light" <?php if ( 'light' == $current_colours['list_divider'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Light</option>
										<option value="med" <?php if ( 'med' == $current_colours['list_divider'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Medium</option>
										<option value="dark" <?php if ( 'dark' == $current_colours['list_divider'] ) { _e('selected="selected"', $this->textdomain ); } ?>>Dark</option>
										<option value="none" <?php if ( 'none' == $current_colours['list_divider'] ) { _e('selected="selected"', $this->textdomain ); } ?>>None</option>
									</select></p>
							</div>
							
							<div style="position:relative; width:579px; height:20px; padding-top:2px; border-top:1px solid #eee; border-bottom:1px solid #eee;">
								<div style="float:left; width:90px; margin-left:9px;"><p class="description" style="margin:0px;"><strong>TEXT</strong></p></div> 
								<div style="float:left; width:430px;"><p class="description" style="margin:0px;">Colour</p></div>
								<br clear="all" />
							</div>
							
							<div style="position:relative; width:579px; padding-top:6px;">
								<div style="float:left; width:65px; margin-left:9px; border:0px solid #aaa;"><p style="margin:0px;line-height:32px;">Screen:<br />Playlist:<br />Selected:<br />Hover:</p></div>
								<div style="float:left; width:460px; border:0px solid #aaa;">
									<p style="margin:0px;line-height:32px;">
										<input type="text" id="opE" onkeyup="udfcol('opE','blE');" size="10" name="mp3foxScreenTextColour" value="<?php echo $current_colours['screen_text_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opE','blE');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opE');" id="blE" style="background:<?php echo $current_colours['screen_text_colour']; ?>;">&nbsp;&nbsp;</span>
										<br />
										<input type="text" id="opF" onkeyup="udfcol('opF','blF');" size="10" name="mp3foxListTextColour" value="<?php echo $current_colours['list_text_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opF','blF');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opF');" id="blF" style="background:<?php echo $current_colours['list_text_colour']; ?>;">&nbsp;&nbsp;</span>
										<br />
										<input type="text" id="opG" onkeyup="udfcol('opG','blG');" size="10" name="mp3foxListCurrentColour" value="<?php echo $current_colours['list_current_colour']; ?>" /> 
										<span class="addcol" onclick="putfcolour('opG','blG');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opG');" id="blG" style="background:<?php echo $current_colours['list_current_colour']; ?>;">&nbsp;&nbsp;</span>
										&nbsp; &nbsp; Background: <input type="text" id="opH" onkeyup="udfcol('opH','blH');" size="10" name="mp3foxListBGaCurrent" value="<?php echo $current_colours['listBGa_current']; ?>" />
										<span class="addcol" onclick="putfcolour('opH','blH');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opH');" id="blH" style="background:<?php echo $current_colours['listBGa_current']; ?>;">&nbsp;&nbsp;</span>
										<br />
										<input type="text" id="opI" onkeyup="udfcol('opI','blI');" size="10" name="mp3foxListHoverColour" value="<?php echo $current_colours['list_hover_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opI','blI');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opI');" id="blI" style="background:<?php echo $current_colours['list_hover_colour']; ?>;">&nbsp;&nbsp;</span>
										&nbsp; &nbsp; Background: <input type="text" id="opJ" onkeyup="udfcol('opJ','blJ');" size="10" name="mp3foxListBGaHover" value="<?php echo $current_colours['listBGa_hover']; ?>" />
										<span class="addcol" onclick="putfcolour('opJ','blJ');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opJ');" id="blJ" style="background:<?php echo $current_colours['listBGa_hover']; ?>;">&nbsp;&nbsp;</span>
									</p>
								</div>
								<br clear="all" />
							</div>
							
							<div style="position:relative; width:579px; height:20px; margin-top:30px; padding-top:2px; border-top:1px solid #eee; border-bottom:1px solid #eee;">
								<div style="float:left; width:90px; margin-left:9px;"><p class="description" style="margin:0px;"><strong>POP-OUT</strong></p></div> 
								<div style="float:left; width:430px;"><p class="description" style="margin:0px;">Background</p></div>
								<br clear="all" />
							</div>
							
							<div style="width:579px; padding-top:6px;">
								<div style="float:left; width:65px; margin-left:9px; border:0px solid #aaa;"><p style="margin:0px;line-height:32px;">Colour:<br />Image:</p></div>
								<div style="float:left; width:460px; border:0px solid #aaa;">
									<p style="margin:0px;line-height:32px;">
										<input type="text" id="opK" onkeyup="udfcol('opK','blK');"  size="10" name="mp3foxPopoutBackground" value="<?php echo $theOptions['popout_background']; ?>" />
										<span class="addcol" onclick="putfcolour('opK','blK');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opK');" id="blK" style="background:<?php echo $theOptions['popout_background']; ?>;">&nbsp;&nbsp;</span></p>
									<p style="margin:4px 0px 0px 0px;line-height:32px;">
										<input type="text" style="width:503px;" name="mp3foxPopoutBGimage" value="<?php echo $theOptions['popout_background_image']; ?>" /></p>
								</div>
								<br clear="all" />
							</div>
							<p class="description" style="margin-top: 30px; margin-bottom: 0px;">&nbsp;&nbsp;(Opacity values from 0 to 100, leave any fields blank to use the default setting)</p>
					</div><!-- close fox_styling-list	-->	
					
					<!-- MP3 FILES -->
					<div style="margin: 10px 120px 4px 0px; border-bottom: 1px solid #e8e8e8; height: 15px;"></div>
					<h4 style="margin: 14px 0px 3px 35px;">Library</h4>
			
			<?php
			// create library file list //
			$library = $this->grab_library_info();
			echo "<p class=\"description\" style=\"margin: 0px 120px 2px 35px;\">Library contains <strong>" . $library['count'] . "</strong> mp3";
			if ( $library['count'] != 1 ) { echo "'s&nbsp;"; }
			else { echo "&nbsp;"; }
			
			if ( $library['count'] > 0 ) {
				echo "<a href=\"javascript:mp3jp_listtoggle('fox_library','files');\" id=\"fox_library-toggle\">Show files</a> | <a href=\"media-new.php\">Upload new</a>";
				echo "</p>";
				echo "<div id=\"fox_library-list\" style=\"display:none;\">\n";
				$liblist = '<p style="margin-left:35px;">';
				$br = '<br />';
				$tagclose = '</p>';
				$n = 1;
				foreach ( $library['filenames'] as $i => $file ) {
					$liblist .= "<a href=\"media.php?attachment_id=" . $library['postIDs'][$i] . "&amp;action=edit\" style=\"font-size:11px;\">[Edit]</a>&nbsp;&nbsp;" . $n++ . ". " . $file . $br;
				}
				$liblist .= $tagclose;
				echo $liblist;
				echo '</div>';
			}
			else { echo "<a href=\"media-new.php\">Upload new</a></p>"; }
				
			// media settings page has moved in WP 3 //
			if ( substr(get_bloginfo('version'), 0, 1) > 2 ) // if WP 3.x //
				$mediapagelink = $this->WPinstallpath . "/wp-admin/options-media.php"; 
			else 
				$mediapagelink = $this->WPinstallpath . "/wp-admin/options-misc.php";			
			
			$upload_dir = wp_upload_dir();
			$localurl = get_bloginfo('url');
			if ( ($uploadsfolder = str_replace($localurl, "", $upload_dir['baseurl'])) != "" ) 
				//echo "<p class=\"description\" style=\"margin: 0px 120px 15px 33px;\">You only need to write filenames in your playlists to play mp3's from the library.<br />The Media Library uploads folder is currently set to <code>" .$uploadsfolder. "</code> , you can always <a href=\"" . $mediapagelink . "\">change it</a> without affecting any playlists.</p>";
				echo "<p class=\"description\" style=\"margin: 0px 120px 15px 33px;\">You just need to write filenames in playlists to play from the library.</p>";
			else
				echo "<p class=\"description\" style=\"margin: 0px 120px 15px 33px;\">You just need to write filenames in playlists to play from the library.</p>";
			?>
					<!-- Non-library -->
					<div style="margin: 0px 120px 0px 35px; border-bottom: 1px solid #eee; height: 5px;"></div>
					<h4 style="margin: 15px 0px 3px 35px;">Folder or URI</h4>
					<p class="description" style="margin: 0px 120px 0px 35px;">Set a default folder or uri for playing mp3's in the box below, eg. <code>/music</code> or <code>www.anothersite.com/music</code><br />You just need to write filenames in playlists to play from here.</p>
					<p style="margin:10px 0px 5px 35px;">Default path: &nbsp; <input type="text" style="width:385px;" name="mp3foxfolder" value="<?php echo $theOptions['mp3_dir']; ?>" /></p>
			
			<?php 
			// create file-list if directory is local
			$n = 1;
			$folderuris = $this->grab_local_folder_mp3s( $theOptions['mp3_dir'] );
			if ( is_array($folderuris) ){
				foreach ( $folderuris as $i => $uri ) {
					$files[$i] = strrchr( $uri, "/" );
					$files[$i] = str_replace( "/", "", $files[$i] );
				}
				$c = count($files);
				echo "<p class=\"description\" style=\"margin: 0px 0px 14px 142px;\">This folder contains <strong>" . $c . "</strong> mp3";
				if ( $c != 1 ) { echo "'s&nbsp;"; }
				else { echo "&nbsp;"; }
				if ( $c > 0 ) {
					echo "<a href=\"javascript:mp3jp_listtoggle('fox_folder','files');\" id=\"fox_folder-toggle\">Show files</a></p>";
					echo "<div id=\"fox_folder-list\" style=\"display:none;\">\n<p style=\"margin-left:35px;\">";
					natcasesort($files);
					foreach ( $files as $i => $val ) {
						echo $n++ . ". " . $val . "<br />";
					}
					echo "</p>\n</div>\n";
				}
				else { echo "</p>";	}
			}
			elseif ( $folderuris == true )
				echo "<p class=\"description\" style=\"margin: 0px 0px 14px 142px;\">Unable to read or locate the folder <code>" . $theOptions['mp3_dir'] . "</code> check the path and folder permissions</p>";
			else 
				echo "<p class=\"description\" style=\"margin: 0px 0px 14px 142px;\">No info is available on remote folders but you can play from here if you know the filenames</p>"; 
			?>						
					<!-- Advanced Settings -->
					<div style="margin: 0px 120px 4px 0px; border-bottom: 1px solid #eee; height: 15px;"></div>
					<h4 style="margin-top: 0px; margin-bottom: 6px; color:#555;">More options<span style="font-size:11px;">&nbsp;<a href="javascript:mp3jp_listtoggle('fox_tools','');" id="fox_tools-toggle">Show</a></span></h4>
					<div id="fox_tools-list" style="display:none; margin-left:30px;">	
						<!--
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="giveShortcodePriority" value="true" <?php //if ($theOptions['give_shortcode_priority'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Give shortcodes priority over widget</p>
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxSmallPlayer" value="true" <?php //if ($theOptions['use_small_player'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Use smaller fonts in widget</p>
						-->
						<p style="margin-top: 12px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxAddTrackNumbers" value="true" <?php if ($theOptions['add_track_numbering'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Number the tracks</p>
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxAnimSliders" value="true" <?php if ($theOptions['animate_sliders'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Animate sliders</p>
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxEncodeFiles" value="true" <?php if ($theOptions['encode_files'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Encode URI's and filenames</p>
						<p style="margin: 0px 0px 10px 8px;">Max playlist height &nbsp; <input type="text" size="6" style="text-align:center;" name="mp3foxMaxListHeight" value="<?php echo $theOptions['max_list_height']; ?>" /> px<br /><span class="description" style="margin-left:27px;">(a scroll bar will show for longer playlists, leave it blank for no limit)</span></p>
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxAtoZ" value="true" <?php if ($theOptions['playlist_AtoZ'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Playlist the tracks in alphabetical order</p>
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxUseLibrary" value="true" <?php if ($theOptions['playlist_UseLibrary'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Always use Media Library titles and excerpts when they exist</p>
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxAllowRemote" value="true" <?php if ($theOptions['allow_remoteMp3'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Allow mp3's from other domains on the players' playlists<br /><span class="description" style="margin-left:34px;">(unchecking this option doesn't affect mp3's playing from a remote default folder if one is set above)</span></p>
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxHideExtension" value="true" <?php if ($theOptions['hide_mp3extension'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Hide '.mp3' extension if a filename is displayed<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(filenames are displayed when there's no available titles)</span></p>
						<h4 style="margin-top: 20px; margin-bottom: 7px;">Margins</h4>
						<p style="margin: 0px 0px 4px 34px;">Above players &nbsp; <input type="text" size="5" style="text-align:center;" name="mp3foxPaddings_top" value="<?php echo $theOptions['paddings_top']; ?>" /> <span class="description">&nbsp; pixels (px) or percent (%)</span></p>
						<p style="margin: 0px 0px 4px 34px;">Inner margin (floated players) &nbsp; <input type="text" size="5" style="text-align:center;" name="mp3foxPaddings_inner" value="<?php echo $theOptions['paddings_inner']; ?>" /> <span class="description">&nbsp; pixels (px) or percent (%)</span></p>
						<p style="margin: 0px 0px 10px 34px;">Below players &nbsp; <input type="text" size="5" style="text-align:center;" name="mp3foxPaddings_bottom" value="<?php echo $theOptions['paddings_bottom']; ?>" /> <span class="description">&nbsp; pixels (px) or percent (%)</span></p>
						<h4 style="margin-top: 20px; margin-bottom: 8px;">Pop-out</h4>
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxEnablePopout" value="true" <?php if ($theOptions['enable_popout'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp; Enable the pop-out player</p>
						<p style="margin: 10px 0px 5px 34px;">Window width &nbsp; <input type="text" size="4" style="text-align:center;" name="mp3foxPopoutWidth" value="<?php echo $theOptions['popout_width']; ?>" /> px <span class="description">&nbsp; (250 - 1600)</span></p>
						<p style="margin: 0px 0px 10px 34px;">Window max height &nbsp; <input type="text" size="4" style="text-align:center;" name="mp3foxPopoutMaxHeight" value="<?php echo $theOptions['popout_max_height']; ?>" /> px <span class="description">&nbsp; (200 - 1200)<br />(a scroll bar will show for longer playlists)</span></p>
						<p style="margin: 0px 0px 10px 34px;">Launch button text &nbsp; <input type="text" style="width:200px;" name="mp3foxPopoutButtonText" value="<?php echo $theOptions['popout_button_title']; ?>" /></p>
						<h4 style="margin-top: 20px; margin-bottom: 8px;">Template</h4>
						<p style="margin-top:0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxUseFixedCSS" value="true" <?php if ($theOptions['use_fixed_css'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp;Bypass colour settings<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(colours can still be set in css)</span></p>
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="disableTemplateTag" value="true" <?php if ($theOptions['disable_template_tag'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp;Bypass player template-tags in theme files<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(ignores mp3j_addscripts() and mp3j_put() template functions)</span></p>
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxEchoDebug" value="true" <?php if ($theOptions['echo_debug'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp;Turn on debug<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(info appears in the source view near the bottom)</span></p>
						<!--
						<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxForceScripts" value="true" <?php //if ($theOptions['force_scripts_from_admin'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /> &nbsp;Enqueue player scripts on all site pages<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(normally scripts are only requested when they're needed by a page or widget)</span></p>
						-->
					</div><br /><br />	
					<p style="margin-top: 4px;"><input type="submit" name="update_mp3foxSettings" class="button-primary" value="<?php _e('Update Settings', $this->textdomain ) ?>" /> &nbsp; Remember settings if plugin is deactivated &nbsp;<input type="checkbox" name="mp3foxRemember" value="true" <?php if ($theOptions['remember_settings'] == "true") { _e('checked="checked"', $this->textdomain ); }?> /></p>
					<input type="hidden" name="mp3foxPluginVersion" value="<?php echo $this->version_of_plugin; ?>" />
				</form>
				<a name="howto"></a><br />				
				<div style="margin: 15px 120px 25px 0px; border-top: 1px solid #999; height: 30px;"><p class="description" style="margin: 0px 120px px 0px;"><a href="http://sjward.org/jplayer-for-wordpress">Plugin home page</a></p></div>
			</div>
		<?php
		}
	} // end class	
}
?>