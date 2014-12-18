<?php
/*
Plugin Name: Live Search Popup
Plugin URI: http://wordpress.org/extend/plugins/live-search-popup
Description: Live Search with an AJAX popup
Author: Stefan Schimanski - heavily based on John Nunemaker's Addicted Search
Version: 1.4.7
Author URI: http://1stein.org/2007/09/11/live-search-popup/
*/

function livesearchpopup_add() {
    if (!isset($ak_prototype) || !$ak_prototype) {                                                           
        echo '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/live-search-popup/js/prototype.js"></script>' . "\n";    
    }
	echo '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/live-search-popup/js/live_search.js"></script>' . "\n";
	echo '
	<script type="text/javascript">
		ls.url = "' . get_bloginfo('wpurl') . '/wp-content/plugins/live-search-popup/search_results.php";
	</script>';
	echo '<link type="text/css" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/live-search-popup/css/live_search.css" rel="stylesheet" />' . "\n";
}

add_action('wp_head', 'livesearchpopup_add');


function livesearchpopup_results() {
?>
     <div id="livesearchpopup_box" style="display: none;">
       <img class="pfeil" src="<?php print get_bloginfo('wpurl') ?>/wp-content/plugins/live-search-popup/searchpfeil.png" alt="" />

       <h1><?php print __("Results", 'live-search-popup'); ?></h1>

       <div id="livesearchpopup_results"></div>
     </div>
<?php
}

function livesearchpopup_resultsbox($width) {
?>
     <div style="padding-top: 7px; width:$width">
    <?php livesearchpopup_results() ?>
     </div>
<?php
}

function livesearchpopup_searchbox() {
?>
<div class="livesearchpopup">
     <div class="box">
     	<form name="ls_form" class="form" id="searchform" method="get" action="<?php print get_bloginfo('wpurl')?>">
      <div class="editbox"><input class="edit" type="text" name="s" id="s" /></div>
       </form>
<?php
        livesearchpopup_results();
?>
     </div>
</div>
<?php
}

function widget_livesearchpopup_init() {
    if ( !function_exists('register_sidebar_widget') )
        return;

    function livesearchpopup_widget($args) {
        extract($args);
		$options = get_option('livesearchpopup_widget');
		$title = $options['title'];
		echo $before_widget . $before_title . $title . $after_title;
        livesearchpopup_searchbox();
        echo $after_widget;
    }

    register_sidebar_widget(array('Live Search Popup', 'widgets'), 'livesearchpopup_widget');

	function livesearchpopup_widget_control() {
		$options = get_option('livesearchpopup_widget');
		if (!is_array($options)) {
			$options = array(
				'title' => ""
			);
		}
		if (isset($_POST['ak_action']) && $_POST['ak_action'] == 'livesearchpopup_update_widget_options') {
			$options['title'] = strip_tags(stripslashes($_POST['livesearchpopup_widget_title']));
			update_option('livesearchpopup_widget', $options);
		}

		// Be sure you format your options to be valid HTML attributes.
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		
		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		print('
			<p style="text-align:right;"><label for="livesearchpopup_widget_title">' . __('Title:') . ' <input style="width: 200px;" id="livesearchpopup_widget_title" name="livesearchpopup_widget_title" type="text" value="'.$title.'" /></label></p>
			<input type="hidden" id="ak_action" name="ak_action" value="livesearchpopup_update_widget_options" />
		');
	}
	register_widget_control(array(__('Live Search Popup', 'live-search-popup'), 'widgets'), 'livesearchpopup_widget_control', 300, 100);
}

add_action('plugins_loaded', 'widget_livesearchpopup_init');

function live_search_popup_rewrite($wp_rewrite) {
    $rules = array(
        'wp-content/plugins/live-search-popup/search_results.php' => '/',
        );

    $wp_rewrite->rules = $rules + $wp_rewrite->rules;
}

// Hook in.
add_filter('generate_rewrite_rules', 'live_search_popup_rewrite');

?>