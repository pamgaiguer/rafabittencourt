<?php
/**
 * 
 *
 */
class jsEffects_Toggle {
    /**
     * @var array
     */
    private $toggleArray = array();

    /**
     * @var array
     */
    private $toggleConfig = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->toggleConfig['current']['gid'] = 0;
        $this->jsToggCongif = array();
    }

    public function initRegisterShortcode() {
        add_shortcode('toggles', array($this, '_processShortcode'));
		add_shortcode('toggle', array($this,'_processShortcode1'));		
    }

    public function initHooks() {
        add_filter('wp_footer', array($this, '_processFooter'));
    }

    public function _processFooter() {
		if(disablePlugin()){
			return;
		}		
        if ($this->toggleConfig['current']['gid']) {
            echo "<script type='text/javascript'>\n";
            echo "jQuery(document).ready(function($) {\n";

            for($i=0; $i<$this->toggleConfig['current']['gid']; $i++) {
                $opts = array(); 
                if(isset($this->jsToggCongif[$i+1]) && is_array($this->jsToggCongif[$i+1])) {
                    
                    foreach($this->jsToggCongif[$i+1] as $k=>$v) {
                        if($v != null) {

                            if(is_numeric($v) || 'true' == $v || 'false' == $v) {
                                array_push($opts, "$k:$v");
                            } else {
                                array_push($opts, "$k:'$v'");
                            }
                        }
                    }
                }
                
                echo '$("#thethe-toggle-'.($i+1).'").thetheToggle({'. implode(',', $opts) .'});' . "\r\n";
            }

            echo "});\n";
            echo '</script>';
        }
    }

    public function _processShortcode($atts, $content = null, $code = '') {
        if ($code == 'toggles') {            
	        $i = ++$this->toggleConfig['current']['gid'];
	        $this->jsToggCongif[$i] = array();
    	    $this->jsToggCongif[$i] = shortcode_atts(array(
        	    'speed'	=> $atts['speed'],
				'active'=> $atts['active'],
				'style'=> $atts['style']
	        ), $this->jsToggCongif[$i]);			
			
            $result = do_shortcode($content);
            return $this->buildHTMLToggle('thethe-toggle-'.$this->toggleConfig['current']['gid'], $atts['title']);			

        }
    }

    public function _processShortcode1($atts, $content = null, $code = '') {
        $atts = shortcode_atts(array(
            'gid' => 'thethe-toggle-'.$this->toggleConfig['current']['gid'],
            'title' => 'My Toggle Title'
        ), $atts);
        
        $this->addTogg($atts['gid'],$atts['title'],$content,'thethe-togle-content-'.rand(1, 10000));
    }
	
	/*
     * Function addTogg
     */
    protected function addTogg($gid, $title, $content, $id = null, $class = 'thethe-toggle-header', $order = 'default') {        

		$title = '<a href="#'.$id.'">'.$title.'</a>';
        $acc['title'] = '<h3 class="'.$class.'">'.$title.'</h3>';
        $acc['content']    = '<div id="'.$id.'" class="thethe-toggle-content">'.do_shortcode(trim($content)).'</div>';
        
        if($order != 'default') {
            $this->toggleArray[$gid][$order] = $acc;
        } else {
            $this->toggleArray[$gid][] = $acc;
        }

        return $this->toggleArray[$gid];
    } // end func addTogg

    public function buildHTMLToggle($id, $title = '', $class = 'thethe-toggle-group') {
        $output = ($title != '') ? '<h2 class="thethe-toggle-group-title">'.$title.'</h2>' : '';
        if (isset($this->toggleArray[$id])) {
            $output.= '<div id="'.$id.'" class="'.$class.'">';
            foreach($this->toggleArray[$id] as $acc) {
                $output.= $acc['title'];
                $output.= $acc['content'];
            }
            $output.= '</div>';
        }
        return $output;
    } // end func buildHTMLToggle
}