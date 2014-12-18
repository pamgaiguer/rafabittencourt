<?php
/*
Plugin Name: Facebook Albums
Plugin URI: http://glamanate.com/wordpress/facebook-album/?utm_source=fbalbum_pluginspage&utm_medium=website&utm_campaign=fbalbum
Description: Facebook Albums allows you to display Facebook Albums on your WordPress site. Brought to you by <a href="http://dooleyandassociates.com/?utm_source=fbalbum&utm_medium=referral&utm_campaign=Facebook%2BAlbum">Dooley & Associates</a>
Version: 2.0.5.3
Author: Matt Glaman
Author URI: http://glamanate.com


Copyright 2012  Matt Glaman  (email : nmd.matt@gmail.com)

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
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
if (!defined('DB_NAME')) {
  header('HTTP/1.0 403 Forbidden');
  die;
}

if (class_exists('FacebookAlbum'))
  return;

if (!class_exists('Facebook'))
  include dirname( __FILE__ ).'/inc/facebook-sdk/facebook.php';

include dirname( __FILE__ ).'/inc/widget.php';


//Registers actions
register_activation_hook(__FILE__, array('FB_Album', 'activate'));
add_action('init', array('FB_Album', 'set_facebook_sdk'), 0);
add_action( 'save_post', array('FB_Album', 'save_post') );
class FB_Album {

  protected static $album_url;  //Stores the album URL in the class
  protected static $album_id;   //Stores the album ID in the class
  protected static $album_limit;  //Stores max number of photos, defaults to 200.

  //The graph API URl the plugin will access. Script replaces {ALBUM_ID} with self::$album_id
  protected static $graph_url = 'https://graph.facebook.com/{ALBUM_ID}/photos?fields=source,link,name,images,album&limit={LIMIT}';
  //Stores plugin options, leave public for widget
  public static $options;
  //Stores SDK class..sloppily for now.
  public static $facebook_sdk;


  //Activated, let us save options
  public static function activate() {
    update_option('facebookalbum', FB_Album::default_options());
  }
  /**
    * Default options to install, provides a fall back just in case.
    * Decap from move to 2.0
    */
  public static function default_options() {
    $default_options = array(
      'order'   => get_option('fbalbum_order'),
      'title'   => '',
      'size'    => get_option('fbalbum_size', '5'), //New save structure, copy in previous value, if set, else set default
      'photo_title' => 'false',
      'colorbox'  => array(
        'enabled'       => get_option('fbalbum_lightbox', 'true'), //New save structure, copy in previous value, if set, else set default
        'width'       => "false",
        'height'    => 'false',
        'transition'    => 'elastic',
        'speed'       => '350',
        'opacity'     => '0.85',
        'slideshow'     => 'false',
        'slideshow-speed' => '2300',
        'slideshow-auto'  => 'true'
      ),
      'pages' => get_option('fbalbum_pages', array()), //New save structure, copy in previous value, if set, else set default
      'app_id' => '',
      'app_secret' => '',
      'access_token' => ''
    );
    return $default_options;
  }

  /**
    * Loads our options, defaults to base options if not set.
    */
  public static function load_options() {
    self::$options = wp_parse_args( get_option('facebookalbum', self::default_options()), array(
      'order'     => '',
      'title'   => '',
      'size'    => '5',
      'photo_title'   => '',
      'colorbox'  => array(
          'enabled'     => 'true',
          'width'       => 'false',
          'height'    => 'false',
          'transition'    => 'elastic',
          'speed'     => '350',
          'opacity'   => '0.85',
          'slideshow'   => 'false',
          'slideshow-speed' => '2300',
          'slideshow-auto'  => 'true'
        ),
      'pages'   => array(),
      'app_id'  => '',
      'app_secret'  => '',
      'access_token'  => ''
      ) );
  }

  /**
    * Function to verify if we have the Facebook application ID and Secret loaded.
    */
  public static function verify_app_information() {
    //This is might get called after reset, so refresh plugin options
    self::load_options();
    if(isset(self::$options['app_id']) && !empty(self::$options['app_id']) && isset(self::$options['app_secret']) && !empty(self::$options['app_secret']))
      return true;
    return false;
  }

  //Shortcode callback
  public static function shortcode_callback( $atts ) {

    //Defaults to null URL. If no limit set, default to 200 (max album size, besides Timeline)
    extract( shortcode_atts( array(
      'url' => '', 'limit' => '200'
    ), $atts ) );
    self::_set_album_url( $url );
    self::$album_limit = $limit;
    //Sets up album HTML. Gathers data from class variables.
    return self::print_album();
  }

  public static function save_post($post_id) {
    if ( wp_is_post_revision( $post_id ) )
      return;

    $post = get_post($post_id);
    if(strpos($post->post_content, '[fbalbum ') !== FALSE) {
      preg_match("/[fbalbum [^>]*url=(.+) /", $post->post_content, $output);
      $facebook_album_url = str_replace('"', '', $output[1]);
      FB_Album::_set_album_url($facebook_album_url);
      $album_id = FB_Album::_get_album_id();
      FB_Album::clear_cache($album_id);
    }
  }

  /**
    * Builds HTML string to output album photos
    */
  public static function print_album() {
      if(!self::_get_album_id() )
        return 'Album ID was empty';
      if(!($fb = self::_get_graph_results(self::$album_limit)))
        return;

      if(!isset($fb['data']) || !$fb['data']) {
        return 'Facebook API came back with a faulty result. You may be accessing an album you do not have permissions to access.';
      }
      self::_enqueue_resources();

      $html = '<div class="facebook-album-container">';
      if(self::$options['title']) $html .= '<h2><a href="' . self::_clean_url(self::_get_album_url()) . '" target="_blank"">' . $fb['data'][0]['album']['name'] . '</a></h2>';

      //Reverse array to show oldest to newest
      if( isset(self::$options['order']) && !empty(self::$options['order']))
        $fb['data'] = array_reverse($fb['data']);

      foreach ($fb['data'] as $img) {
        $photo_title = (isset(self::$options['photo_title']) && isset($img['name'])) ? $img['name'] : '';
        //Quick workaround to fix thumbnail resolution without causing issues on current saved data.
        $thumb_size = self::$options['size'] - 1;
        $thumbnail_src_url = self::check_thumbnail_src_size_url($img, $thumb_size);

        $html .= '<div class="facebook-album-wrapper" style="float: left;margin: 2px;">';
        $html   .= '<a href="'. self::_clean_url($img['images'][1]['source']) . '" title="'. esc_attr($photo_title) .'" rel="lightbox" class="fbalbum cboxElement">';
        $html   .= '<div class="image size-'. self::$options['size'] .'" style="background-image: url(' . self::_clean_url($thumbnail_src_url) . ')">&nbsp;</div>';
        $html .= '</a>';
        $html .= '</div>';
      }
      $html .= '<div style="clear:both">&nbsp;</div>';
      if(isset(self::$options['colorbox']['enabled']) && self::$options['colorbox']['enabled']) $html .= self::build_colorbox();
      $html .="</div>";
      return $html;
  }

  /** Builds the Colorbox script initializer based off of options set
    */
  public static function build_colorbox() {
    $colorbox = self::$options['colorbox'];
    $colorbox = wp_parse_args( $colorbox, array( 'width' => '', 'height' => '', 'speed' => '', 'slideshow' => 'false') );
    $width        = $colorbox['width'];
    $height       = $colorbox['height'];
    $transition       = $colorbox['transition'];
    $speed      = $colorbox['speed'];
    $opacity      = $colorbox['opacity'];
    //$slideshow      = $colorbox['slideshow'];
    //$slideshow_speed    = $colorbox['slideshow-speed'];
    //$slideshow_auto     = $colorbox['slideshow-auto'];

    //TODO: Slideshow settings
    $colorbox_slideshow  = get_option('fbalbum_colorbox_slideshow', 'false');
    $colorbox_slideshow_speed = get_option('fbalum_colorbox_slidespeed', 2300);
    $colorbox_slideshow_auto  = get_option('fbalbum_colorbox_slideauto', 'true');

    $script = '<script>';
    $script .= ' jQuery(document).ready(function($) {';
    $script .= "  $('.cboxElement').colorbox({";
    $script .= "    rel: 'fbalbum', ";
    $script .= "    width:  '{$width}',";
    $script .= "    height: '{$height}', ";
    $script .= "    maxHeight: '90%', ";
    $script .= "    maxWidth: '90%', ";
    $script .= "    transition: '{$transition}', ";
    $script .= "    speed: '{$speed}', ";
    $script .= "    opacity: '{$opacity}', ";
    $script .= "    slideshow: {$colorbox_slideshow}, ";
    $script .= "    slideshowSpeed: '{$colorbox_slideshow_speed}',";
    $script .= "    slideshowAuto: {$colorbox_slideshow_auto},";
    $script .= '  });';
    $script .= ' });';
    $script .= '</script>';
    return $script;
  }

  public static function set_facebook_sdk() {

    self::load_options();
    if(!empty(self::$options['app_id']) && !empty(self::$options['app_secret'])) {
      self::$facebook_sdk = new Facebook(array(
            'appId'  => self::$options['app_id'],
            'secret' => self::$options['app_secret'],
      ));
      if(self::$facebook_sdk->getUser()) {
        try{

        }
        catch (FacebookApiException $e) {
          echo "<!--DEBUG: ".$e." :END-->";
          error_log($e);
        }
      }

      if(!empty(self::$options['access_token'])) {
        self::$facebook_sdk->setAccessToken(self::$options['access_token']);
        self::$facebook_sdk->setExtendedAccessToken();
        self::$options['access_token'] = FB_Album::$facebook_sdk->getAccessToken();
        update_option('facebookalbum', self::$options);
      }
    }

    //Registers the short code to be placed within posts or pages.
    add_shortcode( 'fbalbum', array('FB_Album', 'shortcode_callback') );
    //Registers options page
    add_action('admin_menu', array('FB_Album', '_setup_opt_menu'));
  }

  /**
    * Builds Facebook API string and returns JSON output
    */
  public static function _get_graph_results($limit) {

    if(self::get_api_cache())
      return self::get_api_cache();
    if(!empty(self::$facebook_sdk)) {
      try {
        $api_call = '/'.self::_get_album_id().'/photos?fields=source,link,name,images,album&limit=' . $limit;
        $album = self::$facebook_sdk->api($api_call);
        if($album['data']) {
          //We're good to go, let's cache the JSON
          self::set_api_cache($album);
        } else {
          printf('<p>Please try entering <strong>%s</strong> into your URL bar and seeing if the page loads.', 'https://graph.facebook.com' . $api_call);
        }
      } catch (FacebookApiException $e) {
        error_log($e);
        print $e;

          $album = null;
      }
      return $album;
    } else {
      //Just in case the user did not set API access through a Facebook application
      $facebook_graph_url = str_replace(array('{ALBUM_ID}', '{LIMIT}'), array(self::_get_album_id(), $limit), self::$graph_url);
      $album = self::decap_do_curl($facebook_graph_url);
      if(isset($album['data']) && !empty($album['data'])) {
        //We're good to go, let's cache the JSON
        self::set_api_cache($album);
      } else {
        printf('<p>Please try entering <strong>%s</strong> into your URL bar and seeing if the page loads.', $facebook_graph_url);
      }

      return $album;
    }
  }

  public static function decap_do_curl($uri) {
    $facebook_graph_results = null;
    $facebook_graph_url = $uri; //TODO: Add URL checking here, else error out
      //Attempt CURL
      if (extension_loaded('curl')){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $facebook_graph_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if(!$facebook_graph_results = curl_exec($ch)) {
          printf('<p>cURL Error: %1$s %2$s</p>', curl_errno($ch), curl_error($ch));
          printf('<p>Please try entering <strong>%s</strong> into your URL bar and seeing if the page loads.', $facebook_graph_url);
        }
        if(curl_errno($ch) == 7) {
          print '<p><strong>Your server cannot communicate with Facebook\'s servers. This means your server does not support IPv6 or is having issues resolving facebook.com. Please contact your hosting provider.';
        }
        curl_close($ch);
      } else {
        print ('Sorry, your server does not allow remote fopen or have CURL');
      }
    $facebook_graph_results = json_decode($facebook_graph_results, true);
    return $facebook_graph_results;
  }

  public static function _enqueue_resources() {
    if(isset(self::$options['colorbox']['enabled']) && self::$options['colorbox']['enabled']) {
      wp_enqueue_script('lightbox-js', plugins_url( '/js/colorbox/jquery.colorbox-min.js', __FILE__ ), array('jquery'));
      wp_enqueue_style('lightbox-style', plugins_url( '/css/colorbox.css', __FILE__ ));
    }
      wp_enqueue_style('fbalbum-style', plugins_url( '/css/fbalbum.css', __FILE__ ));
  }

  public static function _setup_opt_menu() {
    add_options_page('Facebook Album', 'Facebook Album', 'publish_pages', 'facebook-album', array('FB_Album', '_options_page'), '', 25);
    add_action('admin_enqueue_scripts', array('FB_Album', '_options_page_resources'));
  }
  public static function _options_page() {
    include dirname( __FILE__ ).'/inc/options-facebookalbum.php';
  }
  public static function _options_page_resources($hook) {
    if( 'settings_page_facebook-album' != $hook ) return;
      wp_enqueue_style( 'facebookalbum.css', plugins_url('/css/admin-facebookalbum.css', __FILE__) );
  }
  /**
    * Some pictures are too small and don't have everythumnail. This cycles to find the lowest one, exits out if thumb hits 0, just in case
    */
  public static function check_thumbnail_src_size_url( $img_array, $thumb_size ) {
    while( !isset($img_array['images'][$thumb_size]['source']) || $thumb_size == 0 ) {
      $thumb_size--;
    }
    return $img_array['images'][$thumb_size]['source'];

  }

  /**
    * Finds and saves album ID by breaking apart the Facebook URL
    * @return void
    */
  protected static function _find_album_id() {
    if(!self::_get_album_url())
        return;

    //Explodes URL based on slashes, we need the end of the URL
    $facebook_album_id = explode('?set=', self::_get_album_url());
    $facebook_album_id = $facebook_album_id['1'];
    //Explodes section by periods, Album ID is first of the 3 sets of numbers
    $facebook_album_id = explode('.', $facebook_album_id);
    $facebook_album_id = $facebook_album_id['1'];

    self::_set_album_id( $facebook_album_id );
  }

  /**
    * Sets WordPress transient to cache API response
    */
  protected static function set_api_cache($json) {
    //Just in case
    if(!self::$album_id)
      return;
    $cache_lifetime = (isset(self::$options['cache'])) ? self::$options['cache'] * HOUR_IN_SECONDS : HOUR_IN_SECONDS;

    set_transient("fbalbum_".self::$album_id, $json, $cache_lifetime); //caching for 12 hours
  }

  /**
   * Clears the plugins cache
   */
  public static function clear_cache($album_id) {
    delete_transient('fbalbum_' . $album_id);
  }

  /**
    * Gets WordPress transient of cached JSON response
    **/
  protected static function get_api_cache() {
    return get_transient("fbalbum_".self::$album_id);
  }

  /**
    * Sets $album_url within class.
    * @param url
    * @return void
    */
  public static function _set_album_url( $url ) {
      self::$album_url = $url;
      self::_find_album_id();
  }
  /**
    * Gets $album_url from within class
    * @return url
    */ public static function _get_album_url() { return self::$album_url; }
  /**
  * Sets $album_id within class
  * @param id
  * @return void
  */
  public static function _set_album_id( $id ) { self::$album_id = $id; }
  /**
  * Gets $album_url from within class
  */
  public static function _get_album_id() { return self::$album_id; }
  /**
  * Makes URLs validator friendly by replacing & to &amp;
  * @param url
  * @return string
  */
  public static function _clean_url( $url ) {
  return str_replace('&', '&amp;', $url);
  }
}
