/*	MP3-jPlayer popout 
	1.7 */
	
var $tid = "";
var $state = "";
var global_lp = 0;

// Prep arrays, click functions & initial text
function mp3j_setup() {	
	I_images();
	I_unwrap();
	if ( typeof mp3j_info !== "undefined" ) { I_setup_players(); }
	
	jQuery("div.MIsliderVolume .ui-widget-header").css( "background" , foxPP_volume_grad );
	jQuery("div.loadMI_mp3j").css({ "background" : foxPP_loader_bar_colour, opacity : foxPP_loader_bar_opac });
	
	var start_track = mp3j_info[0].tr;
	mp3j_info[0].tr = 0;
	change_list_classes( 0, start_track );
	mp3j_info[0].tr = start_track;
	return;
}

function mp3j_init() {
	var j;
	for ( j=0; j < mp3j_info.length; j++ ) {
		if ( mp3j_info[j].autoplay ) {
			mp3j_info[j].autoplay = false;			
			E_playpause_click( j );
			return;
		}
	}
}

function make_slider( id ) { 
	jQuery('#posbar_mp3j_'+id).slider({
		max: 1000,
		range: 'min',
		animate: FoxAnimSlider,
		slide: function(event, ui) { 
			if ( $state === "paused" ) { pause_button( id, mp3j_info[id].play_txt, mp3j_info[id].pause_txt ); }
			jQuery("#jquery_jplayer").jPlayer("playHead", ui.value*(10.0/global_lp));
			$state = "playing";
		}
	});
	jQuery("div.bars_holder .ui-widget-header").css({ "background" : foxPP_posbar_colour, opacity : foxPP_posbar_opac });
}

function change_list_classes( id, track ) {
	jQuery("#mp3j_A_"+id+"_"+mp3j_info[id].tr).removeClass("mp3j_A_current").parent().removeClass("mp3j_A_current");
	jQuery("#mp3j_A_"+id+"_"+track).addClass("mp3j_A_current").parent().addClass("mp3j_A_current");
	if ( foxPP_fixedcss == "false" ) { // assign colours 
		jQuery("ul.UL-MI_mp3j li").css( "background" , foxPP_playlist_divider );
		jQuery("ul.UL-MI_mp3j li a").css({ "color" : foxPP_playlist_text, "background" : "none" });
		write_list_hovers();		
		jQuery("ul.UL-MI_mp3j li a.mp3j_A_current").css({ "color" : foxPP_playlist_current, "background" : foxPP_listBGa_current });
	}
}

function write_list_hovers() {
	if ( foxPP_fixedcss == "false" ) { 
		jQuery("ul.UL-MI_mp3j li a").mouseover(function () {
			 $(this).css( "color" , foxPP_playlist_hover );
			 $(this).css( "background" , foxPP_listBGa_hover );
		});
		jQuery("ul.UL-MI_mp3j li a").mouseout(function () {
			 $(this).css("color", foxPP_playlist_text );
			 $(this).css( "background" , "none" );
		});
		jQuery("ul.UL-MI_mp3j li a.mp3j_A_current").mouseover(function () {
			 $(this).css( "color", foxPP_playlist_current );
			 $(this).css( "background" , foxPP_listBGa_current );
		});
		jQuery("ul.UL-MI_mp3j li a.mp3j_A_current").mouseout(function () {
			 $(this).css( "color", foxPP_playlist_current );
			 $(this).css( "background" , foxPP_listBGa_current );
		});
	}
}

function run_progress_update( loadPercent, playedPercentRelative, playedPercentAbsolute, playedTime, totalTime ) {
	if ( $tid === "" ) { return; }
	//var ppaInt = parseInt(playedPercentAbsolute, 10);
	//var lpInt = parseInt(loadPercent, 10);
	global_lp = loadPercent;
	jQuery("#load_mp3j_"+$tid).css( "width", loadPercent+"%" );
	jQuery('#posbar_mp3j_'+$tid).slider('option', 'value', playedPercentAbsolute*10);
	var dl = mp3j_info[$tid].download;
	jQuery("#T-Time-MI_"+$tid).hide();
	jQuery("#T-Time-MI_"+$tid).text(jQuery.jPlayer.convertTime(totalTime));
	jQuery("#P-Time-MI_"+$tid).text(jQuery.jPlayer.convertTime(playedTime));
	jQuery("#statusMI_"+$tid).empty();
	if (jQuery("#jquery_jplayer").jPlayer("getData", "diag.isPlaying")){ // "PLAYING"
		if (playedTime===0 && loadPercent===0){ // connecting 
			jQuery("#statusMI_"+$tid).append('<span class="mp3-finding"></span><span class="mp3-tint"></span>Connecting');
			jQuery("span.mp3-tint").css( "background" , foxPP_indicator_tint );
			if ( dl ) { jQuery("#download_mp3j_"+$tid).removeClass("whilelinks"); jQuery("#download_mp3j_"+$tid).addClass("betweenlinks"); }
		}
		if (playedTime===0 && loadPercent>0){// buffering
			jQuery("#statusMI_"+$tid).append('<span class="mp3-loading"></span><span class="mp3-tint"></span>Buffering');
			jQuery("span.mp3-tint").css( "background" , foxPP_indicator_tint );
			jQuery("#T-Time-MI_"+$tid).show();
			if ( dl ) { jQuery("#download_mp3j_"+$tid).removeClass("betweenlinks"); jQuery("#download_mp3j_"+$tid).addClass("whilelinks"); }
		} 
		if (playedTime>0){ // playing
			jQuery("#statusMI_"+$tid).append('Playing');
			jQuery("#T-Time-MI_"+$tid).show();
			if ( dl ) { jQuery("#download_mp3j_"+$tid).removeClass("betweenlinks"); jQuery("#download_mp3j_"+$tid).addClass("whilelinks"); }
		}
	} else { // "STOPPED"
		if (playedTime>0){ // paused
			jQuery("#statusMI_"+$tid).append('Paused');
			jQuery("#T-Time-MI_"+$tid).show();
			if ( dl ) { jQuery("#download_mp3j_"+$tid).removeClass("betweenlinks"); jQuery("#download_mp3j_"+$tid).addClass("whilelinks"); }
		} 
		if (playedTime===0){ 
			if(loadPercent>0){ // stopped
				jQuery("#statusMI_"+$tid).append('Stopped');
				jQuery("#T-Time-MI_"+$tid).show();
				if ( dl ) { jQuery("#download_mp3j_"+$tid).removeClass("betweenlinks"); jQuery("#download_mp3j_"+$tid).addClass("whilelinks"); }
			} else { // ready 
				jQuery("#statusMI_"+$tid).append('Ready');
			}
		}
	}
}

function MI_toggleplaylist(text, id){
	var PPwidth = jQuery(window).width();
	if ( mp3j_info[id].lstate ) {
		if ( text==="" ) { text="SHOW"; }
		jQuery("#L_mp3j_"+id).fadeOut(300);
		jQuery("#playlist-toggle_"+id).empty();
		jQuery("#playlist-toggle_"+id).append(text);
		window.resizeTo( PPwidth+24 , player_height );
		mp3j_info[id].lstate = false;
		return;
	}
	if ( !mp3j_info[id].lstate ) {
		if ( text==="" ) { text="HIDE"; }
		jQuery("#L_mp3j_"+id).fadeIn("slow");
		jQuery("#playlist-toggle_"+id).empty();
		jQuery("#playlist-toggle_"+id).append(text);
		window.resizeTo( PPwidth+24 , popout_height );
		mp3j_info[id].lstate = true;
		return;
	}			
}