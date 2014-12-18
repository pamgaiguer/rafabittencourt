<?php
/*
 * This file handles the creation of a Facebook Gallery.
 * When a page containing the "magic tag" is saved, this function will:
 *   -Fetch the album from facebook
 *   -Fill in the album content between the tags, formatting it based on the parameters in the tag
 *   -Add postmeta "_fpf_album_size" with the number of items in the album (which the user can optionally reference)
 *   -Add postmeta "_fpf_album_cover" with the Facebook URL of the cover photo, if found
 *   
 * Re-saving the same page will re-fetch the album from facebook and regenerate its content again.  
 */
add_action('wp_insert_post_data', 'fpf_run_main');
function fpf_run_main($data)
{
    //Don't process REVISIONS (would result in 2 fetches per save)
    if( $data['post_type'] == 'revision')
        return $data;
        
    //Check the content for our magic tag (and parse out everything we need if found)
    $parsed_content = fpf_find_tags($data['post_content']);
    if( !$parsed_content ) return $data;
        
    //Connect to Facebook and generate the album content
    $album_content = fpf_fetch_album_content($parsed_content['aid'], $parsed_content);
    
    //Update the post we're about to save
    $data['post_content'] = $parsed_content['before'] . 
                            $parsed_content['startTag'] . 
                            $album_content['content'] . 
                            $parsed_content['endTag'] . 
                            $parsed_content['after'];
    
    //Set postmeta with the album's size and cover photo (can be optionally referenced by the user)
    //(Note: for some stupid reason, $data doesn't have the ID - we need to parse it out of the guid.)
    $post_ID = substr(strrchr($data['guid'], '='), 1);
    update_post_meta( $post_ID, '_fpf_album_size', $album_content['count'] );
    if(isset($album_content['cover'])) update_post_meta( $post_ID, '_fpf_album_cover', $album_content['cover'] );
    else                               delete_post_meta($post_ID, '_fpf_album_cover'); 

    //Done!
    return $data;
}


/**
  * Check a post's content for valid "magic tags".  If not found, return 0.  Otherwise, return:
  * $retVal['before']   //Content before the start tag
  * $retVal['after']    //Content after the end tag
  * $retVal['aid']      //The albumID parsed from the start tag
  * $retVal['startTag'] //The complete starttag
  * $retVal['endTag']   //The complete endTag
  * $retVal[....]       //Additional supported parameters found in the startTag.
  *                     //For a full list of what's available see fpf_fetch_album_content().
  */
function fpf_find_tags($post_content)
{ 
    //Start by splitting the content at startTag, and check for "none" or "too many" occurrences
    global $fpf_identifier;
    $result = preg_split("/(\<!--[ ]*".$fpf_identifier."[ ]*?([\d_-]+).*?--\>)/", $post_content, -1, PREG_SPLIT_DELIM_CAPTURE );
    if( count($result) < 4 )            //No tags found
        return 0;
    if( count($result) > 4 )            //Too many tags found
    {
        echo "Sorry, this plugin currently supports only one Facebook gallery per page.<br />";
        return 0;
    }
    $retVal = Array();
    $retVal['before']   = $result[0];
    $retVal['startTag'] = $result[1];
    $retVal['aid']      = $result[2];
    $retVal['after']    = $result[3];
    
    //Now search the remaining content and split it at the endTag, again checking for "none" or "too many"
    $result = preg_split("/(\<!--[ ]*\/".$fpf_identifier."[ ]*--\>)/", $retVal['after'], -1, PREG_SPLIT_DELIM_CAPTURE);
    if( count($result) < 3 )
    {
        echo "Missing gallery end-tag.<br />";
        return 0;
    }
    if( count($result) > 3 )
    {
        echo "Duplicate gallery end-tag found.<br />";
        return 0;
    }
    $retVal['endTag'] = $result[1];
    $retVal['after']  = $result[2];
    
    //Check for optional params in the startTag:
    if( preg_match('/cols=(\d+)/', $retVal['startTag'], $matches) )     $retVal['cols']     = $matches[1];
    if( preg_match('/start=(\d+)/', $retVal['startTag'], $matches) )    $retVal['start']    = $matches[1];
    if( preg_match('/max=(\d+)/', $retVal['startTag'], $matches) )      $retVal['max']      = $matches[1];
    if( preg_match('/swapHead=(\d+)/', $retVal['startTag'], $matches) ) $retVal['swapHead'] = $matches[1]?true:false;
    if( preg_match('/hideHead=(\d+)/', $retVal['startTag'], $matches) ) $retVal['hideHead'] = $matches[1]?true:false;
    if( preg_match('/hideCaps=(\d+)/', $retVal['startTag'], $matches) ) $retVal['hideCaps'] = $matches[1]?true:false;
    if( preg_match('/noLB=(\d+)/', $retVal['startTag'], $matches) )     $retVal['noLB']     = $matches[1]?true:false;
    if( preg_match('/hideCred=(\d+)/', $retVal['startTag'], $matches) ) $retVal['hideCred'] = $matches[1]?true:false;
    if( preg_match('/rand=(\d+)/', $retVal['startTag'], $matches) )     $retVal['rand']     = $matches[1];
    if( preg_match('/orderby=(\w+)/', $retVal['startTag'], $matches) )  $retVal['orderby']  = $matches[1];
    return apply_filters('fpf_parse_params', $retVal);
}



