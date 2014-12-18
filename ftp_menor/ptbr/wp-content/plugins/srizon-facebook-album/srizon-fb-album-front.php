<?php
function srz_fb_enqueue_script(){
	wp_enqueue_script('jquery');
	wp_enqueue_script('srzsb',  srz_fb_get_resource_url('js/sb/srzsb.js'),array('jquery'));
	wp_enqueue_script('srzmp',  srz_fb_get_resource_url('js/mag-popup.js'),array('jquery'));
	wp_enqueue_script('srzmpinit',  srz_fb_get_resource_url('js/srz-mag-init.js'),array('srzmp'));
	wp_enqueue_style('srzfbstyles',  srz_fb_get_resource_url('css/srzfbstyles.css'));
	wp_enqueue_style('srzsbcss',  srz_fb_get_resource_url('js/sb/srzsb.css'));
	wp_enqueue_style('srzmpcss',  srz_fb_get_resource_url('css/mag-popup.css'));
}

function srz_fb_album_shortcode($atts){
	if(!isset($atts['id'])) return 'Invalid shortcode... ID missing';
	$albumid = $atts['id'];
	$album = SrizonFBDB::GetAlbum($albumid);
	if(!$album) return 'Album Not found';
	if(isset($_GET['debugjfb'])){
		echo '<pre>';
		print_r($album);
		echo '</pre>';
	}
	if (!isset($GLOBALS['imggroup'])) $GLOBALS['imggroup'] = 1;
	else $GLOBALS['imggroup']++;
	
	$images = srz_fb_get_album_api($album['albumid'], $album['shuffle_images'],$album['updatefeed']*60);
	//$images = array_slice($images_all,0,$album['totalimg']);
	$common_options = SrizonFBDB::GetCommonOpt();
	if($common_options['loadlightbox'] == 'yes'){
		$common_options['lightboxattrib'] = 'data-srizonsb="srzlightbox-'.$GLOBALS['imggroup'].'"';
	}
	else if($common_options['loadlightbox'] == 'mp'){
		$common_options['secondclass'] = ' mpjfb';
		$common_options['lightboxattrib'] = '';
	}
	else{
		$common_options['lightboxattrib'] = stripslashes($common_options['lightboxattrib']);
	}
	$output = srz_fb_render_fullpage($album,$images,$common_options);
	/*switch($album['liststyle']){
		case 'horizontal':
			$output = srz_fb_render_fullpage($album,$images,$common_options);
			break;
		case 'slidergrid':
			$output = render_slidergrid($album,$images,$common_options);
			break;
		case 'slidergridv':
			$output = render_slidergridv($album,$images,$common_options);
			break;
		default:
			break;
	}*/
	return $output;
}

function srz_fb_render_fullpage($album,$images,$common_options){
	$output = '';
	$output.='<div class="jfbalbum'.$common_options['secondclass'].'" id="jfbalbum-'.$GLOBALS['imggroup'].'">';
	$totimg = count($images);
	$getjfpage = isset($_GET['jfpage'])?$_GET['jfpage']:0;
	$jf_start = $getjfpage*$album['paginatenum'];
	$jf_end = min($totimg,($jf_start+$album['paginatenum']));
	$imgboxclass = ($album['tpltheme']=='white')? 'imgboxblack':'imgboxwhite';
	for($j=$jf_start;$j<$jf_end;$j++){
		$image = $images[$j];
		$output.= '<div class="imgboxouter">';
		if($album['thumbwidth']>180 or $album['thumbheight']>120)	{
			$thumb_img = $image['src'];
			$last_slash = strrpos($thumb_img, '/');
			$part1 = substr($thumb_img, 0,$last_slash);
			$part2 = substr($thumb_img,$last_slash);
			$thumb_img = $part1.'/p206x206'.$part2;
		}
		else{
			$thumb_img = str_replace('_n.jpg', '_a.jpg', $image['src']);
		}

		$link = '<a class="aimg" style="width:'.$album['thumbwidth'].'px; height:'.$album['thumbheight'].'px;" href="'. $image['src'].'" title="'.  nl2br($image['txt']).'" '.$common_options['lightboxattrib'].'>';
		$imgcode = 'style="width:'.$album['thumbwidth'].'px; height:'.$album['thumbheight'].'px; background-image: url('.$thumb_img.');"';
		$output.= $link.'<div class="'.$imgboxclass.'" '.$imgcode.'></div></a>';
		$output.= '</div>';
	}
	$totalpages = ceil($totimg/$album['paginatenum']);
	if($totalpages > 1){
		$url = $_SERVER['REQUEST_URI'];//get_page_link();
		if($jfpos = strpos($url,'jfpage')){
			$url=substr($url,0,$jfpos-1);
		}
		$url = str_replace('&', '&amp;', $url);
		if(strpos($url,'?')) $url.='&amp;';
		else $url.='?';
		$output.= '<div id="tnt_pagination">';
		if($getjfpage>4){
			$pgstart = $getjfpage-4;
			$output.= '<a href="'.$url.'jfpage=0">First</a>';
		}
		else $pgstart = 0;
		$pgend = min($totalpages,$pgstart+10);

		for($k=$pgstart;$k<$pgend;$k++){
			if($k==$getjfpage){
				$output.= '<span class="active_tnt_link">'.($k+1).'</span>';
			}
			else{
				$output.= '<a href="'.$url.'jfpage='.$k.'">'.($k+1).'</a>';
			}
		}
		if($totalpages>$pgend){
			$output.= '<a href="'.$url.'jfpage='.($totalpages-1).'">Last</a>';
		}
		$output.= '</div>';
	}
	$output.= '<div style="clear:both; height:1px;"></div> </div>';
	return $output;
}

