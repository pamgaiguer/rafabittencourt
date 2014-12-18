<?php
/*
Plugin Name: WP-UserOnline
Plugin URI: http://lesterchan.net/portfolio/programming/php/
Description: Enable you to display how many users are online on your Wordpress blog with detailed statistics of where they are and who there are(Members/Guests/Search Bots).
Version: 2.50
Author: Lester 'GaMerZ' Chan
Author URI: http://lesterchan.net
*/


/*  
	Copyright 2009  Lester Chan  (email : lesterchan@gmail.com)

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


### Load WP-Config File If This File Is Called Directly
if (!function_exists('add_action')) {
	$wp_root = '../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}


### Create Text Domain For Translations
add_action('init', 'useronline_textdomain');
function useronline_textdomain() {
	load_plugin_textdomain('wp-useronline', false, 'wp-useronline');
}


### UserOnline Table Name
global $wpdb;
$wpdb->useronline = $wpdb->prefix.'useronline';


### Function: WP-UserOnline Menu
add_action('admin_menu', 'useronline_menu');
function useronline_menu() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('index.php',  __('WP-UserOnline', 'wp-useronline'),  __('WP-UserOnline', 'wp-useronline'), 1, 'wp-useronline/wp-useronline.php', 'display_useronline');
	}
	if (function_exists('add_options_page')) {
		add_options_page(__('UserOnline', 'wp-useronline'), __('UserOnline', 'wp-useronline'), 'manage_options', 'wp-useronline/useronline-options.php');
	}
}


### Function: Print Out jQuery Script At The Top
add_action('wp_head', 'useronline_javascripts_header');
function useronline_javascripts_header() {
	wp_print_scripts('jquery');
}


### Function: Enqueue Useronline Javascripts/CSS
add_action('wp_enqueue_scripts', 'useronline_scripts');
function useronline_scripts() {
	wp_enqueue_script('wp-useronline', plugins_url('wp-useronline/useronline-js.js'), array('jquery'), '2.50', true);
	wp_localize_script('wp-useronline', 'useronlineL10n', array(
		'ajax_url' => plugins_url('wp-useronline/wp-useronline.php'),
		'timeout' => (get_option('useronline_timeout')*1000)
	));
}


### Function: Process UserOnline
add_action('admin_head', 'useronline');
add_action('wp_head', 'useronline');
function useronline() {
	global $wpdb, $useronline;
	// Useronline Settings
	$timeoutseconds = get_option('useronline_timeout');
	$timestamp = current_time('timestamp');
	$timeout = ($timestamp-$timeoutseconds);
	$ip = get_ipaddress();
	$url = addslashes(urlencode($_SERVER['REQUEST_URI']));
	$referral = '';
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$current_user = wp_get_current_user();
	if(!empty($_SERVER['HTTP_REFERER'])) {
		$referral = addslashes(urlencode(strip_tags($_SERVER['HTTP_REFERER'])));
	}
	// Check For Bot
	$bots = get_option('useronline_bots');
	foreach ($bots as $name => $lookfor) { 
		if (stristr($useragent, $lookfor) !== false) { 
			$user_id = 0;
			$display_name = addslashes($name);
			$user_name = addslashes($lookfor);		
			$type = 'bot';
			$where = "WHERE ip = '$ip'";
			$bot_found = true;
			break;
		} 
	}

	// If No Bot Is Found, Then We Check Members And Guests
	if(!$bot_found) {
		// Check For Member
		if($current_user->ID > 0) {
			$user_id = $current_user->ID;
			$display_name = addslashes($current_user->display_name);
			$user_name = addslashes($current_user->user_login);		
			$type = 'member';
			$where = "WHERE userid = '$user_id'";
		// Check For Comment Author (Guest)
		} elseif(!empty($_COOKIE['comment_author_'.COOKIEHASH])) {
			$user_id = 0;
			$display_name = addslashes(trim($_COOKIE['comment_author_'.COOKIEHASH]));
			$user_name = __('guest', 'wp-useronline').'_'.$display_name;		
			$type = 'guest';
			$where = "WHERE ip = '$ip'";
		// Check For Guest
		} else {
			$user_id = 0;
			$display_name = __('Guest', 'wp-useronline');
			$user_name = "guest";		
			$type = 'guest';
			$where = "WHERE ip = '$ip'";
		}
	}

	// Get User Agent
	$useragent = addslashes($useragent);

	// Check For Page Title
	$make_page = wp_title('&raquo;', false);
	if(empty($make_page)) {
		$make_page = get_bloginfo('name').' &raquo; '.$_SERVER['REQUEST_URI']; 
	} elseif(is_single()) {
		$make_page = get_bloginfo('name').' &raquo; '.__('Blog Archive', 'wp-useronline').' '.$make_page;
	} else {
		$make_page = get_bloginfo('name').$make_page;
	}
	$make_page = addslashes($make_page);
	
	// Delete Users
	$delete_users = $wpdb->query("DELETE FROM $wpdb->useronline $where OR (timestamp < $timeout)");
	
	// Insert Users
	$insert_user = $wpdb->query("INSERT INTO $wpdb->useronline VALUES ('$timestamp', '$user_id', '$user_name', '$display_name', '$useragent', '$ip', '$make_page', '$url', '$type', '$referral')");

	// Count Users Online
	$useronline = intval($wpdb->get_var("SELECT COUNT(*) FROM $wpdb->useronline"));
	
	// Get Most User Online
	$most_useronline = intval(get_option('useronline_most_users'));

	// Check Whether Current Users Online Is More Than Most Users Online
	if($useronline > $most_useronline) {
		update_option('useronline_most_users', $useronline);
		update_option('useronline_most_timestamp', current_time('timestamp'));
	}
}


### Function: Display UserOnline
if(!function_exists('get_useronline')) {
	function get_useronline($display = true) {
		// Template - Naming Conventions
		$useronline_naming = get_option('useronline_naming');
		// Template - User(s) Online
		$template_useronline = stripslashes(get_option('useronline_template_useronline'));
		$template_useronline = str_replace('%USERONLINE_PAGE_URL%', get_option('useronline_url'), $template_useronline);
		$template_useronline = str_replace('%USERONLINE_MOSTONLINE_COUNT%', number_format_i18n(get_most_useronline()), $template_useronline);
		$template_useronline = str_replace('%USERONLINE_MOSTONLINE_DATE%', get_most_useronline_date(), $template_useronline);
		if(get_useronline_count() == 1) {
			$template_useronline = str_replace('%USERONLINE_USERS%', stripslashes($useronline_naming['user']), $template_useronline);			
		} else {
			$useronline_naming_users = str_replace('%USERONLINE_COUNT%', number_format_i18n(get_useronline_count()), stripslashes($useronline_naming['users']));
			$template_useronline = str_replace('%USERONLINE_USERS%', $useronline_naming_users, $template_useronline);
		}
		if($display) {
			echo $template_useronline;
		} else {
			return $template_useronline;
		}
	}
}


### Function: Display UserOnline Count
if(!function_exists('get_useronline_count')) {
	function get_useronline_count($display = false) {
		global $useronline;
		if($display) {
			echo number_format_i18n($useronline);
		} else {
			return $useronline;
		}
	}
}


### Function: Display Max UserOnline
if(!function_exists('get_most_useronline')) {
	function get_most_useronline($display = false) {
		$most_useronline_users = intval(get_option('useronline_most_users'));
		if($display) {
			echo number_format_i18n($most_useronline_users);
		} else {
			return $most_useronline_users;
		}
	}
}


### Function: Display Max UserOnline Date
if(!function_exists('get_most_useronline_date')) {
	function get_most_useronline_date($display = false) {
		$most_useronline_timestamp = get_option('useronline_most_timestamp');
		$most_useronline_date = mysql2date(sprintf(__('%s @ %s', 'wp-useronline'), get_option('date_format'), get_option('time_format')), gmdate('Y-m-d H:i:s', $most_useronline_timestamp));
		if($display) {
			echo $most_useronline_date;
		} else {
			return $most_useronline_date;
		}
	}
}


### Function Check If User Is Online
function is_online($user_login) { 
	global $wpdb;
	$is_online = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->useronline WHERE username = '$user_login' LIMIT 1");
	return intval($is_online);
}



### Function: Update Member last Visit
//add_action('wp_head', 'update_memberlastvisit');
function update_memberlastvisit() {
	global $current_user, $user_ID;
	if(!empty($current_user) && is_user_logged_in()) {
		update_user_option($user_ID, 'member_last_login', current_time('timestamp'));   
	}
}


### Function: Get Member last Visit
function get_memberlastvisit($user_id = 0) {
	$date_format = sprintf(__('%s @ %s', 'wp-useronline'), get_option('date_format'), get_option('time_format'));
	if($user_id == 0) {
		return mysql2date($date_format, gmdate('Y-m-d H:i:s', get_user_option('member_last_login')));
	} else {
		return mysql2date($date_format, gmdate('Y-m-d H:i:s', get_user_option('member_last_login', $user_id)));
	}
}


### Function: Display Users Browsing The Site
function get_users_browsing_site($display = true) {
	global $wpdb;

	// Get Users Browsing Site
	$page_url = addslashes(urlencode($_SERVER['REQUEST_URI']));
	$users_browse = $wpdb->get_results("SELECT displayname, type FROM $wpdb->useronline ORDER BY type");

	// Variables
	$members = array();
	$guests = array();
	$bots = array();
	$total_members = 0;
	$total_guests = 0;
	$total_bots = 0;
	$nicetext_members = '';
	$nicetext_guests = '';
	$nicetext_bots = '';

	// If There Is Users Browsing, Then We Execute
	if($users_browse) {
		// Get Users Information
		foreach($users_browse as $user_browse) {
			switch($user_browse->type) {
				case 'member':
					$members[] = stripslashes($user_browse->displayname);
					$total_members++;
					break;
				case 'guest':						
					$guests[] = stripslashes($user_browse->displayname);
					$total_guests++;
					break;
				case 'bot':
					$bots[] = stripslashes($user_browse->displayname);
					$total_bots++;
					break;
			}
		}

		// If We Do Not Display It, Return Respective Users Count
		if(!$display) {
			return array($total_members, $total_guests, $total_bots);
		} 
		
		// Template - Naming Conventions
		$useronline_naming = get_option('useronline_naming');

		// Template - User(s) Browsing Site
		$options_browsingsite = get_option('useronline_template_browsingsite');
		$separator_members_browsingsite = stripslashes($options_browsingsite[0]);
		$separator_guests_browsingsite = stripslashes($options_browsingsite[1]);
		$separator_bots_browsingsite = stripslashes($options_browsingsite[2]);
		$template_browsingsite = stripslashes($options_browsingsite[3]);

		// Nice Text For Users
		if(get_useronline_count() == 1) {
			$template_browsingsite = str_replace('%USERONLINE_USERS%', stripslashes($useronline_naming['user']), $template_browsingsite);		
		} else {
			$useronline_naming_users = str_replace('%USERONLINE_COUNT%', number_format_i18n(get_useronline_count()), stripslashes($useronline_naming['users']));
			$template_browsingsite = str_replace('%USERONLINE_USERS%', $useronline_naming_users, $template_browsingsite);
		}

		// Print Member Name
		if($members) {
			$temp_member = '';
			if(!function_exists('get_totalposts')) {
				foreach($members as $member) {
					$temp_member .= $member.$separator_members_browsingsite;
				}
			} else {
				foreach($members as $member) {
					$temp_member .= useronline_stats_page_link($member).$separator_members_browsingsite;
				}
			}
			$template_browsingsite = str_replace('%USERONLINE_MEMBER_NAMES%', substr($temp_member, 0, -strlen($separator_members_browsingsite)), $template_browsingsite);
		} else {
			$template_browsingsite = str_replace('%USERONLINE_MEMBER_NAMES%', '', $template_browsingsite);
		}

		// Nice Text For Members
		if($total_members > 1) { 
			$useronline_naming_members = str_replace('%USERONLINE_COUNT%', number_format_i18n($total_members), stripslashes($useronline_naming['members']));
			$template_browsingsite = str_replace('%USERONLINE_MEMBERS%', $useronline_naming_members, $template_browsingsite);
		} elseif($total_members == 1) {
			$template_browsingsite = str_replace('%USERONLINE_MEMBERS%', stripslashes($useronline_naming['member']), $template_browsingsite);
		} else {
			$template_browsingsite = str_replace('%USERONLINE_MEMBERS%', '', $template_browsingsite);
		}
		
		// Nice Text For Guests
		if($total_guests > 1) {
			$useronline_naming_guests = str_replace('%USERONLINE_COUNT%', number_format_i18n($total_guests), stripslashes($useronline_naming['guests']));
			$template_browsingsite = str_replace('%USERONLINE_GUESTS%', $useronline_naming_guests, $template_browsingsite);
		} elseif($total_guests == 1) {			
			$template_browsingsite = str_replace('%USERONLINE_GUESTS%', stripslashes($useronline_naming['guest']), $template_browsingsite);
		} else {
			$template_browsingsite = str_replace('%USERONLINE_GUESTS%', '', $template_browsingsite);
		}

		// Nice Text For Bots
		if($total_bots > 1) {
			$useronline_naming_bots = str_replace('%USERONLINE_COUNT%', number_format_i18n($total_bots), stripslashes($useronline_naming['bots']));
			$template_browsingsite = str_replace('%USERONLINE_BOTS%', $useronline_naming_bots, $template_browsingsite);
		} elseif($total_bots == 1) {			
			$template_browsingsite = str_replace('%USERONLINE_BOTS%', stripslashes($useronline_naming['bot']), $template_browsingsite);
		} else {
			$template_browsingsite = str_replace('%USERONLINE_BOTS%', '', $template_browsingsite);
		}
		// Seperators
		if($total_members > 0 && $total_guests > 0) {
			$template_browsingsite = str_replace('%USERONLINE_GUESTS_SEPERATOR%', $separator_guests_browsingsite, $template_browsingsite);
		} else {
			$template_browsingsite = str_replace('%USERONLINE_GUESTS_SEPERATOR%', '', $template_browsingsite);
		}
		if(($total_guests > 0 || $total_members > 0) && $total_bots > 0) {
			$template_browsingsite = str_replace('%USERONLINE_BOTS_SEPERATOR%', $separator_bots_browsingsite, $template_browsingsite);
		} else {
			$template_browsingsite = str_replace('%USERONLINE_BOTS_SEPERATOR%', '', $template_browsingsite);
		}

		// Output The Template
		echo $template_browsingsite;
	} else {
		// This Should Not Happen
    global $l10n;
    if (!isset($l10n['wp-useronline'])) {
      useronline_textdomain();
    }
		_e('No User Is Browsing This Site', 'wp-useronline');
	}
}


### Function: Display Users Browsing The Page
function get_users_browsing_page($display = true) {
	global $wpdb;

	// Get Users Browsing Page
	$page_url = addslashes(urlencode($_SERVER['REQUEST_URI']));
	$users_browse = $wpdb->get_results("SELECT displayname, type FROM $wpdb->useronline WHERE url = '$page_url' ORDER BY type");

	// Variables
	$members = array();
	$guests = array();
	$bots = array();
	$total_users = 0;
	$total_members = 0;
	$total_guests = 0;
	$total_bots = 0;
	$nicetext_members = '';
	$nicetext_guests = '';
	$nicetext_bots = '';

	// If There Is Users Browsing, Then We Execute
	if($users_browse) {
		// Reassign Bots Name
		$bots = get_option('useronline_bots');
		$bots_name = array();
		foreach($bots as $botname => $botlookfor) {
			$bots_name[] = $botname;
		}
		// Get Users Information
		foreach($users_browse as $user_browse) {
			switch($user_browse->type) {
				case 'member':
					$members[] = stripslashes($user_browse->displayname);
					$total_members++;
					break;
				case 'guest':						
					$guests[] = stripslashes($user_browse->displayname);
					$total_guests++;
					break;
				case 'bot':
					$bots[] = stripslashes($user_browse->displayname);
					$total_bots++;
					break;
			}
		}
		$total_users = ($total_guests+$total_bots+$total_members);

		// If We Do Not Display It, Return Respective Users Count
		if(!$display) {
			return array ($total_users, $total_members, $total_guests, $total_bots);
		} 

		// Template - Naming Conventions
		$useronline_naming = get_option('useronline_naming');

		// Template - User(s) Browsing Site
		$options_browsingpage = get_option('useronline_template_browsingpage');
		$separator_members_browsingpage = stripslashes($options_browsingpage[0]);
		$separator_guests_browsingpage = stripslashes($options_browsingpage[1]);
		$separator_bots_browsingpage = stripslashes($options_browsingpage[2]);
		$template_browsingpage = stripslashes($options_browsingpage[3]);

		// Nice Text For Users
		if($total_users == 1) {
			$template_browsingpage = str_replace('%USERONLINE_USERS%', stripslashes($useronline_naming['user']), $template_browsingpage);		
		} else {
			$useronline_naming_users = str_replace('%USERONLINE_COUNT%', number_format_i18n($total_users), stripslashes($useronline_naming['users']));
			$template_browsingpage = str_replace('%USERONLINE_USERS%', $useronline_naming_users, $template_browsingpage);
		}

		// Print Member Name
		if($members) {
			$temp_member = '';
			if(!function_exists('get_totalposts')) {
				foreach($members as $member) {
					$temp_member .= $member.$separator_members_browsingpage;
				}
			} else {
				foreach($members as $member) {
					$temp_member .= useronline_stats_page_link($member).$separator_members_browsingpage;
				}
			}
			$template_browsingpage = str_replace('%USERONLINE_MEMBER_NAMES%', substr($temp_member, 0, -strlen($separator_members_browsingpage)), $template_browsingpage);
		} else {
			$template_browsingpage = str_replace('%USERONLINE_MEMBER_NAMES%', '', $template_browsingpage);
		}

		// Nice Text For Members
		if($total_members > 1) { 
			$useronline_naming_members = str_replace('%USERONLINE_COUNT%', number_format_i18n($total_members), stripslashes($useronline_naming['members']));
			$template_browsingpage = str_replace('%USERONLINE_MEMBERS%', $useronline_naming_members, $template_browsingpage);
		} elseif($total_members == 1) {
			$template_browsingpage = str_replace('%USERONLINE_MEMBERS%', stripslashes($useronline_naming['member']), $template_browsingpage);
		} else {
			$template_browsingpage = str_replace('%USERONLINE_MEMBERS%', '', $template_browsingpage);
		}
		
		// Nice Text For Guests
		if($total_guests > 1) {
			$useronline_naming_guests = str_replace('%USERONLINE_COUNT%', number_format_i18n($total_guests), stripslashes($useronline_naming['guests']));
			$template_browsingpage = str_replace('%USERONLINE_GUESTS%', $useronline_naming_guests, $template_browsingpage);
		} elseif($total_guests == 1) {			
			$template_browsingpage = str_replace('%USERONLINE_GUESTS%', stripslashes($useronline_naming['guest']), $template_browsingpage);
		} else {
			$template_browsingpage = str_replace('%USERONLINE_GUESTS%', '', $template_browsingpage);
		}

		// Nice Text For Bots
		if($total_bots > 1) {
			$useronline_naming_bots = str_replace('%USERONLINE_COUNT%', number_format_i18n($total_bots), stripslashes($useronline_naming['bots']));
			$template_browsingpage = str_replace('%USERONLINE_BOTS%', $useronline_naming_bots, $template_browsingpage);
		} elseif($total_bots == 1) {			
			$template_browsingpage = str_replace('%USERONLINE_BOTS%', stripslashes($useronline_naming['bot']), $template_browsingpage);
		} else {
			$template_browsingpage = str_replace('%USERONLINE_BOTS%', '', $template_browsingpage);
		}
		// Seperators
		if($total_members > 0 && $total_guests > 0) {
			$template_browsingpage = str_replace('%USERONLINE_GUESTS_SEPERATOR%', $separator_guests_browsingpage, $template_browsingpage);
		} else {
			$template_browsingpage = str_replace('%USERONLINE_GUESTS_SEPERATOR%', '', $template_browsingpage);
		}
		if(($total_guests > 0 || $total_members > 0) && $total_bots > 0) {
			$template_browsingpage = str_replace('%USERONLINE_BOTS_SEPERATOR%', $separator_bots_browsingpage, $template_browsingpage);
		} else {
			$template_browsingpage = str_replace('%USERONLINE_BOTS_SEPERATOR%', '', $template_browsingpage);
		}


		// Output The Template
		echo $template_browsingpage;
	} else {
		// This Should Not Happen
    global $l10n;
    if (!isset($l10n['wp-useronline'])) {
      useronline_textdomain();
    }
		_e('No User Is Browsing This Page', 'wp-useronline');
	}
}


### Function: Get IP Address
if(!function_exists('get_ipaddress')) {
	function get_ipaddress() {
		if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip_address = $_SERVER["REMOTE_ADDR"];
		} else {
			$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		if(strpos($ip_address, ',') !== false) {
			$ip_address = explode(',', $ip_address);
			$ip_address = $ip_address[0];
		}
		return $ip_address;
	}
}


### Function: Check IP
function check_ip($ip) {
	$current_user = wp_get_current_user();
	$user_level = intval($current_user->wp_user_level);
	$ip2long = ip2long($ip);
	if($user_level == 10 && ($ip != 'unknown') && $ip == long2ip($ip2long) && $ip2long !== false) {
		return "<span dir=\"ltr\">(<a href=\"http://ws.arin.net/cgi-bin/whois.pl?queryinput=$ip\" title=\"".gethostbyaddr($ip)."\">$ip</a>)</span>";
	}
}


### Function: Get User's Country Name/Code
function get_user_origin($ip) {
	global $wpdb, $cache_ips;
	if (!isset($cache_ips[$ip])) {
		$cache_ips[$ip] = $wpdb->get_row("SELECT c.country, c.code FROM ip2nationCountries c, ip2nation i WHERE i.ip < INET_ATON('$ip') AND c.code = i.country ORDER BY i.ip DESC LIMIT 1");
	}
	return $cache_ips[$ip];
}


### Function: Output User's Country Flag/Name
function ip2nation_country($ip, $display_countryname = 0) {
	//$country_mirror = 'http://lesterchan.net/wp-content/themes/lesterchan/images/flags';
	$country_mirror = plugins_url('wp-useronline/images/flags');
	$country_imgtype = 'png';
	$origin = get_user_origin($ip);
	$output = '';
	if($origin) {
		$country_code = $origin->code;
		$country_name = $origin->country;
		if($country_name != 'Private') {				
			if(@file_exists(WP_PLUGIN_DIR.'/wp-useronline/images/flags/sg.'.$country_imgtype) !== false) {
				$output .= '<img src="'.$country_mirror.'/'.$country_code.'.'.$country_imgtype.'" alt="'.$country_name.'" title="'.$country_name.'" /> ';
			}
			if($display_countryname) {
				$output .= $country_name.' ';
			}
		}
	}
	return $output;
}


### Function: Display UserOnline For Admin
function display_useronline() {
	$useronline_page = useronline_page();
	echo '<div class="wrap">'."\n";
	screen_icon();
	echo '<h2>'.__('Users Online Now', 'wp-useronline').'</h2>'."\n";
	echo $useronline_page;
	echo '</div>'."\n";
}


### Function Display UserOnline For Admin's Right Now
add_action('rightnow_end', 'useronline_rightnow');
function useronline_rightnow() {
	$total_users = get_useronline_count(false);
	echo '<p>';
	if ($total_users == 1) { 
		printf(__('There is <strong><a href="%s">%s user</a></strong> online now.', 'wp-useronline'), admin_url('index.php?page=wp-useronline/wp-useronline.php'), number_format_i18n($total_users));
	} else { 
		printf(__('There are a total of <strong><a href="%s">%s users</a></strong> online now.', 'wp-useronline'), admin_url('index.php?page=wp-useronline/wp-useronline.php'), number_format_i18n($total_users));
	}
	echo '<br />';
	get_users_browsing_site();
	echo '.<br />';
	printf(__('Most users ever online were <strong>%s</strong>, on <strong>%s</strong>', 'wp-useronline'), number_format_i18n(get_most_useronline()), get_most_useronline_date());
	echo '</p>'."\n";
}


### Function: Short Code For Inserting Users Online Into Page
add_shortcode('page_useronline', 'useronline_page_shortcode');
function useronline_page_shortcode($atts) {
	return useronline_page();
}


### Function: Format URL
function format_url($url) {
	$url = str_replace('&', '&amp;', $url);
	$url = str_replace(' ', '+', $url);
	return $url;
}


### Function: UserOnline Page
function useronline_page() {
	global $wpdb;
	// Get The Users Online
	$usersonline = $wpdb->get_results("SELECT * FROM $wpdb->useronline ORDER BY type");

	// Variables Variables Variables
	$useronline_output = '';
	$members = array();
	$guests = array();
	$bots = array();
	$total_users = 0;
	$total_members = 0;
	$total_guests = 0;
	$total_bots = 0;
	$nicetext_users = '';
	$nicetext_members = '';
	$nicetext_guests = '';
	$nicetext_bots = '';
	$url_front = 'http://'.$_SERVER['SERVER_NAME'];

	// Process Those User Who Is Online
	if($usersonline) {
		foreach($usersonline as $useronline) {
			switch($useronline->type) {
				case 'member':
					$members[] = array('timestamp' => $useronline->timestamp, 'user_id' => $useronline->userid, 'user_name' => stripslashes($useronline->username), 'display_name' => stripslashes($useronline->displayname), 'user_agent' => stripslashes($useronline->useragent), 'ip' => $useronline->ip, 'location' => stripslashes($useronline->location), 'url' => $url_front.stripslashes(urldecode($useronline->url)), 'referral' => stripslashes(urldecode($useronline->referral)));
					$total_members++;
					break;
				case 'guest':						
					$guests[] = array('timestamp' => $useronline->timestamp, 'user_id' => $useronline->userid, 'user_name' => stripslashes($useronline->username), 'display_name' => stripslashes($useronline->displayname), 'user_agent' => stripslashes($useronline->useragent), 'ip' => $useronline->ip, 'location' => stripslashes($useronline->location), 'url' => $url_front.stripslashes(urldecode($useronline->url)), 'referral' => stripslashes(urldecode($useronline->referral)));
					$total_guests++;
					break;
				case 'bot':
					$bots[] = array('timestamp' => $useronline->timestamp, 'user_id' => $useronline->userid, 'user_name' => stripslashes($useronline->username), 'display_name' => stripslashes($useronline->displayname), 'user_agent' => stripslashes($useronline->useragent), 'ip' => $useronline->ip, 'location' => stripslashes($useronline->location), 'url' => $url_front.stripslashes(urldecode($useronline->url)), 'referral' => stripslashes(urldecode($useronline->referral)));
					$total_bots++;
					break;
			}
		}
		$total_users = ($total_guests+$total_bots+$total_members);
	}

	// Nice Text For Users
	if($total_users == 1) {
		$nicetext_users = number_format_i18n($total_users).' '.__('User', 'wp-useronline');
	} else {
		$nicetext_users = number_format_i18n($total_users).' '.__('Users', 'wp-useronline');
	}

	//  Nice Text For Members
	if($total_members == 1) {
		$nicetext_members = number_format_i18n($total_members).' '.__('Member', 'wp-useronline');
	} else {
		$nicetext_members = number_format_i18n($total_members).' '.__('Members', 'wp-useronline');
	}


	// Nice Text For Guests
	if($total_guests == 1) { 
		$nicetext_guests = number_format_i18n($total_guests).' '.__('Guest', 'wp-useronline');
	} else {
		$nicetext_guests = number_format_i18n($total_guests).' '.__('Guests', 'wp-useronline'); 
	}

	//  Nice Text For Bots
	if($total_bots == 1) {
		$nicetext_bots = number_format_i18n($total_bots).' '.__('Bot', 'wp-useronline'); 
	} else {
		$nicetext_bots = number_format_i18n($total_bots).' '.__('Bots', 'wp-useronline'); 
	}

	// Check Whether WP-Stats Is Activated
	$wp_stats = false;
	if(function_exists('get_totalposts')) {
		$wp_stats = true;
	}
	if ($total_users == 1) { 
		$useronline_output .= '<p>'.sprintf(__('There is <strong>%s</strong> online now: <strong>%s</strong>, <strong>%s</strong> and <strong>%s</strong>.', 'wp-useronline'), $nicetext_users, $nicetext_members, $nicetext_guests, $nicetext_bots).'</p>';
	} else { 
		$useronline_output .= '<p>'.sprintf(__('There are a total of <strong>%s</strong> online now: <strong>%s</strong>, <strong>%s</strong> and <strong>%s</strong>.', 'wp-useronline'), $nicetext_users, $nicetext_members, $nicetext_guests, $nicetext_bots).'</p>';
	}
	$useronline_output .= '<p>'.sprintf(__('Most users ever online were <strong>%s</strong>, on <strong>%s</strong>', 'wp-useronline'), number_format_i18n(get_most_useronline()), get_most_useronline_date()).'</p>';
	// Print Out Members
	if($total_members > 0) {
		$useronline_output .= 	'<h2>'.$nicetext_members.' '.__('Online Now', 'wp-useronline').'</h2>'."\n";
	}
	$no=1;
	if($members) {
		foreach($members as $member) {
			$referral_output = '';
			if(!empty($member['referral'])) {
				$referral_output = ' [<a href="'.format_url($member['referral']).'">'.__('referral', 'wp-useronline').'</a>]';
			}
			if($wp_stats) {
				$useronline_output .= '<p><strong>#'.number_format_i18n($no).' - '.useronline_stats_page_link($member['display_name']).'</strong> '.ip2nation_country($member['ip']).check_ip($member['ip']).' '.__('on', 'wp-useronline').' '.mysql2date(sprintf(__('%s @ %s', 'wp-useronline'), get_option('date_format'), get_option('time_format')), gmdate('Y-m-d H:i:s', $member['timestamp'])).'<br />'.$member['location'].' [<a href="'.format_url($member['url']).'">'.__('url', 'wp-useronline').'</a>]'.$referral_output.'</p>'."\n";
			} else {
				$useronline_output .= '<p><strong>#'.number_format_i18n($no).' - '.$member['display_name'].'</strong> '.ip2nation_country($member['ip']).check_ip($member['ip']).' '.__('on', 'wp-useronline').' '.mysql2date(sprintf(__('%s @ %s', 'wp-useronline'), get_option('date_format'), get_option('time_format')), gmdate('Y-m-d H:i:s', $member['timestamp'])).'<br />'.$member['location'].' [<a href="'.format_url($member['url']).'">'.__('url', 'wp-useronline').'</a>]'.$referral_output.'</p>'."\n";
			}
			$no++;
		}
	}

	// Print Out Guest
	if($total_guests > 0) {
		$useronline_output .= '<h2>'.$nicetext_guests.' '.__('Online Now', 'wp-useronline').'</h2>'."\n";
	}
	$no=1;
	if($guests) {
		foreach($guests as $guest) {
			$referral_output = '';
			if(!empty($guest['referral'])) {
				$referral_output = '[<a href="'.format_url($guest['referral']).'">'.__('referral', 'wp-useronline').'</a>]';
			}
			if($wp_stats) {
				$useronline_output .= '<p><strong>#'.number_format_i18n($no).' - '.useronline_stats_page_link($guest['display_name']).'</strong> '.ip2nation_country($guest['ip']).check_ip($guest['ip']).' '.__('on', 'wp-useronline').' '.mysql2date(sprintf(__('%s @ %s', 'wp-useronline'), get_option('date_format'), get_option('time_format')), gmdate('Y-m-d H:i:s', $guest['timestamp'])).'<br />'.$guest['location'].' [<a href="'.format_url($guest['url']).'">'.__('url', 'wp-useronline').'</a>]'.$referral_output.'</p>'."\n";
			} else {
				$useronline_output .= '<p><strong>#'.number_format_i18n($no).' - '.$guest['display_name'].'</strong> '.ip2nation_country($guest['ip']).check_ip($guest['ip']).' '.__('on', 'wp-useronline').' '.mysql2date(sprintf(__('%s @ %s', 'wp-useronline'), get_option('date_format'), get_option('time_format')), gmdate('Y-m-d H:i:s', $guest['timestamp'])).'<br />'.$guest['location'].' [<a href="'.format_url($guest['url']).'">'.__('url', 'wp-useronline').'</a>]'.$referral_output.'</p>'."\n";
			}
			$no++;
		}
	}

	// Print Out Bots
	if($total_bots > 0) {
		$useronline_output .= '<h2>'.$nicetext_bots.' '.__('Online Now', 'wp-useronline').'</h2>'."\n";
	}
	$no=1;
	if($bots) {
		foreach($bots as $bot) {
			$useronline_output .= '<p><strong>#'.number_format_i18n($no).' - '.$bot['display_name'].'</strong> '.check_ip($bot['ip']).' '.__('on', 'wp-useronline').' '.mysql2date(sprintf(__('%s @ %s', 'wp-useronline'), get_option('date_format'), get_option('time_format')), gmdate('Y-m-d H:i:s', $bot['timestamp'])).'<br />'.$bot['location'].' [<a href="'.format_url($bot['url']).'">'.__('url', 'wp-useronline').'</a>]</p>'."\n";
			$no++;
		}
	}

	// Print Out No One Is Online Now
	if($total_users == 0) {
		$useronline_output .= '<h2>'.__('No One Is Online Now', 'wp-useronline').'</h2>'."\n";
	}

	// Output UserOnline Page
	return apply_filters('useronline_page', $useronline_output);
}


### Function: Stats Page Link
function useronline_stats_page_link($author) {
	$stats_url = '';
	if(function_exists('stats_page')) {
		$stats_url = get_option('stats_url');
		$author_encode = urlencode($author);
		if(strpos($stats_url, '?') !== false) {
			$stats_url = "$stats_url&amp;stats_author=$author_encode";
		} else {
			$stats_url = "$stats_url?stats_author=$author_encode";
		}
		return '<a href="'.$stats_url.'" title="'.$author.'">'.$author.'</a>';
	}
	return $stats_url;
}


### Function: Process AJAX Request
useronline_ajax();
function useronline_ajax() {
	global $wpdb, $useronline;
	$mode = trim($_GET['useronline_mode']);
	if(!empty($mode)) {
		header('Content-Type: text/html; charset='.get_option('blog_charset'));
		switch($mode) {
			case 'useronline_count':
				$useronline = intval($wpdb->get_var("SELECT COUNT(*) FROM $wpdb->useronline"));
				get_useronline();
				break;
			case 'useronline_browsingsite':
				get_users_browsing_site();				
				break;
			case 'useronline_browsingpage':
				get_users_browsing_page();
				break;
		}
		exit();
	}
}


### Function: Plug Into WP-Stats
if(strpos(get_option('stats_url'), $_SERVER['REQUEST_URI']) || strpos($_SERVER['REQUEST_URI'], 'stats-options.php') || strpos($_SERVER['REQUEST_URI'], 'wp-stats/wp-stats.php')) {
	add_filter('wp_stats_page_admin_plugins', 'useronline_page_admin_general_stats');
	add_filter('wp_stats_page_plugins', 'useronline_page_general_stats');
}


### Function: Add WP-UserOnline General Stats To WP-Stats Page Options
function useronline_page_admin_general_stats($content) {
	$stats_display = get_option('stats_display');
	if($stats_display['useronline'] == 1) {
		$content .= '<input type="checkbox" name="stats_display[]" id="wpstats_useronline" value="useronline" checked="checked" />&nbsp;&nbsp;<label for="wpstats_useronline">'.__('WP-UserOnline', 'wp-useronline').'</label><br />'."\n";
	} else {
		$content .= '<input type="checkbox" name="stats_display[]" id="wpstats_useronline" value="useronline" />&nbsp;&nbsp;<label for="wpstats_useronline">'.__('WP-UserOnline', 'wp-useronline').'</label><br />'."\n";
	}
	return $content;
}


### Function: Add WP-UserOnline General Stats To WP-Stats Page
function useronline_page_general_stats($content) {
	$stats_display = get_option('stats_display');
	if($stats_display['useronline'] == 1) {
		$content .= '<p><strong>'.__('WP-UserOnline', 'wp-useronline').'</strong></p>'."\n";
		$content .= '<ul>'."\n";
		$content .= '<li>'.sprintf(_n('<strong>%s</strong> user online now.', '<strong>%s</strong> users online now.', get_useronline_count(), 'wp-useronline'), number_format_i18n(get_useronline_count())).'</li>'."\n";
		$content .= '<li>'.sprintf(_n('Most users ever online was <strong>%s</strong>.', 'Most users ever online was <strong>%s</strong>.', get_most_useronline(), 'wp-useronline'), number_format_i18n(get_most_useronline())).'</li>'."\n";
		$content .= '<li>'.__('On', 'wp-useronline').' <strong>'.get_most_useronline_date().'</strong>.</li>'."\n";
		$content .= '</ul>'."\n";
	}
	return $content;
}

### Class: WP-UserOnline Widget
 class WP_Widget_UserOnline extends WP_Widget {
	// Constructor
	function WP_Widget_UserOnline() {
		$widget_ops = array('description' => __('WP-UserOnline users online statistics', 'wp-useronline'));
		$this->WP_Widget('useronline', __('UserOnline', 'wp-useronline'), $widget_ops);
	}

	// Display Widget
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', esc_attr($instance['title']));
		$type = esc_attr($instance['type']);
		echo $before_widget.$before_title.$title.$after_title;
		echo '<ul>'."\n";
		switch($type) {
			case 'users_online':
				echo '<li><div id="useronline-count">';
				get_useronline();
				echo '</div></li>'."\n";
				break;
			case 'users_browsing_page':
				echo '<li><div id="useronline-browsing-page">';
				get_users_browsing_page();
				echo '</div></li>'."\n";
				break;
			case 'users_browsing_site':
				echo '<li><div id="useronline-browsing-site">';
				get_users_browsing_site();
				echo '</div></li>'."\n";
				break;
			case 'users_online_browsing_page':
				echo '<li><div id="useronline-count">';
				get_useronline();
				echo '</div></li>'."\n";
				echo '<li><div id="useronline-browsing-page">';
				get_users_browsing_page();
				echo '</div></li>'."\n";
				break;
			case 'users_online_browsing_site':
				echo '<li><div id="useronline-count">';
				get_useronline();
				echo '</div></li>'."\n";
				echo '<li><div id="useronline-browsing-site">';
				get_users_browsing_site();
				echo '</div></li>'."\n";
				break;
		}
		echo '</ul>'."\n";
		echo $after_widget;
	}

	// When Widget Control Form Is Posted
	function update($new_instance, $old_instance) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['type'] = strip_tags($new_instance['type']);
		return $instance;
	}

	// DIsplay Widget Control Form
	function form($instance) {
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('title' => __('UserOnline', 'wp-useronline'), 'type' => 'users_online'));
		$title = esc_attr($instance['title']);
		$type = esc_attr($instance['type']);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-useronline'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Statistics Type:', 'wp-useronline'); ?>
				<select name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>" class="widefat">
					<option value="users_online"<?php selected('users_online', $type); ?>><?php _e('Users Online Count', 'wp-useronline'); ?></option>
					<option value="users_browsing_page"<?php selected('users_browsing_page', $type); ?>><?php _e('Users Browsing Current Page', 'wp-useronline'); ?></option>
					<option value="users_browsing_site"<?php selected('users_browsing_site', $type); ?>><?php _e('Users Browsing Site', 'wp-useronline'); ?></option>
					<optgroup>&nbsp;</optgroup>
					<option value="users_online_browsing_page"<?php selected('users_online_browsing_page', $type); ?>><?php _e('Users Online Count & Users Browsing Current Page', 'wp-useronline'); ?></option>
					<option value="users_online_browsing_site"<?php selected('users_online_browsing_site', $type); ?>><?php _e('Users Online Count & Users Browsing Site', 'wp-useronline'); ?></option>
				</select>
			</label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php
	}
}


### Function: Init WP-UserOnline Widget
add_action('widgets_init', 'widget_useronline_init');
function widget_useronline_init() {
	register_widget('WP_Widget_UserOnline');
}


### Function: Create UserOnline Table
add_action('activate_wp-useronline/wp-useronline.php', 'create_useronline_table');
function create_useronline_table() {
	global $wpdb;
  useronline_textdomain();
	$bots = array('Google Bot' => 'googlebot', 'Google Bot' => 'google', 'MSN' => 'msnbot', 'Alex' => 'ia_archiver', 'Lycos' => 'lycos', 'Ask Jeeves' => 'jeeves', 'Altavista' => 'scooter', 'AllTheWeb' => 'fast-webcrawler', 'Inktomi' => 'slurp@inktomi', 'Turnitin.com' => 'turnitinbot', 'Technorati' => 'technorati', 'Yahoo' => 'yahoo', 'Findexa' => 'findexa', 'NextLinks' => 'findlinks', 'Gais' => 'gaisbo', 'WiseNut' => 'zyborg', 'WhoisSource' => 'surveybot', 'Bloglines' => 'bloglines', 'BlogSearch' => 'blogsearch', 'PubSub' => 'pubsub', 'Syndic8' => 'syndic8', 'RadioUserland' => 'userland', 'Gigabot' => 'gigabot', 'Become.com' => 'become.com');
	if(@is_file(ABSPATH.'/wp-admin/upgrade-functions.php')) {
		include_once(ABSPATH.'/wp-admin/upgrade-functions.php');
	} elseif(@is_file(ABSPATH.'/wp-admin/includes/upgrade.php')) {
		include_once(ABSPATH.'/wp-admin/includes/upgrade.php');
	} else {
		die('We have problem finding your \'/wp-admin/upgrade-functions.php\' and \'/wp-admin/includes/upgrade.php\'');
	}
	$charset_collate = '';
	if($wpdb->supports_collation()) {
		if(!empty($wpdb->charset)) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if(!empty($wpdb->collate)) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}
	}
	// Drop UserOnline Table
	$wpdb->query("DROP TABLE IF EXISTS $wpdb->useronline");
	// Create UserOnline Table
	$create_table = "CREATE TABLE $wpdb->useronline (".
							" timestamp int(15) NOT NULL default '0',".
							" userid int(10) NOT NULL default '0',".
							" username varchar(20) NOT NULL default '',".
							" displayname varchar(255) NOT NULL default '',".
							" useragent varchar(255) NOT NULL default '',".
							" ip varchar(40) NOT NULL default '',".						 
							" location varchar(255) NOT NULL default '',".
							" url varchar(255) NOT NULL default '',".							
							" type enum('member','guest','bot') NOT NULL default 'guest',".
							" referral varchar(255) NOT NULL default '',".
							" UNIQUE KEY useronline_id (timestamp,username,ip,useragent)) $charset_collate;";
	maybe_create_table($wpdb->useronline, $create_table);
	// Add In Options
	add_option('useronline_most_users', 1, 'Most Users Ever Online Count');
	add_option('useronline_most_timestamp', current_time('timestamp'), 'Most Users Ever Online Date');
	add_option('useronline_timeout', 300, 'Timeout In Seconds');
	add_option('useronline_bots', $bots, 'Bots Name/Useragent');
	// Database Upgrade For WP-UserOnline 2.05
	add_option('useronline_url', site_url('useronline/'), 'UserOnline Page URL');
	// Database Upgrade For WP-UserOnline 2.20
	add_option('useronline_naming', array('user' => __('1 User', 'wp-useronline'), 'users' => __('%USERONLINE_COUNT% Users', 'wp-useronline'), 'member' => __('1 Member', 'wp-useronline'), 'members' => __('%USERONLINE_COUNT% Members', 'wp-useronline'), 'guest' => __('1 Guest', 'wp-useronline'), 'guests' => __('%USERONLINE_COUNT% Guests', 'wp-useronline'), 'bot' => __('1 Bot', 'wp-useronline'), 'bots' => __('%USERONLINE_COUNT% Bots', 'wp-useronline')),'Member(s), Guest(s) or Bot(s)');
	add_option('useronline_template_useronline', '<a href="%USERONLINE_PAGE_URL%" title="%USERONLINE_USERS%"><strong>%USERONLINE_USERS%</strong> '.__('Online', 'wp-useronline').'</a>', 'Useronline Template');
	add_option('useronline_template_browsingsite', array(__(',', 'wp-useronline').' ', __(',', 'wp-useronline').' ', __(',', 'wp-useronline').' ', _c('Users|Template Element', 'wp-useronline').': <strong>%USERONLINE_MEMBER_NAMES%%USERONLINE_GUESTS_SEPERATOR%%USERONLINE_GUESTS%%USERONLINE_BOTS_SEPERATOR%%USERONLINE_BOTS%</strong>'), 'User Browsing Site Template');
	add_option('useronline_template_browsingpage', array(__(',', 'wp-useronline').' ', __(',', 'wp-useronline').' ', __(',', 'wp-useronline').' ',  '<strong>%USERONLINE_USERS%</strong> '.__('Browsing This Page.', 'wp-useronline').'<br />'._c('Users|Template Element', 'wp-useronline').': <strong>%USERONLINE_MEMBER_NAMES%%USERONLINE_GUESTS_SEPERATOR%%USERONLINE_GUESTS%%USERONLINE_BOTS_SEPERATOR%%USERONLINE_BOTS%</strong>'), 'User Browsing Site Template');
}
?>