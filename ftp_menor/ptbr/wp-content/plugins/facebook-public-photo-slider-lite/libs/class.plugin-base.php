<?php
require( ABSPATH . WPINC . '/pluggable.php' );

class WPFacebookPhotoBase{
	var $admin_js;
	var $admin_css;
	var $front_css;
	var $front_js;
	var $message;
    function __construct($config){    	
    	$this->admin_js = self::array_union($this->admin_js,array('jquery'));
    	$this->front_js = self::array_union($this->front_js,array('jquery'));
    	$this->front_css = self::array_union($this->front_css,array());
    	$this->admin_css = self::array_union($this->admin_css,array());
        $this->config = $config;//json_decode(file_get_contents($configfile));
        $this->plugin_folder = strtok(plugin_basename(__FILE__),'/');
        $this->plugin_path = WP_PLUGIN_DIR . '/'.$this->plugin_folder;
        $this->plugin_url  = WP_PLUGIN_URL . '/'.$this->plugin_folder;  
        $this->plugin_id = self::escapize($this->config->plugin_name);        
        $this->options = get_option($this->plugin_id);
        add_action('admin_menu', array(&$this, 'add_menu'));
        add_action('init',array(&$this, 'load_front_libraries'));
        register_activation_hook( $this->plugin_path . '/' . $this->config->plugin_base_file, array(&$this,'activate') );
        register_deactivation_hook( $this->plugin_path . '/' . $this->config->plugin_base_file, array(&$this,'deactivate') );
        $methods = get_class_methods($this);
        foreach($methods as $method){
        	$p = explode('__',$method);
        	$jackpot = array('filter','action','shortcode');
        	if(isset($p[1])&&in_array($p[1], $jackpot)){
        		$a = sprintf("add_%s", $p[1]);
        		$a($p[0], array(&$this, $method));
        	}
        }     
    }
    function add_menu(){
    	if(!isset($this->config->pages)) return;
    	if(isset($this->addon)){
    		foreach($this->config->pages  as $key=>$value){
    			$page = add_submenu_page($this->addon, $value->title,$value->title, 'add_users',
    					$this->plugin_id.'-'.$key, array(&$this, 'dispatch_page'));
    			add_action('admin_print_styles-' . $page, array(&$this, 'load_admin_libraries'));
    		}    	
    	}    	
    	$default_page = $this->config->default_page;    	
    	if($this->config->menu_type == "option_page"){    	
	    	$page = add_options_page($this->config->plugin_name, $this->config->plugin_name,
	    			'manage_options', $this->plugin_id.'-'.$default_page,
	    			array(&$this, 'dispatch_page'));
	    	add_action('admin_print_styles-' . $page, array(&$this, 'load_admin_libraries'));
    	}else if($this->config->menu_type == 'menu_page'){
    		$page = add_menu_page($this->config->plugin_name, $this->config->plugin_name,'add_users', 
    				$this->plugin_id.'-'.$default_page, array(&$this, 'dispatch_page'),$this->plugin_url.'/images/'.'menu_logo.png');    	
    		add_action('admin_print_styles-' . $page, array(&$this, 'load_admin_libraries'));
    		$default_page_title = $this->config->pages->{$default_page}->title;
    		$page = add_submenu_page($this->plugin_id.'-'.$default_page, $default_page_title, $default_page_title, 'add_users', 
    				$this->plugin_id.'-'.$default_page, array(&$this, 'dispatch_page'));
    		add_action('admin_print_styles-' . $page, array(&$this, 'load_admin_libraries'));
    		foreach($this->config->pages  as $key=>$value){
    			if($key == $default_page) continue;
    			$page = add_submenu_page($this->plugin_id.'-'.$default_page, $value->title,$value->title, 'add_users', 
    					$this->plugin_id.'-'.$key, array(&$this, 'dispatch_page'));
    			add_action('admin_print_styles-' . $page, array(&$this, 'load_admin_libraries'));
    		}
    	}
    }
    function dispatch_page(){
    	$screen = $this->get_current_screen();
    	echo '<h2>'.$this->config->plugin_name .' v'.$this->config->version .'</h2>';
    	$this->draw_tabs();
    	if(method_exists($this, $screen->page)){
    		$this->{$screen->page}();
    	}else{
    		$method = $screen->page. '_tab_'. $screen->tab;
    		if(method_exists($this, $method)){
    			$this->$method();
    		}else{
    			echo 'Define <b>'.$screen->page .'</b> or <b>'. $method . '</b>' . ' in <b>'. get_class($this).'</b> class.';
    		} 
    	}
    }
    function load_front_libraries(){
    	if(!is_admin()){
    		wp_enqueue_scripts(array('jquery'));
    		foreach($this->front_js as $js){
    			wp_enqueue_script($js, $this->plugin_url. '/js/'.$js.'.js');
    		}
    		foreach($this->front_css as $css){
    			wp_enqueue_style($css, $this->plugin_url. '/css/'.$css.'.css');
    		}    		
    	}
    }
    function load_admin_libraries(){
    	$pages  = array_keys((array)$this->config->pages);
    	$screen = $this->get_current_screen();
    	if(!in_array($screen->page, $pages)) return;
    	wp_enqueue_scripts(array('jquery'));
    	foreach($this->admin_js as $js){
    		wp_enqueue_script($js, $this->plugin_url. '/js/'.$js.'.js');
    	}
    	foreach($this->admin_css as $css){
    		wp_enqueue_style($css, $this->plugin_url. '/css/'.$css.'.css');
    	}    	
    }
    function set_options($new_options = array()){
    	foreach($new_options as $key=>$value)
    		$this->options[$key] = $value;
    	update_option($this->plugin_id , $this->options);
    }
    
