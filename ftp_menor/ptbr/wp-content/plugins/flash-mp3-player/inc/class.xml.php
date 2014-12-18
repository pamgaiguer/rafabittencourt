<?php

/**
 * XML File Reader
 * @author Charles
 */
class XML_Conf_Reader {

    var $php_ver = 0;
    var $xml_ver = '1.0';
    var $encoding = 'utf-8';
    var $assoc_array = array();

    function XML_Conf_Reader($filename) {
        $this->__construct($filename);
    }

    function __construct($filename) {
        $this->php_ver = PHP_VERSION;
        if (version_compare(PHP_VERSION, '5.0.0') >= 0) {
            $this->read_xml_php5($filename);
        } else if (version_compare(PHP_VERSION, '4.3.0') >= 0 && function_exists('domxml_open_file')) {
            $this->read_xml_php4($filename);
        } else {
            $this->assoc_array = false;
        }
    }

    function read_xml_php4($file) {
        $xml = file_get_contents($file);
        $xml = domxml_open_mem($xml);
        $root_element = $xml->document_element();
        $this->assoc_array[$root_element->tagname()] = $this->traverse_xml_php4($root_element);
    }

    function traverse_xml_php4($root) {
        $root_array = array();
        if ($root->has_child_nodes()) {
            $childs = $root->child_nodes();
            $need_go_deep = false;
            $text_value = '';
            for ($i = 0, $l = count($childs); $i < $l; $i++) {
                $node = $childs[$i];
                if ($node->node_type() == XML_ELEMENT_NODE) {
                    $need_go_deep = true;
                    break;
                }
                if ($node->node_type() == XML_TEXT_NODE) {
                    if (trim($node->node_value()) != '') {
                        $text_value = trim($node->node_value());
                    }
                }
            }
            if (!$need_go_deep) {
                return $text_value;
            }
            $idx = 0;
            while ($idx < count($childs)) {
                $node = $childs[$idx];
                $type = $node->node_type();
                //echo $node->nodeName, ' node type is:', $type, '<br/>';
                if ($type == XML_ELEMENT_NODE) {
                    $root_array[$node->tagname()][] = $this->traverse_xml_php4($node);
                }
                $idx++;
            }
        }
        return $root_array;
    }

    function read_xml_php5($file) {
        $dom = new DOMDocument($this->xml_ver, $this->encoding);
        if (!$dom->load($file)) {
            $this->assoc_array = false;
            return;
        }
        $root_element = $dom->firstChild;
        $this->assoc_array[$root_element->nodeName] = $this->traverse_xml_php5($root_element);
    }

    function traverse_xml_php5($root) {
        $root_array = array();
        if ($root->hasChildNodes()) {
            //第一遍遍历，如果所有的节点都是text和comment，直接取值
            $childs = $root->childNodes;
            $need_go_deep = false;
            $text_value = '';
            for ($i = 0, $l = $childs->length; $i < $l; $i++) {
                $node = $childs->item($i);
                if ($node->nodeType == XML_ELEMENT_NODE) {
                    $need_go_deep = true;
                    break;
                }
                if ($node->nodeType == XML_TEXT_NODE) {
                    if (trim($node->nodeValue) !== '') {
                        $text_value = trim($node->nodeValue);
                    }
                }
            }
            if (!$need_go_deep) {
                return $text_value;
            }
            //第二遍遍历，
            $node = $root->firstChild;
            while ($node !== NULL) {
                $type = $node->nodeType;
                //echo $node->nodeName, ' node type is:', $type, '<br/>';
                if ($type == XML_ELEMENT_NODE) {
                    $root_array[$node->nodeName][] = $this->traverse_xml_php5($node);
                }
                $node = $node->nextSibling;
            }
        }
        return $root_array;
    }

}

/**
 * XML File Writer
 * @author Charles
 */
class XML_Conf_Writer {

    var $php_ver = 0;
    var $xml_ver = '1.0';
    var $encoding = 'utf-8';
    var $assoc_array = array();
    var $dom = '';

    function __construct($xml_array) {
        $this->assoc_array = $xml_array;
        $this->php_ver = PHP_VERSION;
    }

    function XML_Conf_Writer($xml_array) {
        $this->__construct($xml_array);
    }

    function save_xml($file) {
        if (version_compare(PHP_VERSION, '5.0.0') >= 0) {
            return $this->write_xml_php5($file);
        } else if (version_compare(PHP_VERSION, '4.3.0') >= 0 && function_exists('domxml_open_file')) {
            return $this->write_xml_php4($file);
        } else {
            return false;
        }
    }

    function write_xml_php5($file) {
        $this->dom = new DOMDocument($this->xml_ver, $this->encoding);
        foreach ($this->assoc_array as $key => $value) {
            $root = $this->dom->createElement($key);
            $this->create_insert_dom_php5($root, $value);
            $this->dom->appendChild($root);
        }
        $this->dom->formatOutput = true;
        $ret = $this->dom->save($file);
    }

    function create_insert_dom_php5($root, $nodearray) {
        if (!is_array($nodearray)) {
            $text = $this->dom->createTextNode($nodearray);
            $root->appendChild($text);
            return;
        }
        foreach ($nodearray as $key => $value) {
            $l = count($value);
            if ($l == 1 && !is_array($value[0])) {
                $node = $this->dom->createElement($key, $value[0]);
                $root->appendChild($node);
                continue;
            }
            for ($i = 0; $i < $l; $i++) {
                $node = $this->dom->createElement($key);
                $this->create_insert_dom_php5($node, $value[$i]);
                $root->appendChild($node);
            }
        }
    }

    function write_xml_php4($file) {
        $this->dom = domxml_new_doc($this->xml_ver);
        foreach ($this->assoc_array as $key => $value) {
            $root = $this->dom->create_element($key);
            $this->create_insert_dom_php4($root, $value);
            $this->dom->append_child($root);
        }
        $ret = $this->dom->dump_mem(true, $this->encoding);
        $fd = fopen($file, 'w');
        $ret = fwrite($fd, $ret);
        fclose($fd);
    }

    function create_insert_dom_php4($root, $nodearray) {
        if (!is_array($nodearray)) {
            $text = $this->dom->create_text_node($nodearray);
            $root->append_child($text);
            return;
        }
        foreach ($nodearray as $key => $value) {
            $l = count($value);
            if ($l == 1 && !is_array($value[0])) {
                $node = $this->dom->create_element($key);
                $text = $this->dom->create_text_node($value[0]);
                $node->append_child($text);
                $root->append_child($node);
                continue;
            }
            for ($i = 0; $i < $l; $i++) {
                $node = $this->dom->create_element($key);
                $this->create_insert_dom_php4($node, $value[$i]);
                $root->append_child($node);
            }
        }
    }

}

?>
