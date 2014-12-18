<?php

if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }

$root = __FILE__;
for ($i = 0; $i < 4; $i++) $root = dirname($root);
require_once($root . DS . 'wp-config.php');
require_once(ABSPATH . 'wp-admin' . DS . 'admin-functions.php');

class GalleryAjax extends GalleryPlugin {

	var $safecommands = array('slides_order');

	function GalleryAjax($cmd) {
		$this -> register_plugin('slideshow-gallery', __FILE__);
	
		if (!empty($cmd)) {		
			if (in_array($cmd, $this -> safecommands) || current_user_can('gallery_slides')) {			
				if (method_exists($this, $cmd)) {
					$this -> $cmd();
				}
			}
		}
	}
	
	function slides_order() {
		if (!empty($_REQUEST['item'])) {
			foreach ($_REQUEST['item'] as $order => $slide_id) {
				if (empty($_REQUEST['gallery_id'])) {
					$this -> Slide -> save_field('order', ($order + 1), array('id' => $slide_id));
				} else {
					$this -> GallerySlides -> save_field('order', ($order + 1), array('slide_id' => $slide_id, 'gallery_id' => $_REQUEST['gallery_id']));
				}
			}
			
			_e('Slides have been ordered', $this -> plugin_name);
		}
	}
}

$GalleryAjax = new GalleryAjax($_GET['cmd']);

?>