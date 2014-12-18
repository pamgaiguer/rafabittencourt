<?php
/*
Plugin Name: WP Facebook grabber
Plugin URI: http://www.entula.net/wordpress-facebook-grabber/
Description: WP Facebook grabber allows you to grab facebook album and add it to a post or a page.
Author: http://www.borraccetti.it/borraccetti
Version: 4
 * Examples and documentation at: http://www.entula.net/wordpress-facebook-grabber/
 * Home: http://www.entula.net/wordpress-facebook-grabber/
 * 
 * Copyright (c) 2010 Fabio Borraccetti
 *
 * Version: v4.0 30-8-2012
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * 
 * Tested and Developer with php 5
 * Requires: php 5 or later
 * 
 *
 */ 
function wpfbAgAddCSS(){
	echo '<link rel="stylesheet" type="text/css" href="'.get_option('siteurl').'/wp-content/plugins/wordpress-facebook-grabber/wp-fb-grabber.css" />';
}
add_action('wp_print_styles', 'wpfbAgAddCSS');

function mycurl_get_content($url)
{
    $ch = curl_init();

    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_HEADER, 0);

    ob_start();

    curl_exec ($ch);
    curl_close ($ch);
    $string = ob_get_contents();

    ob_end_clean();
   
    return $string;    
}

function mygetRemoteFile($url){
	$fp = fopen( $url, 'r' );
    $content = "";
    while( !feof( $fp ) ) {
       $buffer = trim( fgets( $fp, 4096 ) );
       $content .= $buffer;
    }
	return $content;
}

function fbAlbumGrabber($fburl,$album_id){
	$maxitem = get_option('max_photo');
	if(get_option('wpfbmode') != "jquery")
		if (function_exists("curl_init")) {
			$content = @mycurl_get_content($fburl);
		}else{
			$content = @file_get_contents($fburl);
		}
	if($content){
		$data = json_decode($content);
		$retval = "";
		$i=get_the_ID();
		$count=0;
		if(count($data->data)>0)
		foreach($data->data as $data->item){
			$retval .= '<div class="fb_thumb" id="fb_thumb_'.$i.'_'.$count.'">
				<a title=" '.str_replace("-"," ",sanitize_title($data->item->name)).'" href="'.htmlentities($data->item->source).'"  rel="lightbox['.$i.']" >
				<img class="wp_fb_album_grabber_img" src="'.htmlentities($data->item->picture).'" alt=" '.str_replace("-"," ",sanitize_title($data->item->name)).'" title=" '.str_replace("-"," ",sanitize_title($data->item->name)).' " />
				</a>
			</div>';
			if($count>$maxitem-2)
				break;
			$count++;
		}
		$retval .='
		<div style="text-align: right; clear:both" id="wp_fb-credits"> <small>Powered by <a href="http://www.entula.net/wordpress-facebook-grabber">Wordpress Facebook Grabber</a></small></div>';
		return $retval;
	}else{
		//					$handle,      $src,                                         $deps,          $ver, $in_footer 
		//wp_enqueue_script('newscript',  WP_PLUGIN_URL . '/wp-fb-grabber/wp-fb-grabber.js' );
		//$retval .= '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script> ';
		$retval .= '<script type="text/javascript" src="'.  WP_PLUGIN_URL . '/wordpress-facebook-grabber/wp-fb-grabber.js"></script> ';
		$retval .= '<div id="wp-fb-grabber">';
		$retval .= '</div>';
		$retval .= '<script type="text/javascript">
		jQuery().ready( function (){
			jQuery("#wp-fb-grabber").grabFBalbum({ 
				fburl: "'.$fburl.'", 
				maxitem: '.get_option("max_photo").'
			});
		});	
		</script>';
		$retval .='
		<div style="text-align: right; clear: both" id="wp_fb-credits"> <small>Powered by <a href="http://www.entula.net/wordpress-facebook-grabber">Wordpress Facebook Grabber</a></small></div>';
		$retval .='<div style="text-align: right; clear:both" target=_blank id="wp_fb-source"> <small>Original content: <a href="http://www.facebook.com/'.$album_id.'">http://www.facebook.com/'.$album_id.'</a></small></div>';

		return $retval;
	}
}
function fbFeedGrabber($fburl,$profile_id=null){
	$maxitem = get_option('max_feed');
	if(get_option('wpfbmode') != "jquery")
		if (function_exists("curl_init")) {
			$content = @mycurl_get_content($fburl);
		}else{
			$content = @file_get_contents($fburl);
		}
	if($content){
		$data = json_decode($content);
		$retval = "";
		$i=get_the_ID();
		$count=0;
		if(count($data->data)>0)
		foreach($data->data as $data->item){
			if($count != -1) {
				$id = $data->item->from;
				$retval .= '<div class="fb_feed" id="fb_feed_'.$i.'_'.$count.'" >
				<a href="'.str_replace("graph.","www.",$data->item->link).'"  target="_blank" >
				<span class="fb_from">'.$id->name.'</span>
				<p class="fb_feed_title">'.str_replace("-"," ",sanitize_title($data->item->name)).'</p>
					';
					if($data->item->picture)
					$retval .='<div class="fb_feed_thumb"><img class="wp_fb_album_grabber_img" src="'.htmlentities($data->item->picture).'" alt=" '.str_replace("-"," ",sanitize_title($data->item->name)).'" title=" '.str_replace("-"," ",sanitize_title($data->item->name)).' " /></div>';
					$desc=$data->item->message?$data->item->message:$data->item->description;
					$retval .='	
					</a>
					<div class="fb_feed_desc">
					<p>
						'.$desc.'
					</p>
					</div>
				</div>
				';
			}
			if($count>$maxitem-2)
				break;
			$count++;
		}
		$retval .='<div style="text-align: right;" id="wp_fb-credits"> <small>Powered by <a href="http://www.entula.net/wordpress-facebook-grabber">Wordpress Facebook Grabber</a></small></div>';
		return $retval;
	}else{
		//					$handle,      $src,                                         $deps,          $ver, $in_footer 
		//wp_enqueue_script('newscript',  WP_PLUGIN_URL . '/wp-fb-grabber/wp-fb-grabber.js' );
		//$retval .= '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script> ';
		$retval .= '<script type="text/javascript" src="'.  WP_PLUGIN_URL . '/wordpress-facebook-grabber/wp-fb-grabber.js"></script> ';
		$retval .= '<div id="wp-fb-grabber">';
		$retval .= '</div>';
		$retval .= '<script type="text/javascript">
			jQuery("#wp-fb-grabber").grabFBfeed({ 
				fburl: "'.$fburl.'", 
				maxitem: '.get_option("max_feed").'
			});
		</script>';
		$retval .='<div style="text-align: right; clear:both" id="wp_fb-credits"> <small>Powered by <a href="http://www.entula.net/wordpress-facebook-grabber">Wordpress Facebook Grabber</a></small></div>';
		$retval .='<div style="text-align: right; clear:both" target=_blank id="wp_fb-source"> <small>Original content: <a href="http://www.facebook.com/'.$profile_id.'">http://www.facebook.com/'.$profile_id.'</a></small></div>';
		return $retval; 
	}   
	
} 

