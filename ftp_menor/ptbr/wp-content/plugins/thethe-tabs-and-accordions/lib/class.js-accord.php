<?php
/**
 * 
 *
 */
class jsEffects_Accord {

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
        $this->accordConfig['current']['id'] = 0;
        $this->jsAccCongif = array();
    } // end func __construct

    public function initRegisterShortcode() {
        add_shortcode('accordions', array($this,'_processShortcode'));
        add_shortcode('accordion', array($this,'_processShortcode1'));
    }

    public function initHooks() {
        add_filter('wp_footer',array($this,'_processFooter'));
    }

    public function _processFooter() {
		if(disablePlugin()){
			return;
		}		
        if ($this->accordConfig['current']['gid']) {
            echo "<script type='text/javascript'>\r\n";
            echo "jQuery(document).ready(function($) {\r\n";

            for($i=0; $i<$this->accordConfig['current']['gid']; $i++) {
                $opts = array(); 
                if (isset($this->jsAccCongif[$i+1]) && is_array($this->jsAccCongif[$i+1])) {
                    
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
                
                echo '$("#thethe-accord-'.($i+1).'").accordion({'. implode(',', $opts) .'});' . "\r\n";
            }

            echo "});\r\n";
            echo '</script>';
        }
    }

    public function _processShortcode($atts, $content = null, $code = '') {
        if ($code == 'accordions') {
            $i = ++$this->accordConfig['current']['gid'];
			$atts['active'] = ($atts['active'] ? ($atts['active'] - 1) : 0);
            $this->jsAccCongif[$i] = array();
            $this->jsAccCongif[$i] = shortcode_atts(array(
                'disabled'		=> $atts['disabled'],
                'active'		=> $atts['active'],
                'animated'		=> $atts['animated'],
                'autoHeight'	=> $atts['autoheight'],
                'clearStyle'	=> $atts['clearstyle'],
                'collapsible'	=> $atts['collapsible'],
                'fillSpace'		=> $atts['fillspace'],
                'event'			=> $atts['event']                
            ), $this->jsAccCongif[$i]);

            $result = do_shortcode($content);
            return $this->buildHTMLAccord('thethe-accord-'.$this->accordConfig['current']['gid'], $atts['title']);
        }
    }

    public function _processShortcode1($atts, $content = null, $code = '')
    {
    	if ($code == 'accordion') {
			$atts = shortcode_atts(array(
				'gid' => 'thethe-accord-'.$this->accordConfig['current']['gid'],
				'title' => 'My Accordion Title ',
				'anchor' => 'thethe-accordion-content-' . (++$this->accordConfig['current']['id'])
			), $atts);

			$this->addAcc($atts['gid'],$atts['title'],$content,$atts['anchor']);
    	}
    } // end func

    /**
     * Function addAcc
     *
     * @param string $gid
     * @param string $title
     * @param string $content
     * @param string $id
     * @param string $class
     * @param string $order
     */
    protected function addAcc($gid, $title, $content, $id = null, $class = 'thethe-accordion-header', $order = 'default') {        

        $acc['title'] = '<h3 class="'.$class.'"><a href="#'.$id.'">'.$title.'</a></h3>';
        $acc['content']    = '<div id="'.$id.'">'.do_shortcode(trim($content)).'</div>';
        
        if($order != 'default') {
            $this->accordArray[$gid][$order] = $acc;
        } else {
            $this->accordArray[$gid][] = $acc;
        }

        return $this->accordArray[$gid];
    } // end func addAcc

	// }}}
	// {{{ buildHTMLAccord

	/**
	 * Build html accordions
	 * @param string $id
	 * @param string $class
	 * @param string $title
	 */
	public function buildHTMLAccord($id, $title = '', $class = 'thethe-accord-group') {
        $output = ($title != '') ? '<h2 class="thethe-accord-group-title">'.$title.'</h2>' : '';
        if (isset($this->accordArray[$id])) {
            $output.= '<div id="'.$id.'" class="'.$class.'">';
            foreach($this->accordArray[$id] as $acc) {
                $output.= $acc['title'];
                $output.= $acc['content'];
            }
            $output.= '</div>';
        }
        return $output;
    } // end func buildHTMLAccord

} // end class 