/**
  * Given a Facebook AlbumID, fetch its content and return:
  * $retVal['content'] - The generated HTML content we'll use to display the album
  * $retVal['cover']   - The Facebook album's cover photo (if set)
  * $retVal['count']   - The number of SHOWN photos in the album
  * 
  * $params is a array of extra options, parsed from the startTag by fpf_find_tags().
  * For a list of supported options and their meanings see the $defaults array below.   
  */
function fpf_fetch_album_content($aid, $params)
{
    //Combine optional parameters with default values
    global $fpf_homepage;
    $defaults = array('cols'    => 4,               //Number of columns of images (aka Number of images per row)
                      'start'   => 0,               //The first photo index to show (aka skip some initially)
                      'max'     => 99999999999,     //The max number of items to show
                      'swapHead'=> false,           //Swap the order of the 2 lines in the album header?
                      'hideHead'=> false,           //Hide the album header entirely?
                      'hideCaps'=> false,           //Hide the per-photo captions on the main listing?
                      'noLB'    => false,           //Suppress outputting the lightbox javascript?
                      'hideCred'=> false,           //Omit the "Generated by Facebook Photo Fetcher" footer (please don't :))
                      'rand'    => false,           //Randomly select n photos from the album (or from photos between "start" and "max")
                      'orderby' => 'normal');       //Can be "normal" or "reverse" (for now)
    $params = array_merge( $defaults, $params );
    $itemwidth = $params['cols'] > 0 ? floor(100/$params['cols']) : 100;
    $itemwidth -= (0.5/$params['cols']); //For stupid IE7, which rounds fractional percentages UP (shave off 0.5%, or the last item will wrap to the next row)
    $retVal = Array();
    
    //Get our saved access token (and make sure it exists) 
    global $fpf_opt_access_token;
    $access_token = get_option($fpf_opt_access_token);
    if(!$access_token)
    {
        $retVal['content'] = 'This plugin does not have a valid Facebook access token.  Please use your admin panel to login with Facebook.';
        return $retVal;
    }
    
    //Try to fetch the album object from Facebook, and check for common errors.
    $album = fpf_get("https://graph.facebook.com/$aid?access_token=$access_token&fields=id,cover_photo,count,link,name,from,created_time,description");
    if(!$album || isset($album->error))
    {
        if(!$album)                       $retVal['content'] = "An unknown error occurred while trying to fetch the album (empty reply).";
        else if($album->error->code==190) $retVal['content'] = "Error 190: Invalid OAuth Access Token.  Try using the admin panel to re-validate your plugin.";
        else if($album->error->code==803) $retVal['content'] = "Error 803: Your album id doesn't appear to exist.";
        else if($album->error->code==100) $retVal['content'] = "Error 100: Your album id doesn't appear to be accessible.";
        return $retVal;
    }
    if(!isset($album->id) || $album->id != $aid)
    {
        $retVal['content'] = "An unknown error occurred while trying to fetch the album (id mismatch).";
        return $retVal;
    }
    if(!isset($album->cover_photo) || $album->id != $aid)
    {
        $retVal['content'] = "An error occurred while trying to fetch the album: the ID specified does not appear to be an album.";
        return $retVal;
    }
    if($album->count == 0)
    {
        $retVal['content'] = "An error occurred while trying to fetch the album: it appears to be empty.";
        return $retVal;
    }
    
    //Now that we know the album is OK, try to fetch its photos and run some checks on them.
    $photos = fpf_get("https://graph.facebook.com/$aid/photos?access_token=$access_token&limit=999&fields=name,source,picture");
    if(!$photos || !isset($photos->data))
    {
        $retVal['content'] = "An unknown error occurred while trying to fetch the photos (empty data).";
        return $retVal;
    }
    if(count($photos->data) != $album->count)
    {
        $retVal['content'] = "<i>Warning: A size mismatch error occurred while trying to fetch the photos (the album reported $album->count entries, but only " . count($photos->data) . " were returned).</i><br />";
        //$retVal['content'] .= "Album:\n\nhttps://graph.facebook.com/$aid?access_token=$access_token&fields=id,cover_photo,count,link,name,from,created_time,description\n\n";
        //$retVal['content'] .= "Photos:\n\nhttps://graph.facebook.com/$aid/photos?access_token=$access_token&limit=999&fields=name,source,picture\n\n";
    }
    $photos = $photos->data;
    
    //Run filters so we can modify the album and photo data
    $album = apply_filters('fpf_album_data', $album );
    $photos = apply_filters('fpf_photos_presort', $photos );
    
    //Store the filename of the album cover
    //We must do this here, prior to slicing down the array of photos.
    if( isset($album->cover_photo) )
    {
        foreach($photos as $photo)
        {
            if( strcmp($photo->id, $album->cover_photo) == 0 )
                $retVal['cover'] = $photo->source;
        }
    }
    
    //Reorder the photos if necessary
    if( $params['orderby'] == 'reverse' )
    {
        $photos = array_reverse($photos);
    }
    
    //Slice the photo array as necessary
    if( count($photos) > 0 )
    {
        //Slice the photos between "start" and "max"
        if( $params['start'] > $album->count )
        {
            $retVal['content'] .= "<b>Error: Start index ". $params['start']." is greater than the total number of photos in this album; Defaulting to 0.</b><br /><br />";
            $params['start'] = 0;
        }
        if( $params['max'] > $album->count - $params['start'] )
            $params['max'] = $album->count - $params['start'];
        $photos = array_slice($photos, $params['start'], $params['max']); 
        
        //If "rand" is specified, randomize the order and slice again
        if( $params['rand'] )
        {
            shuffle($photos);
            $photos = array_slice($photos, 0, $params['rand']);
        }
    }
    
    //Run a filter so addons can modify/process the photos
    $photos = apply_filters('fpf_photos_postsort', $photos );
    
    //Create a header with some info about the album
    $retVal['count'] = count($photos);
    if(!$params['hideHead'])
    {
        $headerTitle  = 'From <a href="' . htmlspecialchars($album->link) . '">' . $album->name . '</a>';
        if( isset($album->from->id) && isset($album->created_time) )
        {
            $headerTitle .= ', posted by <a href="http://www.facebook.com/profile.php?id=' . $album->from->id . '">' . $album->from->name . '</a>';
            $headerTitle .= ' on ' . date('n/d/Y', strtotime($album->created_time));
        }
        if( $retVal['count'] < $album->count) $headerTitle .= ' (Showing ' . $retVal['count'] . ' of ' . $album->count . " items)\n";
        else                                  $headerTitle .= ' (' . $retVal['count'] . " items)\n";
        $headerTitle .= '<br /><br />';            
        if( $album->description ) $headerDesc = '"'.$album->description.'"<br /><br />'."\n";
        else                      $headerDesc = "";
    } 

    //Output the album!  Starting with a (hidden) timestamp, then the header, then each photo.
    global $fpf_version;
    $retVal['content'] .= "<!-- ID ". $aid ." Last fetched on " . date('m/d/Y H:i:s') . " v$fpf_version-->\n";
    if( $params['swapHead'] )   $retVal['content'] .= $headerTitle . $headerDesc;
    else                        $retVal['content'] .= $headerDesc . $headerTitle; 
    $retVal['content'] .= "<div class='gallery'>\n";
    foreach($photos as $photo)
    {
        //Strip [], or WP will try to run it as shortcode
        $caption = preg_replace("/\[/", "(", $photo->name);
        $caption = preg_replace("/\]/", ")", $caption);
        
        //Strip emoji.
        //Emoji come from FB as surrogate pairs (like "\udbb8\udf2c"), which get converted to UTF8 when we json_decode() the string (see http://stackoverflow.com/questions/17445901/replace-iphone-emoji-in-html-page)
        //First, strip these (http://apps.timwhitlock.info/emoji/tables/unicode) (Code from: http://stackoverflow.com/questions/12807176/php-writing-a-simple-removeemoji-function)
        $caption = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $caption);
        $caption = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $caption);
        $caption = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $caption);
        //And here are some more (This was the range that was messing up Mark Laurich's albums: https://github.com/adamrocker/Japanese-Mobile-Emoji/blob/master/EmojiData.csv)
        $caption = preg_replace('/[\x{FE000}-\x{FE4E4}]/u', '', $caption); 

        //Output this photo
        $caption = preg_replace("/\r/", "", $caption);
        $caption_with_br = htmlspecialchars(preg_replace("/\n/", "<br />", $caption));
        $caption_no_br = htmlspecialchars(preg_replace("/\n/", " ", $caption));
        if ($caption_with_br != '')
            $link = '<a rel="' . htmlspecialchars($album->link) . '" class="fbPhoto" href="'.$photo->source . '" title="'.$caption_with_br.' " ><img src="' . $photo->picture . '" alt="" /></a>';
        else
            $link = '<a rel="' . htmlspecialchars($album->link) . '" class="fbPhoto" href="'.$photo->source . '"><img src="' . $photo->picture . '" alt="" /></a>';
        $retVal['content'] .= "<dl class='gallery-item' style=\"width:$itemwidth%\">";
        $retVal['content'] .= "<dt class='gallery-icon'>$link</dt>";
        if(!$params['hideCaps'])
        {
            $retVal['content'] .= "<dd class='gallery-caption'>";
            $retVal['content'] .= mb_substr($caption_no_br,0, 85) . (strlen($caption_no_br)>85?"...":"");
            $retVal['content'] .= "</dd>";
        }
        $retVal['content'] .= "</dl>\n";
        
        //Move on to the next row?
        if( $params['cols'] > 0 && ++$i % $params['cols'] == 0 ) $retVal['content'] .= "<br style=\"clear: both\" />\n\n";
    }
    if( $i%$params['cols'] != 0 ) $retVal['content'] .= "<br style=\"clear: both\" />\n\n";
    $retVal['content'] .= "</div>\n";
    if( !$params['hideCred'] )    $retVal['content'] .= "<span class=\"fpfcredit\">Generated by <i>Facebook Photo Fetcher 2</i></span>\n";
    
    //Activate the lightbox when the user clicks a photo (only if the Lightbox plugin isn't already there)
    if( !$params['noLB'] && !function_exists('lightbox_2_options_page') )
    {
        $retVal['content'] .= '<script type="text/javascript">//<!--
		jQuery(document).ready(function() {
			jQuery("a[rel*=\''.$aid.'\']").fancybox({
				"transitionIn"	: "elastic",
				"transitionOut"	: "elastic",
				"titlePosition" : "inside",
				"titleFormat"	: function(title, currentArray, currentIndex, currentOpts)
				{
					return "<span id=\'fancybox-title-over\' style=\'background-image:none; text-align:left;\'>" + (title.length ? title : "") + "</span>";
				}
			});
		});'.
        "\n//--></script>\n";
    }
    $retVal['content'] .= "<!-- End Album ". $aid ." -->\n";
    return $retVal;
}


?>