<?php

/**
* HTML_TreeMenu Modules Links Presenation class
*
* This class renders the tree nodes presented to it as an interface to inserting links into FCKEditor
*
* @author Tohir Solomons
*/
$this->loadClass('presentation', 'tree');
class modulelinkspresentation extends presentation
{

    /**
    * Constructor
    *
    * @param object $structure The menu structure
    * @param array  $options   Options whic affect the display of the listbox.
    *                          These can consist of:
    *                           o listtag          HTML Tag to use: Either UL or OL
    *                           o target           target of links
    *                           o insertLinkText   Text for Insert Link (Since this is a normal class, it does not have access to the language object
    */
    function modulelinkspresentation($structure, $options = array())
    {
        $this->presentation($structure);
        
        $this->listTag = 'ul';
        $this->target  = '_self';
        $this->insertLinkText = 'Insert Link';
        
        if (is_array($options)) {
            foreach ($options as $option => $value) {
                $this->$option = $value;
            }
        }
        
    }

    /**
    * Returns the HTML generated
    */
    function toHTML()
    {
        
      
        $nodeHTML = '<'.$this->listTag.'>';

        /**
        * Loop through subnodes
        */
        if (isset($this->menu->items)) {
            for ($i=0; $i<count($this->menu->items); $i++) {
                $nodeHTML .= $this->_nodeToHTML($this->menu->items[$i]);
            }
        }
        
        return $nodeHTML.'</'.$this->listTag.'>';
    }

    /**
    * Returns HTML for a single node
    *
    * @access private
    */
    function _nodeToHTML($node)
    {
        
        $cssArray = array();
        
        // Turn Off
        $this->startProcess = FALSE;
        
        $html ='<li>';
        
        if ($node->link) {
            $html .= '<a href="javascript:previewWindow(\''.$node->link.'\');">';
        }
        

        if (trim($node->text) == '') {
            $html .= '[[link]]';
        } else {
            $html .= $node->text;
        }
        
        if ($node->link) {
            $html .= '</a>';
            
            $html .= ' &nbsp; &nbsp; (<a href="javascript:insertWysiLink(\''.$node->link.'\');">'.$this->insertLinkText.'</a>)';
        }

        /**
        * Loop through subnodes
        */
        if ( isset($node->items) ) {
            
            $html .= '<'.$this->listTag.'>'; // commence tag for subnodes
            
            for ($i=0; $i<count($node->items); $i++) {
                $html .= $this->_nodeToHTML($node->items[$i]);
            }
            
            $html .= '</'.$this->listTag.'>'; // end tag for subnodes
        }

        return $html.'</li>';
    }
} // End class HTML_TreeMenu_Listbox

?>