    function setting_page(){
	    $screen = $this->get_current_screen();	   
	    $tab = $this->config->pages->setting_page->tabs[$screen->tab];	    
	    if(isset($_POST[$this->plugin_id.'_submit'])){
	    	$updated_options = $_POST[$this->plugin_id];
	    	$whole_new_options = array();
	    	foreach($tab->blocks as $key=>$block){
	    		foreach($block->fields as $field){
	    		    if (isset($field->name))
	    		        $name = $field->name;
	    		    else
	    			    $name = self::escapize($field->level);
	    			if(isset($updated_options[$key][$name]))
	    				$whole_new_options[$key][$name] = $updated_options[$key][$name];
	    			else
	    				$whole_new_options[$key][$name]  = '';
	    		}
	    	}	    	
	    	$this->set_options($whole_new_options);   
	    	if(empty($this->message))$this->message = 'Options updated!';
	    }	   
	    $options = $this->options;
	    include_once($this->plugin_path.'/views/settings.php');
    }
    function draw_tabs(){
    	$screen = $this->get_current_screen();
    	$current_page = $this->config->pages->{$screen->page};
    	if(!isset($current_page->tabs)) return;
    	$tabs = $this->config->pages->{$screen->page}->tabs;    	
    	echo '<h2 class="nav-tab-wrapper">';
    	foreach($tabs as $key=>$value){
    		$active = (($key== $screen->tab)?'nav-tab-active':'');
    		$url = '?page='. $screen->plugin.'-'.$screen->page.'&tab='.$key;
    		echo  '<a href="' . $url. '" class="nav-tab '. $active. '" >' .$value->title . '</a>';
    	}
    	echo '</h2>'; 
    }
    function activate(){
        $this->set_options(array('setting'=>$_POST['setting']));
    }
    function deactivate(){
    }
    function get_current_screen(){
    	$default_page = $this->config->default_page;
    	$page = explode('-',$_GET['page']);
    	$screen = new stdClass();
    	$screen->tab    = isset($_GET['tab'])?$_GET['tab'] : 0;
    	$screen->page   = isset($page[1])?$page[1] : $default_page;
    	$screen->menuid = 0;
    	$screen->plugin = $page[0];
    	return $screen;
    }
    static function escapize($str){
    	return str_replace(' ', '_', strtolower($str));
    }    
    static function array_union($a1,$a2){
    	if(empty($a1)) $a1 = array();
    	if(empty($a2)) $a2 = array();
    	foreach( $a2 as $a)array_push($a1, $a);
    	return array_unique($a1);
    }    
}
