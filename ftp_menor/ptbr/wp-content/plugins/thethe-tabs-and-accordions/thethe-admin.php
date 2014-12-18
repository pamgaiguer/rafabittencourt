<?php
if (!function_exists('TheThe_makeAdminPage')) {
    function TheThe_makeAdminPage(){
		include 'inc/view-about-us.php';
    }
}
function TheTheTS_Style(){
	$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	wp_admin_css( 'nav-menu' );			
	wp_deregister_style( 'thethefly-plugin-panel-interface');
	wp_register_style( 'thethefly-plugin-panel-interface', $x.'/style/admin/interface.css' );
	wp_enqueue_style( 'thethefly-plugin-panel-interface' );
	
	wp_enqueue_script( 'postbox');
	wp_enqueue_script( 'post');	
}

function TheTheTS_RegisterPluginLinks($links, $file) {
	$base = $GLOBALS['TheTheTS']['wp_plugin_base_name'];
	if ($file == $base) {
		$links[] = '<a href="admin.php?page=tabs-n-accordions&view=settings">' . __('Settings') . '</a>';
		$links[] = '<a href="http://thethefly.com/support/forum/wordpress-plugins-by-thethe-fly-group3/thethe-tabs-and-accordions-forum11/">' . __('Support') . '</a>';
		$links[] = '<a href="http://thethefly.com/themes/">' . __('Themes') . '</a>';
		$links[] = '<a href="http://thethefly.com/wordpress-tips-tricks-hacks-newsletter/">' . __('Tips & Tricks') . '</a>';			
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=U2DR7CUBZLPFG">' . __('Donate') . '</a>';
	}
	return $links;
}

/**
 * Add menu to admin options page
 */
function TheTheTS_Menu()
{
    global $menu;

    $flag['makebox'] = true;
    if (is_array($menu)) foreach ($menu as $e) {
        if (isset($e[0]) && (in_array($e[0], array('TheThe Fly','TheTheFly')))) {
            $flag['makebox'] = false;
            break;
        }
    }

    if ($flag['makebox']) {
		$icon_url = $GLOBALS['TheTheTS']['wp_plugin_dir_url'].'style/admin/images/favicon.ico';
		// Add a new top-level menu:
		add_menu_page('TheThe Fly', 'TheThe Fly', 'edit_theme_options', 'thethefly', 'TheThe_makeAdminPage',$icon_url, 63);
        // Add a submenu to the top-level menu:
		$panelHookAboutUs = add_submenu_page('thethefly', 'TheThe Fly: About the Club', 'About the Club', 'edit_theme_options', 'thethefly', 'TheThe_makeAdminPage');
    }

    // Add Tabs-n-slides
    $panelHook = add_submenu_page('thethefly', 'TheThe Tabs and Accordions','Tabs and Accordions','edit_theme_options','tabs-n-accordions','TheTheTS_options');
	add_filter( 'admin_print_styles-' . $panelHookAboutUs, 'TheTheTS_Style');
	add_filter( 'admin_print_styles-' . $panelHook, 'TheTheTS_Style');	
	add_filter('plugin_row_meta', 'TheTheTS_RegisterPluginLinks',10,2);			
} // end func TheTheTS_Menu

/**
 * 
 */
function TheTheTS_options()
{
    $jQueryUIThemes = array('none',
        'black-tie','blitzer','cupertino','dark-hive','dot-luv',
        'eggplant','excite-bike','flick','hot-sneaks','humanity',
        'le-frog','mint-choc','overcast','pepper-grinder','redmond',
        'smoothness','south-street','start','sunny','swanky-purse',
        'trontastic','ui-darkness','ui-lightness','vader');
    $message = '';
    $options = $newoptions = get_option('thethe_tna_option');
    if(isset($_POST['submit'])) {
        $newoptions['frontdisable'] = $_POST['frontdisable'];
        $newoptions['jqueryui-theme'] = $_POST['jqueryui-theme'];
        $newoptions['style'] = base64_encode($_POST['style']);
        if($options != $newoptions) {
            update_option('thethe_tna_option',$newoptions);
            $message = "Options Saved";
        }
    }
	if(isset($_POST['reset'])) {
        $newoptions['frontdisable'] = '';
        $newoptions['jqueryui-theme'] = 'smoothness';
        $newoptions['style'] = '';
		if($options != $newoptions) {
			update_option('thethe_tna_option',$newoptions);
			$message = "Options Saved";
		}
	}	
    $options = get_option('thethe_tna_option');
    $frontdisable = ($options['frontdisable']=='true') ? ' checked="checked "': '';

	include 'inc/view-tabs.php';	
}// end func TheTheTS_options