function wpfbReplaceCallback($match) {
	
    if ( strtolower( $match[1] ) == "album" ){
        $toprint = fbAlbumGrabber("https://graph.facebook.com/$match[2]/photos?limit=".get_option('max_photo'),$match[2]);
    }else{
    	$access_token=get_fb_token();
		$app_id = get_option('app_id'); 
		$app_secret = get_option('app_secret'); 
		if($access_token==false){
			//$url = "https://graph.facebook.com/oauth/authorize?client_id=".$app_id."&redirect_uri=".$url = plugins_url()."/wordpress-facebook-grabber/oauth.php";
			$url = "https://www.facebook.com/dialog/oauth?client_id=".$app_id."&redirect_uri=http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];//."&response_type=token";
			return "<div class=\"wp_fb_grabber\"><a href='".$url."'>Login with Facebook to see the content</a> </div>";
		}
        $toprint = fbFeedGrabber("https://graph.facebook.com/$match[2]/feed"."?".$access_token,$match[2]);
	}
    return $toprint ? "<div class=\"wp_fb_grabber\">$toprint</div>" : "";
}

function get_fb_token(){
	//@session_start();
	$access_token=$_SESSION['access_token'];
	if(isset($access_token)){
		return $access_token;
	}else{
		$code=$_GET['code'];
		if(isset($code) and strlen($code)>2){
			$app_id = get_option('app_id'); 
			$app_secret = get_option('app_secret'); 
		   
		    //https://graph.facebook.com/oauth/access_token?
     	    //	client_id=YOUR_APP_ID
     	    //	&redirect_uri=YOUR_URL&
            //client_secret=YOUR_APP_SECRET
            //&code=THE_CODE_FROM_ABOVE
		    $current_url="http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$u= explode("?",$current_url);
			$token_url = "https://graph.facebook.com/oauth/access_token?client_id=".$app_id."&redirect_uri=".$u[0]."&scope=offline_access&client_secret=".$app_secret."&code=".$code;
			 
			//echo $token_url."<br>";
			$response = @file_get_contents($token_url);
			//print_r($response);
			$params = null;
			parse_str($response, $params);
			//print_r($params);
			
			$access_token = "access_token=".$params['access_token'];

			$_SESSION['access_token']=$access_token;
			return $access_token;
		}else{
			return false;
		}
		return false;
	} 
	return false;

}


function wpfbGrabber($text) {
    return preg_replace_callback('/\[fb(Album|Feed)\](.*?)\[\/fb\1\]/i', 'wpfbReplaceCallback', $text);
}

add_filter('the_content', 'wpfbGrabber');
include("option_panel.php");
?>