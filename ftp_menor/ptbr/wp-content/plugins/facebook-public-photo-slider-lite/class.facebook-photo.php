<?php
include_once (dirname(__FILE__).'/libs/class.plugin-base.php');
class  WPFacebookPhoto extends WPFacebookPhotoBase{
	var $admin_js = array('jquery');
	var $front_css = array('facebook-public-photo');
    var $front_js = array('jquery','facebook-public-photo','jquery.easing-1.3.min');
    function __construct(){
        $pages = self::get_page_list();    	
        $config = new stdClass();
        $config->plugin_name = "Facebook Photo";        
        $config->version = FACEBOOKPHOTO_VER;
        $config->plugin_base_file= 'facebook-photo.php';
        $config->pages = $pages;
        $config->default_page = 'setting_page';
        $config->menu_type = 'option_page';
        parent::__construct($config);        	
    }
    function setting_page(){	
	    if(isset($_POST[$this->plugin_id.'_setting_submit'])){
	        $this->set_options(array('setting'=>$_POST['setting']));
            $attrs = $_POST['setting']; unset($attrs['shortcode']);          
            $shortcode = '[binnash_facebook_photo ' . $this->array_to_string($attrs, "  ") . ' ]';
            $this->options['setting']['shortcode'] = $shortcode;
	        $this->message = '<div id="message" class="updated fade"><p>Shortcode Generated!</p></div>';
	    }
        else{
            $this->options['setting']['shortcode'] = '';
        }
    	$setting = $this->options['setting'];
        
    	include_once($this->plugin_path.'/views/settings.php');
    }
    function binnash_facebook_photo__shortcode($attrs){ 
        if(!isset($attrs['album_url'])||empty($attrs['album_url'])) return;
        $response = self::get_url_contents(html_entity_decode($attrs['album_url']));
        $dom = new DOMDocument;
        $urls = array();
        @$dom->loadHTML($response['content']);
        $xpath = new DOMXpath($dom);
        $elements = $xpath->query('/html/body/code/comment()');
        if (!is_null($elements)) {
          foreach ($elements as $element) {
                $sxe = simplexml_load_string($element->nodeValue);
                $attr = (string)$sxe->attributes()->class;
                if(!empty($attr)&& (strpos($attr, 'fbPhotosGrid')!=false)){
                    foreach ($sxe->children()->tbody[0] as $tr){ 
                        foreach($tr[0] as $td){              
                            if(!empty($td[0]->div[0]->a[0]['data-src']))
                                $style = 'background-image:url('. (string)$td[0]->div[0]->a[0]['data-src']. ');';
                            else
                                $style = (string)$td[0]->div[0]->a[0]->i[0]['style'];
  
                            parse_str(urldecode((string)$td[0]->div[0]->a[0]['ajaxify']), $output);
                            $output['href'] = urldecode((string)$td[0]->div[0]->a[0]['href']);
                            $output['style'] = urldecode($style);
                            $urls[] = $output;
                        }
                    }
                    break;                              
                }

          }
          ob_start();  
          include_once('views/slider.php');
          $out = ob_get_contents();  
          ob_end_clean();
          return $out;
        }        
    }
    function array_to_string($array, $separator="  "){
        $attrs = "";
        $first = true;
        foreach( $array as $key=>$value){
            if($first)$first = false;
            else $attrs .=$separator;
            $attrs .= $key . "=&quot;" . $value . "&quot;";
        }
        return $attrs;
    }
    static function get_url_contents($url){
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "spider", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;        
    }
    static function get_page_list(){
        $pages = array('setting_page'=>array('title'=>"Facebook Photo"));
        return json_decode(json_encode($pages));
    }
}
