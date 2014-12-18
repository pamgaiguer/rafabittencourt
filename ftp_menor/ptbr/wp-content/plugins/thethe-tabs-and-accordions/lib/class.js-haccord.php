<?php
/**
 * 
 *
 */
class jsEffects_HAccord {

    /**
     * @var array
     */
    private $accordArray = array();

    /**
     * @var array
     */
    private $accordConfig = array();

    /**
     * Constructor
     */
    public function __construct() {
        $this->accordConfig['current']['gid'] = 0;
        $this->jsAccCongif = array();
    } // end func __construct

    public function initRegisterShortcode() {
        add_shortcode('haccordions', array($this, '_processShortcode'));
        add_shortcode('haccordion', array($this, '_processShortcode1'));
    }

    public function initHooks() {
        add_filter('wp_footer', array($this, '_processFooter'));
    }

    public function _processFooter() {
		if(disablePlugin()){
			return;
		}		
        if ($this->accordConfig['current']['gid']) {
            echo "<script type='text/javascript'>\n";
            echo "jQuery(document).ready(function($) {\n";

            for($i=0; $i<$this->accordConfig['current']['gid']; $i++) {
                $opts = array(); 
                if(isset($this->jsAccCongif[$i+1]) && is_array($this->jsAccCongif[$i+1])) {
                    
                    foreach($this->jsAccCongif[$i+1] as $k=>$v) {
                        if($v != null) {
                            if(is_numeric($v) || 'true' == $v || 'false' == $v) {
                                array_push($opts, "$k:$v");
                            } else {
                                array_push($opts, "$k:'$v'");
                            }
                        }
                    }
                }

                echo '$("#thethe-haccord-'.($i+1).'").thetheHAccordion({'. implode(',', $opts) .'});' . "\r\n";
            }

            echo "});\n";
            echo '</script>';
        }
    }

    public function _processShortcode($atts, $content = null, $code = '') {
		if ($code == 'haccordions') {
    	    $i = ++$this->accordConfig['current']['gid'];

	        $this->jsAccCongif[$i] = array();
	        $this->jsAccCongif[$i] = shortcode_atts(array(
	            'slideSpeed'		=>$atts['speed'],
	            'containerWidth'	=>$atts['width'],
	            'containerHeight'	=>$atts['height'],
	            'headerWidth'		=>$atts['hwidth'],

	            'firstSlide'		=>$atts['active'],
				'autoPlay'			=>$atts['autoplay'],
				'cycleSpeed'		=>$atts['cyclespeed'],
				'pauseOnHover'		=>$atts['pauseonhover'],
				
				'enumerateSlides'	=>$atts['enumerate']
        	), $this->jsAccCongif[$i]);

    	    $result = do_shortcode($content);
	        return $this->buildHTMLHAccord('thethe-haccord-'.$this->accordConfig['current']['gid'], $atts['title']);
		}
    }

    public function _processShortcode1($atts, $content = null, $code = '') {
        $atts = shortcode_atts(array(
            'gid' => 'thethe-haccord-'.$this->accordConfig['current']['gid'],
            'title' => 'My HAcc Title'
        ), $atts);
        
        $this->accordArray[$atts['gid']][] = $this->addHAcc($atts['gid'], $atts['title'], $content);
    } // end func

    /**
     * Function addHAcc
     *
     * @param string $gid
     * @param string $title
     * @param string $content
     * @param string $id
     * @param string $class
     * @param string $order
     */
    protected function addHAcc($gid, $title, $content, $class = 'thethe-haccord-header', $order = 'default') {
        $acc['title'] = '<h3 class="'.$class.'"><span><a href="#">'.$title.'</a></span></h3>';
        $acc['content'] = '<div class="thethe-haccord-content"><div class="content-inner">'.do_shortcode(trim($content)).'</div></div>';

        return $acc;
    } // end func addTab

    /**
     * Build html accordions
     * @param string $id
     * @param string $class
     * @param string $title
     */
    public function buildHTMLHAccord($id, $title = '', $class = 'thethe-haccord-group') {
        $output = ($title != '') ? '<h2 class="thethe-haccord-group-title">'.$title.'</h2>' : '';
        
        if (isset($this->accordArray[$id])) {
			$class.=" ui-accordion ui-widget";
            $output.= '<div id="'.$id.'" class="'.$class.'"><ul class="thethe-haccord-sections">';
            
            foreach($this->accordArray[$id] as $acc) {
                $output .='<li class="thethe-haccord-section">'. $acc['title'] . $acc['content'] . '</li>';
            }
            
            $output.= '</ul></div>';
        }
        return $output;
    } // end func buildHTMLHAccord

}