<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
			<title></title>
			<!-- MP3-jPlayer 1.7.2 Pop-out player -->
			<script type="text/javascript">
			<!--
			function loadjscss(filename, filetype) {
				if (filetype=="js"){
					var fileref=document.createElement('script');
					fileref.setAttribute("type","text/javascript");
					fileref.setAttribute("src", filename);
				}
				else if (filetype=="css"){ 
					var fileref=document.createElement("link");
					fileref.setAttribute("rel", "stylesheet");
					fileref.setAttribute("type", "text/css");
					fileref.setAttribute("href", filename);
				}
				if (typeof fileref!="undefined") { 
					document.getElementsByTagName("head")[0].appendChild(fileref); 
				}
			}
			
			if(window.opener && !window.opener.closed) {
				var foxpathtoswf = window.opener.foxpathtoswf;
				var foxpathtoimages = window.opener.foxpathtoimages;
				var FoxAnimSlider = window.opener.FoxAnimSlider;
				var fox_playf = "false";
				var foxPP_fixedcss = window.opener.foxPP_fixedcss;
				var info_array = window.opener.mp3j_info;
				var launched_id = window.opener.pp_playerID;
				var mp3j_info = [{ 	
					list:info_array[launched_id].list, 
					type:'MI', 
					tr:info_array[launched_id].tr, 
					lstate:info_array[launched_id].lstate, 
					loop:info_array[launched_id].loop, 
					play_txt:info_array[launched_id].play_txt, 
					pause_txt:info_array[launched_id].pause_txt, 
					pp_title:info_array[launched_id].pp_title, 
					autoplay:window.opener.pp_startplaying, 
					has_ul:1, 
					transport:'playpause', 
					status:'full', 
					download:info_array[launched_id].download, 
					vol:info_array[launched_id].vol,
					height:info_array[launched_id].height
				}];
				var popout_height = window.opener.popout_height;
				var player_height = window.opener.player_height;
				
				//css
				var foxPP_playlist_divider = window.opener.foxPP_playlist_divider;
				var foxPP_playlist_text = window.opener.foxPP_playlist_text;
				var foxPP_playlist_current = window.opener.foxPP_playlist_current;
				var foxPP_playlist_hover = window.opener.foxPP_playlist_hover;
				var foxPP_indicator_tint = window.opener.foxPP_indicator_tint;
				var foxPP_volume_grad = window.opener.foxPP_volume_grad;
				var foxPP_loader_bar_colour = window.opener.foxPP_loader_bar_colour;
				var foxPP_loader_bar_opac = window.opener.foxPP_loader_bar_opac;
				var foxPP_posbar_colour = window.opener.foxPP_posbar_colour;
				var foxPP_posbar_opac = window.opener.foxPP_posbar_opac;
				var foxPP_listBGa_hover = window.opener.foxPP_listBGa_hover;
				var foxPP_listBGa_current = window.opener.foxPP_listBGa_current;
				loadjscss( window.opener.foxPP_stylesheet, "css" );
			}
			//-->
			</script>
			
			<style type="text/css">
				span.mp3-finding, span.mp3-loading { opacity:.60; filter:alpha(opacity=60); }
				div.wrap-MI { min-width:190px; }
			</style>
						
			<script type="text/javascript" src="js/jquery.js"></script>
			<script type="text/javascript" src="js/ui.core.js"></script>
			<script type="text/javascript" src="js/ui.widget.js"></script>
			<script type="text/javascript" src="js/ui.mouse.js"></script>
			<script type="text/javascript" src="js/ui.slider.js"></script>
			<script type="text/javascript" src="js/jquery.jplayer.min.js"></script>
			<script type="text/javascript" src="js/mp3j-functions.js"></script>
			<script type="text/javascript" src="js/popout.js"></script>
			
			<script type="text/javascript">
			<!--
			jQuery(document).ready(function(){
				if ( typeof mp3j_info === "undefined" ) { 
					jQuery("body").empty();
					jQuery("body").css("background", '#333333');
					jQuery("*").css("color", '#cccccc');
					jQuery("body").append("<h4 style='margin-left:10px;'>Please launch a playlist from the site to use me,<br />I've been refreshed and can't find my parent window.</h4>");
					return; 
				}
				
				mp3j_setup();
				jQuery("#jquery_jplayer").jPlayer({
						ready: function() {
							mp3j_init();
						},
						oggSupport: false,
						volume: 100,
						swfPath: foxpathtoswf
				})
				.jPlayer("onProgressChange", function(loadPercent, playedPercentRelative, playedPercentAbsolute, playedTime, totalTime) {
					run_progress_update( loadPercent, playedPercentRelative, playedPercentAbsolute, playedTime, totalTime );
				})
				.jPlayer("onSoundComplete", function() {
					run_sound_complete();
				});	
			});
			//-->
			</script>
						
	</head>
	<body style="padding:5px 4px 0px 4px; margin:0px;">
			
			<div style="position:relative;"><div id="jquery_jplayer"></div></div>

			<div class="wrap-MI" style="position:relative; padding:0; margin:0px auto 0px auto; width:100%;">
				<div class="jp-innerwrap">
					
					<div class="innerx"></div>
					<div class="innerleft"></div>
					<div class="innerright"></div>
					<div class="innertab"></div>

					<div class="jp-interface">
						<div id="T_mp3j_0" class="player-track-title" style="left:16px;"></div>
						<div id="C_mp3j_0" class="player-artist"></div>
						<div class="MIsliderVolume" id="vol_mp3j_0"></div>
						<div class="bars_holder">
							<div class="loadMI_mp3j" id="load_mp3j_0"></div>
							<div class="posbarMI_mp3j" id="posbar_mp3j_0"></div>
						</div>
						<div class="transport-MI"><div class="buttons_mp3j" id="playpause_mp3j_0">Play Pause</div><div class="stop_mp3j" id="stop_mp3j_0">Stop</div><div class="Next_mp3j" id="Next_mp3j_0">Next&raquo;</div><div class="Prev_mp3j" id="Prev_mp3j_0">&laquo;Prev</div></div>
						<div id="P-Time-MI_0" class="jp-play-time"></div>
						<div id="T-Time-MI_0" class="jp-total-time"></div>
						<div id="statusMI_0" class="statusMI"></div>
						<div id="download_mp3j_0" class="dloadmp3-MI" style="visibility: visible;"></div>
						<div class="playlist-toggle-MI" id="playlist-toggle_0" onclick="javascript:MI_toggleplaylist('', 0);">HIDE PLAYLIST</div>
					</div>
					
				</div>
				<div class="listwrap_mp3j" id="L_mp3j_0">
					
					<div class="playlist-colour"></div>
					<div class="playlist-wrap-MI"><ul class="UL-MI_mp3j" id="UL_mp3j_0"><li></li></ul></div>
				
				</div>
			</div>

			<script type="text/javascript">
			<!--
			if(window.opener && !window.opener.closed) {				
				$("div.jp-interface").css( "height", mp3j_info[0].height+"px" );
				if ( !mp3j_info[0].download ) { 
					$("div.dloadmp3-MI").hide(); 
				}
				if ( mp3j_info[0].list.length < 2 ) { 
					$("#Prev_mp3j_0").hide();
					$("#Next_mp3j_0").hide(); 
				}
				if ( foxPP_fixedcss == "false" ) {
					$("body").css( "background" , window.opener.foxPP_bodycolour + " url('" + window.opener.foxPP_bodyimg + "')");
					$("div.player-track-title, div.player-artist, div.jp-play-time, div.jp-total-time, div.statusMI").css( "color" , window.opener.foxPP_screentext );
					$("ul.UL-MI_mp3j").css( "background" , window.opener.foxPP_playlist_img );
					$("div.playlist-colour").css({ "background" : window.opener.foxPP_playlist_colour, opacity : window.opener.foxPP_playlist_opac });
					$("div.innertab").css({ "background" : window.opener.foxPP_screen_background, opacity : window.opener.foxPP_screen_opac });
					$("div.transport-MI div").css("color", foxPP_playlist_hover );
					$("div.transport-MI div").mouseover(function () {
						 $(this).css( "color" , foxPP_playlist_current );
					});
					$("div.transport-MI div").mouseout(function () {
						 $(this).css("color", foxPP_playlist_hover );
					});
				}
				$("title").text(mp3j_info[0].pp_title);
			}
			//-->
			</script>
	</body>
</html>