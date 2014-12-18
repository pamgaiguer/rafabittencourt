<?php
/**
 * init
 */
function disablePlugin(){
    $options = get_option('thethe_tna_option');
	$frontDisable = ($options['frontdisable'] == "true") ? true : false;
	
	if((is_front_page() || is_home()) && ($frontDisable)){
		return true;
	}			
	return false;	
}
function TheTheTS_head_scripts(){
	if(disablePlugin()){
			return;
	}		
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', 'http://code.jquery.com/jquery-latest.min.js', false, 'latest');
    wp_enqueue_script( 'jquery' );

    wp_deregister_script( 'jquery-ui' );
    wp_register_script( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js');
    wp_enqueue_script( 'jquery-ui' );

    wp_deregister_script('thethe-toggle');
    wp_register_script( 'thethe-toggle', $GLOBALS['TheTheTS']['wp_plugin_dir_url'].'style/js/thethe.toggle.js');
    wp_enqueue_script( 'thethe-toggle' );

    wp_deregister_script('thethe-haccordion');
    wp_register_script('thethe-haccordion', $GLOBALS['TheTheTS']['wp_plugin_dir_url'].'style/js/thethe.haccordion.js');
    wp_enqueue_script('thethe-haccordion');
	
}
function TheTheTS_head_styles(){
	if(disablePlugin()){
		return;
	}
			
    $options = get_option('thethe_tna_option');
    $style['jquery-ui-style'] = $options['jqueryui-theme'];
	
    if ($style['jquery-ui-style'] && $style['jquery-ui-style'] != 'none') {
        wp_register_style('jquery-ui-style-'.$style['jquery-ui-style'], 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/'.$style['jquery-ui-style'].'/jquery-ui.css');
        wp_enqueue_style('jquery-ui-style-'.$style['jquery-ui-style']);
    }
	
    wp_register_style('thethe-haccordion', $GLOBALS['TheTheTS']['wp_plugin_dir_url'].'style/thethe-haccordion.css');
    wp_enqueue_style('thethe-haccordion');
}

function TheTheTS_Init()
{
	add_action('wp_print_scripts', "TheTheTS_head_scripts");
	add_action('wp_print_styles', "TheTheTS_head_styles");
    add_action('wp_head', 'TheTheTS_Print_Style');	
}

/**
 * Install
 */
function TheTheTS_Activate()
{
    $o = array();
    $o['frontdisable'] = '';
    $o['style'] = '';
    $o['jqueryui-theme'] = 'smoothness';
    update_option('thethe_tna_option', $o);
}

/**
 * UnInstall
 */
function TheTheTS_deActivate()
{
    delete_option('thethe_tna_option');
} // end func 

/**
 * Print Custom style
 */
function TheTheTS_Print_Style()
{
	if(disablePlugin()){
		return;
	}	
    $options = get_option('thethe_tna_option');
	if($options['style']) {
	    echo "<!-- TheThe Fly Tabs-n-Accordions custom style begin //-->\n";
	    echo "<style type='text/css'>\n".stripslashes(base64_decode($options['style'])). "\n</style>";
	    echo "<!-- TheThe Fly Tabs-n-Accordions custom style end //-->\n";
	}
}
