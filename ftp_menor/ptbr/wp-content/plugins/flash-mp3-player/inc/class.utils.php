<?php
class FMP_Utils{
    function FMP_Utils(){}
  
    function print_player($args){
        $defaults = array(
            'player_url'        => '',
            'config_url'        => '',
            'playlist_url'      => '',
            'width'             => 230,
            'height'            => 350,
            'id'                => '',
            'class'             => '',
            'transparent'       => true,
            'autostart'         => false,
            'file'              => ''
        );
        $r = wp_parse_args($args, $defaults);
        extract($r,EXTR_SKIP);

        if(!empty($config_url)){
            $config_url = urlencode($config_url) . '?' . rand();
        }

        if(!empty($playlist_url)){
            $playlist_url = urlencode($playlist_url) . '?' . rand();
        }else{
            $playlist_url = urlencode($file);
        }

        if ($id == ''){
            $id = 'css' . md5(uniqid(mt_rand(), false)); //the id must start with char
        }
        if ($class != '') $class = ' class="' . $class . '" ';
        if ($transparent) $wmode = 'transparent'; else $wmode = 'opaque';
?>

<div <?php echo ' id="', $id, '" ', $class;?>>
    <p>Here is the Music Player. You need to installl flash player to show this cool thing!</p>
</div>
<script type="text/javascript">

var flashvars = {
  config: "<?php echo $config_url;?>",
  file  : "<?php echo $playlist_url;?>"
};
var params = {
  wmode             : "<?php echo $wmode;?>",
  quality           : "high",
  allowFullScreen   : "true",
  allowScriptAccess : "true"
};
var attributes = {};

swfobject.embedSWF("<?php echo $player_url;?>", "<?php echo $id;?>", "<?php echo $width;?>", "<?php echo $height;?>", "9", "expressInstall.swf", flashvars, params, attributes);

</script>

<?php
    }
    
    function player_shortcode($atts){
        global $fmp_jw_url, $fmp_jw_files_url;
    	extract(shortcode_atts(array(
            'width' => '177',
            'height' => '280',
            'config' => '',
            'playlist' => '',
            'file' => '',
            'id'    => '',
            'class' => ''
        ), $atts));
        $args = array(
            'player_url'        => $fmp_jw_url . '/player/player.swf',
            'config_url'        => $fmp_jw_files_url . '/configs/' . $config,
            'playlist_url'      => empty($playlist)?'':$fmp_jw_files_url . '/playlists/' . $playlist,
            'width'             => $width,
            'height'            => $height,
            'file'              => empty($file)?'':$file,
            'id'                => $id,
            'class'             => $class
        );
        ob_start();
        $this->print_player($args);
        $player = ob_get_contents();
        ob_end_clean();
        return $player;
    }

    function add_media_button(){
        global $fmp_jw_url, $fmp_jw_files_dir;
        $wizard_url = $fmp_jw_url . '/inc/shortcode_wizard.php';
        $config_dir = $fmp_jw_files_dir . '/configs';
        $playlist_dir = $fmp_jw_files_dir .'/playlists';
        $button_src = $fmp_jw_url . '/inc/images/playerbutton.gif';
        $button_tip = 'Insert a Flash MP3 Player';
        echo '<a title="Add a MP3 player" href="'.$wizard_url.'?config=' .urlencode($config_dir). '&playlist='.urlencode($playlist_dir).'&KeepThis=true&TB_iframe=true" class="thickbox" ><img src="' . $button_src . '" alt="' . $button_tip . '" /></a>';
    }
}

function safe_check(){
    if(!function_exists('scandir') && (!function_exists('opendir') || !function_exists('readdir'))){
        ?>
        <div class="error fade" id="message">
            <p><strong>Sorry, PHP function 'scandir' is forbidden in your server, so that the plugin will not work. Please disable the plugin.</strong></p>
        </div>
        <?php
    }
}

if( !function_exists('my_scandir') ) {
    function my_scandir($directory, $sorting_order = 0) {
        if(function_exists('scandir')){
            return scandir($directory, $sorting_order);
        }else{
            $dh  = opendir($directory);
            while( false !== ($filename = readdir($dh)) ) {
                $files[] = $filename;
            }
            if( $sorting_order == 0 ) {
                sort($files);
            } else {
                rsort($files);
            }
            return($files);
        }
    }
}


function fmp_tag_print_player($args){
    global $fmp_jw_util;
    global $fmp_jw_url;
    $defaults = array(
        'player_url'        => $fmp_jw_url . '/player/player.swf',
        'config_url'        => '',
        'playlist_url'      => '',
        'width'             => 230,
        'height'            => 350,
        'id'                => '',
        'class'             => '',
        'transparent'       => true,
        'autostart'         => false,
        'file'              => ''
    );
    $r = wp_parse_args($args, $defaults);
    $fmp_jw_util->print_player($r);
}
?>
