<?php
/**
 * 
 *
 */
class jsEffects_Tabs
{
    
    /**
     * @var array
     */
    private $tabArray = array();

    /**
     * @var array
     */
    private $tabConfig = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tabConfig['current']['gid'] = 0;
        $this->tabConfig['current']['tid'] = 0;
        $this->jsTabCongif = array();

    } // end func __construct


    public function initRegisterShortcode() {
        add_shortcode('tabs', array($this,'_processShortcode'));
        add_shortcode('tab', array($this,'_processShortcode1'));
    }

    public function initHooks() {
        add_filter('wp_footer',array($this,'_processFooter'));
    }

    public function _processFooter() {
		if(disablePlugin()){
			return;
		}		
        $html = '';
        if ($this->tabConfig['current']['gid']) {
            $html .= "<script type='text/javascript'>\n";
            $html .="jQuery(document).ready(function($) {\n";
            
            for ($i=0; $i<$this->tabConfig['current']['gid']; $i++) {
                $opts = array();
                if(isset($this->jsTabCongif[$i+1]) && is_array($this->jsTabCongif[$i+1])) {
                    foreach($this->jsTabCongif[$i+1] as $k=>$v) {
                        if($v != null) {

                            if(is_numeric($v) || 'true' == $v || 'false' == $v) {
                                array_push($opts, "$k: $v");
                            } else {
                                array_push($opts, "$k: '$v'");
                            }
                        }
                    }
                }
                
                $html .= '$("#thethe-tabs-'.($i+1).'").tabs({'. implode(',', $opts) .'});';
                $html .= "\n";
            }
            $html .= "});\n";
            $html .= '</script>';
        }

        echo $html;
    }

    public function _processShortcode($atts, $content = null, $code = '') {
        if ($code == 'tabs') {
            $i = ++$this->tabConfig['current']['gid'];
			$atts['active'] = ($atts['active'] ? ($atts['active'] - 1) : 0);
            $this->jsTabCongif[$i] = array();
            $this->jsTabCongif[$i] = shortcode_atts(array(
                'disabled'		=> $atts['disabled'],
                'collapsible'	=> $atts['collapsible'],
                'selected'		=> $atts['active'],
                'event'			=> $atts['event']				
            ), $this->jsTabCongif[$i]);
            
            $result = do_shortcode($content); 

            $class = (isset($atts['class']))?$atts['class']:null;
            $title = (isset($atts['title']))?$atts['title']:null;
            
            return $this->buildHTMLTabs('thethe-tabs-'.$this->tabConfig['current']['gid'], $class, $title);
        }

        return null;
    }

    /**
     * Build html tabs
     * @param string $id
     * @param string $class
     * @param string $title
     */
    public function buildHTMLTabs($id, $class = 'thethe-tabs-group', $title = '')
    {
        $output = ($title != '') ? '<h2 class="thethe-tabs-group-title">'.$title.'</h2>' : '';
        if (isset($this->tabArray[$id])) {
            $class = ($class == 'thethe-tabs-group') ? $class : $class . ' thethe-tabs-group';
            $output.= '<div id="'.$id.'" class="'.$class.'">';
            $output.= '<ul>';
            foreach($this->tabArray[$id] as $tab) {
                $output.= $tab['li'];
            }
            $output.= '</ul>';
            foreach($this->tabArray[$id] as $tab) {
                $output.= $tab['content'];
            }
            $output.= '</div>';
        }

        return $output;
    } // end func buildHTMLTabs

    public function _processShortcode1($atts, $content = null, $code = '') {
        ++$this->tabConfig['current']['tid'];
        
        $atts = shortcode_atts(array(
            'gid' => 'thethe-tabs-'.$this->tabConfig['current']['gid'],
            'title' => 'My Tab Title',
            'id' => 'thethe-tabs-'.$this->tabConfig['current']['gid'].'-'.$this->tabConfig['current']['tid']
        ), $atts);
        
        $this->addTab($atts['gid'], $atts['title'], $content, $atts['id']);
    } // end func


    /**
     * Function addTab
     *
     * @param string $gid
     * @param string $title
     * @param string $content
     * @param string $id
     * @param string $class
     * @param string $order
     */
    protected function addTab($gid, $title, $content, $id = null, $class = 'thethe-tab', $order = 'default') {
        $tab['li'] = '<li class="'.$class.'"><a href="#'.$id.'">'.$title.'</a></li>';
        $tab['content'] = '<div id="'.$id.'">'.do_shortcode(trim($content)).'</div>';
        
        if($order != 'default') {
            $this->tabArray[$gid][$order] = $tab;
        } else {
            $this->tabArray[$gid][] = $tab;
        }
        return $this->tabArray[$gid];
    } // end func addTab

}