function srz_fb_extract_ids($lines){
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

function srz_fb_get_album_api($albumids, $shuffle_images, $cachetime){
	$albumids_arr = srz_fb_extract_ids($albumids);

	if(count($albumids_arr)==0 && isset($_GET['debugjfb'])){
		echo 'No valid AlbumID found. Please check the corresponding parameter.';
	}

	$images = array();
	$i=0;
	foreach($albumids_arr as $albumid){
		$contents = get_transient(md5($albumid));
		if(!$contents or isset($_GET['forcesync'])){
			$url = 'http://graph.facebook.com/'.$albumid.'/photos?fields=picture';
			$contents = srz_fb_remote_to_data($url);
			set_transient(md5($albumid), $contents, $cachetime);
		}
		if(isset($_GET['debugjfb'])){
			echo 'Dumping Contents<pre>';
			print_r($contents);
			echo '</pre>';
		}
		if ($contents) {
			$json = json_decode($contents);
			if(is_array($json->data)){
				foreach($json->data as $obj){
					$images[$i]['src'] = $obj->picture;
					$images[$i]['src'] = str_replace('_s.jpg', '_n.jpg', $images[$i]['src']);
					$images[$i]['src'] = str_replace('_s.png', '_n.png', $images[$i]['src']);
					$images[$i]['txt'] = isset($obj->name)?$obj->name:'';
					$images[$i]['txt'] = htmlspecialchars($images[$i]['txt']);
					$i++;
				}
			}
			else{
				if(isset($_GET['debugjfb'])){
					echo 'Got empty result from facebook. Either the ID used was invalid or no photos with necessary permission exists in that album or your server has connectivity issue with facebook';
				}
			}
		}
		else{
			if(isset($_GET['debugjfb'])){
				echo 'Got empty result from facebook. Your server may have connectivity issue with facebook';
			}
		}
	}
	if(isset($_GET['debugjfb'])){
		echo '<pre>';
		print_r($images);
		echo '</pre>';
	}
	if($shuffle_images == 'yes') shuffle($images);
	return $images;
}

function srz_fb_remote_to_data($url){
	if(isset($_GET['debugjfb'])){
		echo 'getting remote data from:'.$url;
	}
	$data = @file_get_contents( $url );
	if(!$data){
		require_once (dirname(__FILE__) . '/mycurl.php');
		if(isset($_GET['debugjfb'])){
			echo "\n".'file_get_contents failed... trying wp_remote_get and curl';
		}
		$data1 = wp_remote_get($url);
		if(is_array($data1) and isset($data1['body']))
		{
			$data = $data1['body'];
		}
		else{
			$fbcurl = new SrzFBMycurl($url);
			$fbcurl->createCurl();
			$fbcurl->setUserAgent('');
			$fbcurl->setCookiFileLocation('');
			$fbcurl->setReferer('');
			$data = $fbcurl->tostring();
			if(isset($_GET['debugjfb'])){
				echo "\n".'wp_remote_get and curl failed to get the api response. either the pageid or albumid is wrong or your server is blocking all remote connection functions!';
			}
		}
	}
	return $data;
}